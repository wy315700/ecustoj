#include <stdio.h>
#include <unistd.h>
#include <sys/types.h>
#include <sys/stat.h>

#include "database.h"
#include "config.h"
#include "judgecore.h"
#include "log.h"
#define LOCKFILE "/var/run/judged.pid"
#define LOCKMODE (S_IRUSR|S_IWUSR|S_IRGRP|S_IROTH)
//const int get_by_http = 1;
//const char * http_baseurl = "https://ecustoj.sinaapp.com/";
void init_daemon()
{
	int pid;
	int i;
	if(pid = fork())
		exit(0);//是父进程，结束父进程
	else 
		if(pid < 0)
			exit(1);//fork失败，退出

	//是第一子进程，后台继续执行
	setsid();// 第一子进程成为新的会话组长和进程组长

	//并与控制终端分离
	if(pid = fork())
		exit(0);// 是第一子进程，结束第一子进程
	else 
		if(pid< 0)
			exit(1);//fork失败，退出

	// 是第二子进程，继续

	//第二子进程不再是会话组长

	for(i=0;i< 64;++i)//关闭打开的文件描述符
		close(i);
	//chdir("/");//改变工作目录到 /tmp
	umask(0);//重设文件创建掩模
	return;

} 
int lockfile(int fd)
{
        struct flock fl;
        fl.l_type = F_WRLCK;
        fl.l_start = 0;
        fl.l_whence = SEEK_SET;
        fl.l_len = 0;
        return (fcntl(fd,F_SETLK,&fl));
}
int already_running(){
        int fd;
        char buf[16];
        fd = open(LOCKFILE, O_RDWR|O_CREAT, LOCKMODE);
        if (fd < 0){
				CLog::Log(CLog::LOG_ERROR,"can't open %s: %s\n", LOCKFILE, strerror(errno));
                exit(1);
        }
        if (lockfile(fd) < 0){
                if (errno == EACCES || errno == EAGAIN){
                        close(fd);
                        return 1;
                }
                CLog::Log(CLog::LOG_ERROR,"can't lock %s: %s\n", LOCKFILE, strerror(errno));
                exit(1);
        }
        ftruncate(fd, 0);
        sprintf(buf,"%d", getpid());
        write(fd,buf,strlen(buf)+1);
        return (0);
}

int main(int argc, char **argv) {

	init_daemon();
	CConfig::InitConfig("/etc/eoj/eoj.conf");
	
    CJudgeCore::Init();
    CLog::Init(CConfig::m_strLogPath);
	if(already_running())
	{
		CLog::Log(CLog::LOG_ERROR,"This daemon program is already running!\n");
		exit(1);
	}
    CDatabase::Connect(&CConfig::m_MysqlConn,CConfig::m_strDBHost.c_str(),CConfig::m_strDBUser.c_str(),
        CConfig::m_strDBPwd.c_str(),CConfig::m_strDBName.c_str(),CConfig::m_nDBPort);

    while(1) {
		usleep(1000*200);
        CJudgeCore::STC_SUBMIT stcSubmit;
        CJudgeCore::STC_PROINFO stcProInfo;
        if (!CJudgeCore::GetNextSubmit(stcSubmit)) {
            continue;
        }
        CJudgeCore::DelQueue(stcSubmit.nSubmitId);
        if (!CJudgeCore::GetProInfo(stcSubmit.nProId,stcProInfo))
            continue;
        if(stcSubmit.nLanguage == 4)
        {
            stcProInfo.nCaseTimeLimit *= 3;
            stcProInfo.nTimeLimit     *= 3;
        }
        if (!CJudgeCore::MakeFiles(stcSubmit))
            continue;
        //CLog::Log(CLog::LOG_ERROR,"here");
        if (!CJudgeCore::Compile(stcSubmit))
            continue;
        CJudgeCore::STC_RESULT stcResult,stcResultSingle;
        memset(&stcResult,0,sizeof(stcResult));
        int nCountPass = 0;
        CFileInfo::VEC_CASES vecCases;
        int nTotCases = CFileInfo::GetProCases(stcProInfo.nProId,vecCases);
        if (nTotCases == 0) {
            CJudgeCore::SetStatus(stcSubmit,CJudgeCore::OJ_WAIT,0,0);
            continue;
        }
        for(int i = 0; i < nTotCases; ++i) {
            memset(&stcResultSingle,0,sizeof(stcResultSingle));
            if (CJudgeCore::RunProcess(stcSubmit,stcProInfo,vecCases[i].strInFile,vecCases[i].strOutFile,stcResultSingle)) {
                if (stcResultSingle.nResult == CJudgeCore::OJ_AC) {
                    stcResult.nResult = CJudgeCore::OJ_AC;
                    stcResult.nTime += stcResultSingle.nTime;
                    stcResult.nMem = max(stcResult.nMem,stcResultSingle.nMem);
                    if (stcResult.nTime > stcProInfo.nTimeLimit) {
                        CJudgeCore::SetStatus(stcSubmit,CJudgeCore::OJ_TLE,stcResult.nTime,stcResult.nMem);
                        break;
                    }
                    nCountPass++;
                }
                else {
                    CJudgeCore::SetStatus(stcSubmit,stcResultSingle.nResult,stcResultSingle.nTime,stcResultSingle.nMem);
                    break;
                }
            }
            else {
                CLog::Log(CLog::LOG_ERROR,"Run error,SubmitId:%d\n",stcSubmit.nSubmitId);

                break;
            }
        }
        if (nCountPass == nTotCases) {
            CJudgeCore::SetStatus(stcSubmit,stcResult.nResult,stcResult.nTime,stcResult.nMem);
        }

    }
    return 0;
}
