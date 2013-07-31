#include <iostream>
#include <fstream>
#include <string>
#include <cstring>
#include <cstdio>
#include <cstdlib>
#include <ctime>
#include <algorithm>

#include <unistd.h>
#include <errno.h>
#include <sys/time.h>
#include <sys/signal.h>
#include <sys/resource.h>
#include <sys/types.h>
#include <sys/reg.h>
#include <sys/ptrace.h>
#include <sys/syscall.h>
#include <sys/user.h>
#include <sys/wait.h>
#include <sys/signal.h>

#include "judgecore.h"
#define BUFFER_SIZE 4096
const char * http_baseurl = "https://ecustoj.sinaapp.com";
const int get_by_http = 0;
using namespace std;
int RF_table[1024];
#ifdef __i386
//C or C++
int LANG_CV[256]={SYS_execve, SYS_read, SYS_uname, SYS_write, SYS_open, SYS_close, SYS_access, SYS_brk, SYS_munmap, SYS_mprotect, SYS_mmap2, SYS_fstat64, SYS_set_thread_area, SYS_exit_group, SYS_exit, -1};
int LANG_CC[256]={1,          -1,       -1,        -1,        -1,       -1,        -1,         -1,      -1,         -1,           -1,        -1,          -1,                  -1,             -1,       0};
//Pascal
int LANG_PV[256]={SYS_execve, SYS_open, SYS_set_thread_area, SYS_brk, SYS_read, SYS_uname, SYS_write, SYS_ioctl, SYS_readlink, SYS_mmap, SYS_rt_sigaction, SYS_getrlimit, SYS_exit_group, SYS_exit, SYS_ugetrlimit, -1};
int LANG_PC[256]={1,          -1,       -1,                  -1,      -1,       -1,        -1,        -1,        -1,           -1,       -1,               -1,            -1,             -1,       -1,             0};
//Java
//int LANG_JV[256]={SYS_execve, SYS_ugetrlimit, SYS_rt_sigprocmask, SYS_futex, SYS_read, SYS_mmap2, SYS_stat64, SYS_open, SYS_close, SYS_access, SYS_brk, SYS_readlink, SYS_munmap, SYS_close, SYS_uname, SYS_clone, SYS_uname, SYS_mprotect, SYS_rt_sigaction, SYS_sigprocmask, SYS_getrlimit, SYS_fstat64, SYS_getuid32, SYS_getgid32, SYS_geteuid32, SYS_getegid32, SYS_set_thread_area, SYS_set_tid_address, SYS_set_robust_list, SYS_exit_group, -1};
//int LANG_JC[256]={2,          -1,            -1,                 -1,        -1,        -1,        -1,         -1,       -1,        -1,         -1,      -1,           -1,         -1,        -1,        1,         -1,        -1,           -1,               -1,              -1,            -1,          -1,           -1,           -1,            -1,            -1,                  -1,                  -1,                  -1,              0};
int LANG_JV[256]={295,SYS_fcntl64,SYS_getdents64 , SYS_ugetrlimit, SYS_rt_sigprocmask, SYS_futex, SYS_read, SYS_mmap2, SYS_stat64, SYS_open, SYS_close, SYS_execve, SYS_access, SYS_brk, SYS_readlink, SYS_munmap, SYS_close, SYS_uname, SYS_clone, SYS_uname, SYS_mprotect, SYS_rt_sigaction, SYS_sigprocmask, SYS_getrlimit, SYS_fstat64, SYS_getuid32, SYS_getgid32, SYS_geteuid32, SYS_getegid32, SYS_set_thread_area, SYS_set_tid_address, SYS_set_robust_list, SYS_exit_group, 0};
int LANG_JC[256]={-1,-1,         -1,              -1,            -1,                 -1,        -1,       -1,        -1,         -1,       -1,        -1,          -1,         -1,      -1,           -1,         -1,        -1,        1,         -1,        -1,           -1,               -1,              -1,            -1,          -1,           -1,           -1,            -1,            -1,                  -1,                  -1,                  -1,              0};
#else
int LANG_JV[256]={61,22,6,33,8,13,16,111,110,39,79,SYS_fcntl,SYS_getdents64 , SYS_getrlimit, SYS_rt_sigprocmask, SYS_futex, SYS_read, SYS_mmap, SYS_stat, SYS_open, SYS_close, SYS_execve, SYS_access, SYS_brk, SYS_readlink, SYS_munmap, SYS_close, SYS_uname, SYS_clone, SYS_uname, SYS_mprotect, SYS_rt_sigaction, SYS_getrlimit, SYS_fstat, SYS_getuid, SYS_getgid, SYS_geteuid, SYS_getegid, SYS_set_thread_area, SYS_set_tid_address, SYS_set_robust_list, SYS_exit_group,158, -1};
int LANG_JC[256]={-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,         -1,              -1,            -1,                 -1,        -1,       -1,        -1,         -1,       -1,        -1,          -1,         -1,      -1,           -1,         -1,        -1,        1,         -1,        -1,           -1,                             -1,            -1,          -1,           -1,           -1,            -1,            -1,                  -1,                  -1,                  -1,-1,              0};

int LANG_CV[256]={SYS_time,SYS_read, SYS_uname, SYS_write, SYS_open, SYS_close, SYS_execve, SYS_access, SYS_brk, SYS_munmap, SYS_mprotect, SYS_mmap, SYS_fstat, SYS_set_thread_area, 252,SYS_arch_prctl,231,-1};
int LANG_CC[256]={1,-1,       -1,        -1,        -1,       -1,        -1,          -1,         -1,      -1,         -1,           -1,        -1,          -1,                  2,-1,-1,0};

int LANG_PV[256]={SYS_open, SYS_set_thread_area, SYS_brk, SYS_read, SYS_uname, SYS_write, SYS_execve, SYS_ioctl, SYS_readlink, SYS_mmap, SYS_rt_sigaction, SYS_getrlimit, 252,191,158,231,SYS_close,SYS_exit_group,SYS_munmap,SYS_time,4,-1};
int LANG_PC[256]={-1,       -1,                  -1,      -1,       -1,        -1,        1,          -1,        -1,           -1,       -1,               -1,            2,-1,-1,-1,-1,-1,-1,-1,-1,0};
#endif
#ifdef __i386
#define REG_SYSCALL orig_eax
#define REG_RET eax
#define REG_ARG0 ebx
#define REG_ARG1 ecx
#else
#define REG_SYSCALL orig_rax
#define REG_RET rax
#define REG_ARG0 rdi
#define REG_ARG1 rsi

#endif
int CJudgeCore::in_syscall;
FILE * read_cmd_output(const char * fmt, ...) {
        char cmd[BUFFER_SIZE];

        FILE *  ret =NULL;
        va_list ap;

        va_start(ap, fmt);
        vsprintf(cmd, fmt, ap);
        va_end(ap);
       // if(DEBUG) printf("%s\n",cmd);
        usleep(1000*200);
        ret = popen(cmd,"r");

        return ret;
}
bool CJudgeCore::Init() {
    //Init_RF_Table(); //初始化对应RF的syscall的表
    if(chdir(CConfig::m_strWorkPath.c_str()) < 0){
        //perror("chdir");
        //dp("chdir");
        return false;
    }
    return true;
}
void CJudgeCore::Init_RF_Table(int nLan) {
    int i;
    memset(RF_table, 0, sizeof(RF_table));
    int lang = nLan;
    if (lang == 1 || lang == 2){ // C & C++ 
        for (i = 0; LANG_CV[i]>-1; i++) { //64位下的SYS_read居然是0，所以用-1判断末尾
            RF_table[LANG_CV[i]]=LANG_CC[i];
        }
    }else if (lang == 3){ // Pascal
        for (i = 0; LANG_PV[i]>-1; i++) {
            RF_table[LANG_PV[i]]=LANG_PC[i];
        }
    }else if (lang == 4){ // Java
        for (i = 0; LANG_JV[i]>-1; i++) {
            RF_table[LANG_JV[i]]=LANG_JC[i];
        }
    }else{
        //dp("BAD lang");
    }
}
bool CJudgeCore::GetNextSubmit(STC_SUBMIT &stcSubmit) {
    if(get_by_http)
        _GetNextSubmitHttp(stcSubmit);
    else
        _GetNextSubmitMysql(stcSubmit);
}
bool CJudgeCore::_GetNextSubmitHttp(STC_SUBMIT &stcSubmit){
    memset(&stcSubmit,0,sizeof(stcSubmit));
    const char  * cmd=" wget --post-data=\"name=adminadminadminadmin&checkout=1\" --load-cookies=cookie --save-cookies=cookie --keep-session-cookies -q -O - \"%s/http/judge.php\"";
    int ret=0;
    FILE * fjobs=read_cmd_output(cmd,http_baseurl);
    if(fjobs == NULL)return false;
    fscanf(fjobs,"%d",&stcSubmit.nSubmitId);
    pclose(fjobs);
    if(stcSubmit.nSubmitId == -1)
        return false;

    cmd=" wget --post-data=\"name=adminadminadminadmin&submit_id=%d&getnextsubmitinfo=1\" --load-cookies=cookie --save-cookies=cookie --keep-session-cookies -q -O - \"%s/http/judge.php\"";
    fjobs=read_cmd_output(cmd,stcSubmit.nSubmitId,http_baseurl);
    if(fjobs == NULL)return false;
    fscanf(fjobs,"%d",&stcSubmit.nUserId);
    fscanf(fjobs,"%d",&stcSubmit.nProId);
    fscanf(fjobs,"%d",&stcSubmit.nLanguage);
    fscanf(fjobs,"%d",&stcSubmit.nType);
    pclose(fjobs);
    cmd=" wget --post-data=\"name=adminadminadminadmin&submit_id=%d&getnextsubmitcode=1\" --load-cookies=cookie --save-cookies=cookie --keep-session-cookies -q -O - \"%s/http/judge.php\"";
    fjobs=read_cmd_output(cmd,stcSubmit.nSubmitId,http_baseurl);
    if(fjobs == NULL)return false;
    char buffer[BUFFER_SIZE] = {0};
    while(fgets(buffer,BUFFER_SIZE,fjobs))
    {
        strcat(stcSubmit.szCode,buffer);
        memset(buffer,0,sizeof(buffer));
    }
    pclose(fjobs);
     if (strlen(stcSubmit.szCode) > 65536) {
            CLog::Log(CLog::LOG_ERROR,"code too long");
            return false;
        }

    return true;
}
bool CJudgeCore::_GetNextSubmitMysql(STC_SUBMIT &stcSubmit) {
    //lock the queue
    memset(&stcSubmit,0,sizeof(stcSubmit));
    if (!CDatabase::ExecQuery(&CConfig::m_MysqlConn,
        "select submit_id from judge_queue where status=0"))
        return false;
    CDatabase::SQL_RST result = CDatabase::GetResult(&CConfig::m_MysqlConn);
    MYSQL_ROW row;
    if (result == NULL)
        return false;
    if ((row = mysql_fetch_row(result))) {
        stcSubmit.nSubmitId = atoi(row[0]);
    }

    if (!CDatabase::ExecQuery(&CConfig::m_MysqlConn,
        "update judge_queue set status=1 where submit_id=%d",stcSubmit.nSubmitId))
        return false;

    if (!CDatabase::ExecQuery(&CConfig::m_MysqlConn,
        "select user_id,pro_id,language,type from submit_status where submit_id=%d",stcSubmit.nSubmitId))
        return false;
    result = CDatabase::GetResult(&CConfig::m_MysqlConn);
    if ((row = mysql_fetch_row(result))) {
        stcSubmit.nUserId = atoi(row[0]);
        stcSubmit.nProId = atoi(row[1]);
        stcSubmit.nLanguage = atoi(row[2]);
        stcSubmit.nType = atoi(row[3]);
        //stcSubmit.strCode = row[5];
    }

    if (!CDatabase::ExecQuery(&CConfig::m_MysqlConn,"set names utf8"))
        return false;
    if (!CDatabase::ExecQuery(&CConfig::m_MysqlConn,
        "select code_content from code_info where submit_id=%d",stcSubmit.nSubmitId))
        return false;
    result = CDatabase::GetResult(&CConfig::m_MysqlConn);
    if ((row = mysql_fetch_row(result))) {
        //CLog::Log(CLog::LOG_INFO,row[0]);
        sprintf(stcSubmit.szCode,"%s",row[0]);
        if (strlen(stcSubmit.szCode) > 65536) {
            CLog::Log(CLog::LOG_ERROR,"code too long");
            return false;
        }
        return true;
    }
    return false;
}
bool CJudgeCore::GetProInfo(int nProId,STC_PROINFO &stcProInfo) {
    if(get_by_http)
        _GetProInfoHttp(nProId,stcProInfo);
    else
        _GetProInfoMysql(nProId,stcProInfo);

}
bool CJudgeCore::_GetProInfoHttp(int nProId,STC_PROINFO &stcProInfo) {
    memset(&stcProInfo,0,sizeof(stcProInfo));
    const char  * cmd=" wget --post-data=\"name=adminadminadminadmin&getproinfo=1&pro_id=%d\" --load-cookies=cookie --save-cookies=cookie --keep-session-cookies -q -O - \"%s/http/judge.php\"";
    FILE * fjobs=read_cmd_output(cmd,nProId,http_baseurl);
    if(fjobs == NULL)return false;
    stcProInfo.nProId = nProId;
    fscanf(fjobs,"%d",&stcProInfo.nTimeLimit);
    fscanf(fjobs,"%d",&stcProInfo.nMemLimit);
    fscanf(fjobs,"%d",&stcProInfo.nCaseTimeLimit);
    fscanf(fjobs,"%d",&stcProInfo.nSpj);
    pclose(fjobs);
    return true;
}
bool CJudgeCore::_GetProInfoMysql(int nProId,STC_PROINFO &stcProInfo) {
    memset(&stcProInfo,0,sizeof(stcProInfo));

    if (!CDatabase::ExecQuery(&CConfig::m_MysqlConn,
        "select time_limit,mem_limit,case_time_limit,isspj from problems where pro_id=%d",nProId))
        return false;
    CDatabase::SQL_RST result = CDatabase::GetResult(&CConfig::m_MysqlConn);
    MYSQL_ROW row;
    if (result == NULL)
        return false;
    if ((row = mysql_fetch_row(result))) {
        stcProInfo.nProId = nProId;
        stcProInfo.nTimeLimit = atoi(row[0]);
        stcProInfo.nMemLimit = atoi(row[1]);
        stcProInfo.nCaseTimeLimit = atoi(row[2]);
        stcProInfo.nSpj = atoi(row[3]);
        return true;
    }
    return false;
}

bool CJudgeCore::Compile(const STC_SUBMIT &stcSubmit) {

    //DEBUG(2);
    SetStatus(stcSubmit,OJ_COMPILING,0,0);

    pid_t child = fork();
    if(child < 0){
        //创建新进程出错
        return false;
    }else if(child == 0){
        rlimit lim;
        //时间限制
        lim.rlim_max = 10;
        lim.rlim_cur = 10;
        if(setrlimit(RLIMIT_CPU, &lim) < 0){
            //perror("setrlimit");
            exit(1);
        }
        SetTimer(10000);
        char szTmp[256] = {0};
        sprintf(szTmp, "rm * %s",CConfig::m_strWorkPath.c_str());
        //system(szTmp);
        switch(stcSubmit.nLanguage) {
        case E_C:
                sprintf(szTmp,
                "gcc %d.c -o %d -ansi -fno-asm -Wall -lm -DONLINE_JUDGE --static  2> %d.ce",
                stcSubmit.nSubmitId,stcSubmit.nSubmitId,stcSubmit.nSubmitId);
                if (system(szTmp)) {
                    SetStatus(stcSubmit,OJ_CE,0,0);
                    SetCEInfo(stcSubmit);
                    UpdateSubmit(stcSubmit,false);
                    //return false;
                    exit(1);
                }
            break;
        case E_CPLUSPLUS:
                sprintf(szTmp,
                "g++ %d.cpp -o %d -ansi -fno-asm -Wall -lm -DONLINE_JUDGE --static  2> %d.ce",
                stcSubmit.nSubmitId,stcSubmit.nSubmitId,stcSubmit.nSubmitId);
                if (system(szTmp)) {
                    SetStatus(stcSubmit,OJ_CE,0,0);
                    SetCEInfo(stcSubmit);
                    UpdateSubmit(stcSubmit,false);
                    //return false;
                    exit(1);
                }
            break;
        case E_JAVA:
                sprintf(szTmp,
                "javac Main.java -J-Xms32m -J-Xmx256m  2> %d.ce",
                stcSubmit.nSubmitId,stcSubmit.nSubmitId,stcSubmit.nSubmitId);
                if (system(szTmp)) {
                    SetStatus(stcSubmit,OJ_CE,0,0);
                    SetCEInfo(stcSubmit);
                    UpdateSubmit(stcSubmit,false);
                    //return false;
                    exit(1);
                }
            break;
        default :;
        }
        exit(0);
    }
    else {
        int stat_val;
        while(true){
            if(wait4(child, &stat_val, 0, NULL) < 0){
                break;
            }
            if (WIFEXITED(stat_val)) {
                if (WEXITSTATUS(stat_val) == 0)
                    return true;
                else
                    return false;
            }
            else {

                if(WIFSIGNALED(stat_val)) {
                    int sig = WTERMSIG(stat_val);
                    //dp("sig = %d\n", sig);
                    switch(sig){
                        //超时, TLE
                        case SIGALRM:
                            SetStatus(stcSubmit,OJ_CE,0,0);
                            SetCEInfo(stcSubmit);
                            UpdateSubmit(stcSubmit,false);
                            while(waitpid(child,(int *)0,WNOHANG) == 0) {};
                            return false;
                    };
                }
                //break;
            }
        }
    }
    return false;
}
void CJudgeCore::IORedirect(const string &strInfile) {
    //重定向输入
    //cout << strInfile << endl;
    if(freopen(strInfile.c_str(), "r", stdin) == NULL){
        //perror("freopen(stdin)");
        //exit(EXIT_FREOPEN_IN);
    }

    //重定向输出
    if(freopen("stdout.txt", "w", stdout) == NULL){
        //perror("freopen(stdout)");
        //exit(EXIT_FREOPEN_OUT);
    }
    /*
    */
    //重定向错误输出
    if(freopen("stderr.txt", "w", stderr) == NULL){
        //perror("freopen(stderr)");
        //exit(EXIT_FREOPEN_ERR);
    }
}
void CJudgeCore::SetLimit(const STC_PROINFO &stcProInfo) {
    rlimit lim;
    //时间限制
    lim.rlim_max = (stcProInfo.nCaseTimeLimit + 999) / 1000 + 1; //秒，硬限制向上取整+1
    lim.rlim_cur = (stcProInfo.nCaseTimeLimit + 999) / 1000; //软限制向上取整，不加1

    if(setrlimit(RLIMIT_CPU, &lim) < 0){
        //perror("setrlimit");
        exit(EXIT_SETRLIMIT_TIME);
    }
    /*
    //内存限制
    //在这里进行内存限制可能导致MLE被判成RE
    //所以改成在每次wait以后判断
    lim.rlim_max = memlimit * 1024;
    lim.rlim_cur = memlimit * 1024;
    if(setrlimit(RLIMIT_AS, &lim) < 0){
        perror("setrlimit");
        exit(EXIT_SETRLIMIT_MEM);
    }
    */
    //堆栈空间限制
    lim.rlim_max = 4 * MEGA; // 4MB
    lim.rlim_cur = 4 * MEGA;
    if(setrlimit(RLIMIT_STACK, &lim) < 0){
        //perror("setrlimit");
        exit(EXIT_SETRLIMIT_STACK);
    }
    //输出文件大小限制
    lim.rlim_max = MAX_OUT_LIMIT * 1024;
    lim.rlim_cur = MAX_OUT_LIMIT * 1024;
    if(setrlimit(RLIMIT_FSIZE, &lim) < 0){
        //perror("setrlimit");
        exit(EXIT_SETRLIMIT_FSIZE);
    }
    //dp("cpu/mem/stack/fsize limit set ok.\n");
}
void CJudgeCore::SetTimer(int nTimeLimit) {
    struct itimerval now;
    now.it_interval.tv_sec = nTimeLimit / 1000;
    now.it_interval.tv_usec = nTimeLimit % 1000000 + 200000; //放宽100ms
    now.it_value.tv_sec = nTimeLimit / 1000;
    now.it_value.tv_usec = nTimeLimit % 1000000 + 200000;
    //VIRTUAL TIMER, 进程实际执行时间
    if(setitimer(ITIMER_VIRTUAL, &now, NULL) < 0){
        //perror("setitimer");
        //exit(EXIT_SETITIMER);
    }
    now.it_interval.tv_sec *= 2;
    now.it_value.tv_sec *= 2;
    //REAL TIMER, 系统真实时间(以免sleep卡死)
    if(setitimer(ITIMER_REAL, &now, NULL) < 0){
        //perror("setitimer");
        //exit(EXIT_SETITIMER);
    }
    //dp("setitimer ok.\n");
}

int CJudgeCore::Compare(const char *szFile1,const char *szFile2) {
    CFileReader a(szFile1), b(szFile2);
    int ac = 1;
    char t1, t2;
    while(true){
        t1 = a.next();
        t2 = b.next();
        if(t1 == -1 || t2 == -1) break;
        if(ac == 1){
            if(t1 == t2){
                continue;
            }else /* t1 != t2 */ {
                if(IsBlank(t1) || IsBlank(t2)){
                    ac = 0;
                    a.ret();
                    b.ret();
                    continue;
                }else{
                    return OJ_WA;
                }
            }
        }else{
            while(t1 != -1 && IsBlank(t1)) t1 = a.next();
            while(t2 != -1 && IsBlank(t2)) t2 = b.next();
            if(t1 == -1 || t2 == -1) break;
            if(t1 != t2){
                return OJ_WA;
            }
        }
    }
    if(t1 != -1 || t2 != -1) ac = 0;
    while(t1 != -1 && IsBlank(t1)) t1 = a.next();
    while(t2 != -1 && IsBlank(t2)) t2 = b.next();

    if(t1 == -1 && t2 == -1){
        if(ac == 1){
            return OJ_AC;
        }else{
            return OJ_PE;
        }
    }else{
        return OJ_WA;
    }
    return OJ_WA;
}
bool CJudgeCore::IsBlank(char cT) {
    return
      (cT == ' '  ||
       cT == '\t' ||
       cT == '\r' ||
       cT == '\n');
}
bool CJudgeCore::IsValidSyscall(SYSCALL_NUM_TYPE nNum) {
    //static int in_syscall = 0;
    in_syscall = 1 - in_syscall;
    //dp("%d (%s)\n", num, in_syscall? "in" : "out");

    if(RF_table[nNum] == 0) {
        return false;
    } else{
        if(in_syscall == 0){
            RF_table[nNum]--;
        }
    }
    return true;
}

int CJudgeCore::SpecialJudge(int nProId, string strInFile, string strOutFile) {
    //return 0;
    char szTmp[64] = {0};
    sprintf(szTmp,"%d",nProId);
    string strProId = szTmp;
    string cmdline = CConfig::m_strDataPath + strProId + "/spj " + strInFile  + " " + strOutFile + " stdout.txt";
    //dp("SPJ: %s\n", cmdline.c_str());
    FILE *fp = popen(cmdline.c_str(), "r");
    if(fp == NULL){
        //perror("popen");
        CLog::Log(CLog::LOG_ERROR,"Open Spj error,ProId:%d\n",nProId);
        //exit(EXIT_SPJ_POPEN);
        return -1;
    }
    //signal(SIGALRM, popen_timeout);
    //signal(SIGVTALRM, popen_timeout);
    //SetTimer(5000);

    int res;
    if(fscanf(fp, "%d", &res) != 1){
        fprintf(stderr, "fscanf: no output produced from spj\n");
        CLog::Log(CLog::LOG_ERROR,"No output produced from spj,ProId:%d\n",nProId);
        return -1;
    }
    //dp("SPJ res: %d\n", res);

    if(pclose(fp) < 0){
        //perror("pclose");
        CLog::Log(CLog::LOG_ERROR,"Pclose error,ProId:%d\n",nProId);
        //exit(EXIT_SPJ_PCLOSE);
    }
    switch(res){
        case 1:
            return OJ_AC;
        case 2:
            return OJ_PE;
        case 4:
            return OJ_WA;
        default:;
            //dp("SPJ BAD RETURN VALUE: %d\n", res);
            //exit(EXIT_SPJ_UNKNOWN);
    }
    return -1;
}
bool CJudgeCore::RunProcess(const STC_SUBMIT &stcSubmit, const STC_PROINFO &stcProInfo, const string &strInFile, const string &strOutFile, STC_RESULT &stcResult) {
    char szTmp[32] = {0};
    sprintf(szTmp,"%d",stcSubmit.nSubmitId);
    string strTmp = szTmp;
    string strExecutive = CConfig::m_strWorkPath + strTmp;
    int result = 0;

    Init_RF_Table(stcSubmit.nLanguage);
    in_syscall = 0;
    //单文件
    //string strInFile = CConfig::m_strDataPath + szTmp + "/data.in";
    //string strOutFile = CConfig::m_strDataPath + szTmp + "/data.out";
    pid_t child = fork();
    if(child < 0){
        //创建新进程出错
        return false;
    }else if(child == 0){
        //子进程
        //CLog::Log(CLog::LOG_ERROR,"ad");
        //cout << "ad " << endl;
        IORedirect(strInFile); //重定向输入/输出/错误
        //cout << "ad " << endl;
        SetLimit(stcProInfo); //设置CPU/MEM/STACK/FSIZE的限制
        SetTimer(stcProInfo.nCaseTimeLimit); //设置定时器

        if(ptrace(PTRACE_TRACEME, 0, NULL, NULL) < 0){
            return false;
        }

        //载入程序
        if(stcSubmit.nLanguage != E_JAVA)
            execl(strExecutive.c_str(), NULL, NULL);
        else
            execl("/usr/bin/java", "/usr/bin/java", "-Xms32m","-Xmx256m",
                    "-Djava.security.manager",
                        "-Djava.security.policy=./java.policy", "Main", (char *)NULL);
        //载入出错
    }else{
        //父进程
        int status = 0;
        long memuse = 0;
        //int orig_eax = 0;
        struct rusage rused;
        struct user_regs_struct regs;

        while(true){
            if(wait4(child, &status, 0, &rused) < 0){
                return false;
            }
            //正常退出
            if(WIFEXITED(status)){
                //dp("AC or PE or WA\n");
                if(!stcProInfo.nSpj){
                    //cout << strOutFile << endl;
                    result = Compare(strOutFile.c_str(), "stdout.txt");
                }else{
                    result = SpecialJudge(stcProInfo.nProId,strInFile,strOutFile);
                    if (result == -1)
                        result = OJ_SE;
                }
                break;
            }
            //判RF
            if(WIFSIGNALED(status)){
                int sig = WTERMSIG(status);
                //dp("sig = %d\n", sig);
                switch(sig){
                    //超时, TLE
                    case SIGALRM:
                    case SIGXCPU:
                    case SIGKILL:
                        //cout << "TLE" << endl;
                        result = OJ_TLE;
                        break;
                    //输出过多，OLE
                    case SIGXFSZ:
                        //dp("OLE\n");
                        result = OJ_OLE;
                        break;
                    //RE的各种情况
                    case SIGSEGV:
                        result = OJ_RE_SEGV;
                        break;
                    case SIGFPE:
                        result = OJ_RE_FPE;
                        break;
                    case SIGBUS:
                        result = OJ_RE_BUS;
                        break;
                    case SIGABRT:
                        result = OJ_RE_ABRT;
                        break;
                    default:
                        result = OJ_RE_UNKNOWN;
                        break;
                }
                kill(child, SIGKILL);
                break; //退出循环
            }

            if(WEXITSTATUS(status) != 5){
                //dp("EXITCODE = %d\n", WEXITSTATUS(status));
                switch(WEXITSTATUS(status)){
                    //超时, TLE
                    case SIGALRM:
                    case SIGXCPU:
                    case SIGKILL:
                        //dp("TLE\n");
                        result = OJ_TLE;
                        break;
                    //输出过多，OLE
                    case SIGXFSZ:
                        //dp("OLE\n");
                        result = OJ_OLE;
                        break;
                    //RE的各种情况
                    case SIGSEGV:
                        result = OJ_RE_SEGV;
                        break;
                    case SIGFPE:
                        result = OJ_RE_FPE;
                        break;
                    case SIGBUS:
                        result = OJ_RE_BUS;
                        break;
                    case SIGABRT:
                        result = OJ_RE_ABRT;
                        break;
                    default:
                        result = OJ_RE_UNKNOWN;
                        break;
                }
                kill(child, SIGKILL);
                break; //退出循环
            }

            memuse = max(memuse, rused.ru_minflt * (getpagesize() / 1024));
            //内存使用超过限制 MLE
            if(memuse > stcProInfo.nMemLimit){
                //dp("MLE(%dKB)\n", memuse);
                result = OJ_MLE;
                kill(child, SIGKILL);
                break;
            }

            /**/
            //截获SYSCALL并进行检查
            if(ptrace(PTRACE_GETREGS, child, NULL, &regs) < 0){
                perror("ptrace(PTRACE_GETREGS)");
                //exit(EXIT_PTRACE_GETREGS);
            }
		//	CLog::Log(CLog::LOG_ERROR,"syscall : %d",regs.REG_SYSCALL);
            //禁止的系统调用, RF
            if(regs.REG_SYSCALL >= 0 && !IsValidSyscall(regs.REG_SYSCALL)){
                //dp("RF (SYSCALL = %d)\n", regs.orig_eax);
                result = OJ_RF;
                kill(child, SIGKILL);
                break;
            }

            //继续运行
            if(ptrace(PTRACE_SYSCALL, child, NULL, NULL) < 0){
                perror("ptrace(PTRACE_SYSCALL)");
                //exit(EXIT_PTRACE_SYSCALL);
            }
        }

    //子进程结束, 统计资源使用, 返回结果
        int timeuse = (rused.ru_utime.tv_sec * 1000 +
                       rused.ru_utime.tv_usec / 1000);
        //dp("[child_ends]\n");
        //SetStatus(stcSubmit,(E_STATUS_FLAG)result,timeuse,memuse);
        stcResult.nResult = result;
        stcResult.nTime = timeuse;
        stcResult.nMem = memuse;

        while(waitpid(child,(int *)0,WNOHANG) == 0) {};
    }
    return true;
}
void CJudgeCore::SetStatus(const STC_SUBMIT &stcSubmit,int eStatus,int nTime,int nMem) {
    if(get_by_http)
        _SetStatusHttp(stcSubmit,eStatus,nTime,nMem);
    else
        _SetStatusMysql(stcSubmit,eStatus,nTime,nMem);

}
void CJudgeCore::_SetStatusHttp(const STC_SUBMIT &stcSubmit,int eStatus,int nTime,int nMem) {

    const char  * cmd="wget --post-data=\"name=adminadminadminadmin&returnstatus=1&user_id=%d&pro_id=%d&submit_id=%d&result=%d&cost_time=%d&cost_mem=%d\" --load-cookies=cookie --save-cookies=cookie --keep-session-cookies -q -O - \"%s/http/judge.php\"";
    FILE * fjobs = NULL;
    int result = -1;
    while(fjobs = read_cmd_output(cmd,stcSubmit.nUserId,stcSubmit.nProId,stcSubmit.nSubmitId,eStatus,nTime,nMem,http_baseurl))
    {
        if(fjobs == NULL) 
        {
            pclose(fjobs);
            fjobs = NULL;
            continue;
        }
        fscanf(fjobs,"%d",&result);
        if(result != 1 && result != 0)
        {
            pclose(fjobs);
            fjobs = NULL;
            continue;
        }
        else
{
pclose(fjobs);
            break;
}
    }
}
void CJudgeCore::_SetStatusMysql(const STC_SUBMIT &stcSubmit,int eStatus,int nTime,int nMem) {
    CDatabase::ExecQuery(&CConfig::m_MysqlConn,
        "update submit_status set result=%d,cost_time=%d,cost_mem=%d where submit_id=%d",
        eStatus,nTime,nMem,stcSubmit.nSubmitId);
    if (eStatus == OJ_AC) {
        UpdateSubmit(stcSubmit,true);
    }
}

bool CJudgeCore::MakeFiles(const STC_SUBMIT &stcSubmit) {
    ofstream fout;
    char szFileName[32] = {0};
    if (stcSubmit.nLanguage == E_C) {
        sprintf(szFileName,"%d.c",stcSubmit.nSubmitId);
    }
    else if (stcSubmit.nLanguage == E_CPLUSPLUS) {
        sprintf(szFileName,"%d.cpp",stcSubmit.nSubmitId);
    }
    else if (stcSubmit.nLanguage == E_JAVA){
        sprintf(szFileName,"Main.java");
    }
    else {
        return false;
    }

    fout.open(szFileName);
    fout << stcSubmit.szCode << endl;
    fout.close();
    return true;
}

void CJudgeCore::SetCEInfo(const STC_SUBMIT &stcSubmit) {
    if(get_by_http)
        _SetCEInfoHttp(stcSubmit);
    else
        _SetCEInfoMysql(stcSubmit);
}
void CJudgeCore::_SetCEInfoHttp(const STC_SUBMIT &stcSubmit) {
    const char  * cmd=" wget --post-data=\"name=adminadminadminadmin&setceinfo=1&submit_id=%d&ce_info=%s\" --load-cookies=cookie --save-cookies=cookie --keep-session-cookies -q -O - \"%s/http/judge.php\"";
    string strCEInfo;
    char szTmp[256] = {0};
    sprintf(szTmp,"%s%d.ce",CConfig::m_strWorkPath.c_str(),stcSubmit.nSubmitId);
    string strFileName = szTmp;
    CFileInfo::GetFileContent(strFileName.c_str(),strCEInfo);
    char szCEInfo[40960 + 10] = {0};
    //sprintf(szCEInfo,"%s",strCEInfo.c_str());
    mysql_real_escape_string(&CConfig::m_MysqlConn,szCEInfo,strCEInfo.c_str(),strCEInfo.size());
    if (strlen(szCEInfo) > 40960)
        return;
    FILE * fjobs=read_cmd_output(cmd,stcSubmit.nSubmitId,szCEInfo,http_baseurl); 
    pclose(fjobs);   
}
void CJudgeCore::_SetCEInfoMysql(const STC_SUBMIT &stcSubmit) {
    string strCEInfo;
    char szTmp[256] = {0};
    sprintf(szTmp,"%s%d.ce",CConfig::m_strWorkPath.c_str(),stcSubmit.nSubmitId);
    string strFileName = szTmp;
    CFileInfo::GetFileContent(strFileName.c_str(),strCEInfo);
    char szCEInfo[40960 + 10] = {0};
    //sprintf(szCEInfo,"%s",strCEInfo.c_str());
    mysql_real_escape_string(&CConfig::m_MysqlConn,szCEInfo,strCEInfo.c_str(),strCEInfo.size());
    if (strlen(szCEInfo) > 40960)
        return;
    CDatabase::ExecQuery(&CConfig::m_MysqlConn,
        "insert into ce_info (submit_id,ce_info) values(%d,'%s')",
        stcSubmit.nSubmitId,szCEInfo);
}

void CJudgeCore::UpdateSubmit(const STC_SUBMIT &stcSubmit,bool bStatus) {
    if (bStatus) {
        CDatabase::ExecQuery(&CConfig::m_MysqlConn,
            "update problems set accepted=accepted+1 where pro_id=%d",
            stcSubmit.nProId);
        if (HaveAC(stcSubmit.nUserId,stcSubmit.nProId) != 1) {
            CDatabase::ExecQuery(&CConfig::m_MysqlConn,
                "update users set solved=solved+1 where user_id=%d",
                stcSubmit.nUserId);
            CDatabase::ExecQuery(&CConfig::m_MysqlConn,
                "update problems set solved_users=solved_users+1 where pro_id=%d",
                stcSubmit.nProId);
            CDatabase::ExecQuery(&CConfig::m_MysqlConn,
                "update user_submit set result=1 where pro_id=%d and user_id=%d",
                stcSubmit.nProId,stcSubmit.nUserId);
        }
    } else {

    }
}

int CJudgeCore::HaveAC(int nUserId,int nProId) {
    if (!CDatabase::ExecQuery(&CConfig::m_MysqlConn,
        "select result from user_submit where user_id=%d and pro_id=%d",
        nUserId,nProId)) {
            return -1;
        }
    CDatabase::SQL_RST result = CDatabase::GetResult(&CConfig::m_MysqlConn);
    if (result == NULL)
        return -1;
    if (MYSQL_ROW row = mysql_fetch_row(result)) {
        int nResult = atoi(row[0]);
        if (nResult == 1)
            return 1;
        else
            return 0;
    }
    return -1;
}
void CJudgeCore::SetQueueFlag(int nSubmitId, int nFlag) {
    CDatabase::ExecQuery(&CConfig::m_MysqlConn,
        "update judge_queue set status=%d where submit_id=%d",
        nFlag,nSubmitId);
}

void CJudgeCore::DelQueue(int nSubmitId) {
    if(get_by_http)
        _DelQueueHttp(nSubmitId);
    else
        _DelQueueMysql(nSubmitId);

}
void CJudgeCore::_DelQueueMysql(int nSubmitId) {
    CDatabase::ExecQuery(&CConfig::m_MysqlConn,
        "delete from judge_queue where submit_id=%d",
        nSubmitId);
}
void CJudgeCore::_DelQueueHttp(int nSubmitId) {
    const char  * cmd=" wget --post-data=\"name=adminadminadminadmin&deletequeue=1&submit_id=%d\" --load-cookies=cookie --save-cookies=cookie --keep-session-cookies -q -O - \"%s/http/judge.php\"";
    FILE * fjobs=read_cmd_output(cmd,nSubmitId,http_baseurl); 
    pclose(fjobs);   
}
