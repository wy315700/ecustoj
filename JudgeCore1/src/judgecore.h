#ifndef JUDGECORE_H
#define JUDGECORE_H

#include <pthread.h>
#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <string.h>
#include <sys/time.h>
#include <sys/resource.h>
#include <signal.h>
#include <sys/wait.h>

#include "database.h"
#include "config.h"
#include "log.h"
#include "fileinfo.h"
#include "common.h"

#ifdef __i386
#define SYSCALL_NUM_TYPE int

#else
#define SYSCALL_NUM_TYPE unsigned long
#endif


class CJudgeCore {
public :
    enum {
        MAX_OUT_LIMIT   = 1024,
    };
    typedef enum _E_LANG_FLAG {
        E_C         = 1,
        E_CPLUSPLUS = 2,
        E_PASCAL    = 3,
        E_JAVA      = 4,
        E_PYTHON    = 5,
    }E_LANG_FLAG;

    typedef enum _E_STATUS_FLAG {
        //OJ结果代码
        OJ_WAIT       = 0, //OK
        OJ_AC         = 1, //OK
        OJ_PE         = 2, //OK
        OJ_TLE        = 3, //OK
        OJ_MLE        = 4, //OK
        OJ_WA         = 5, //OK
        OJ_OLE        = 6, //OK
        OJ_CE         = 7, //OK
        OJ_RE_SEGV    = 8, //OK
        OJ_RE_FPE     = 9, //OK
        OJ_RE_BUS     = 10,//OK
        OJ_RE_ABRT    = 11,//OK
        OJ_RE_UNKNOWN = 12,//OK
        OJ_RF         = 13,//OK
        OJ_SE         = 14,//System Error
        OJ_COMPILING  = 15,
    }E_STATUS_FLAG;

    typedef struct _STC_SUBMIT {
        int nSubmitId;
        int nUserId;
        int nProId;
        int nLanguage;
        int nType;
        //string strCode;
        char szCode[65536 + 10];
    }STC_SUBMIT;
    typedef struct _STC_PROINFO {
        int nProId;
        int nTimeLimit;
        int nMemLimit;
        int nCaseTimeLimit;
        int nSpj;
    }STC_PROINFO;
    typedef struct _STC_RESULT {
        int nResult;
        int nTime;
        int nMem;
    }STC_RESULT;
    static bool Init();
    static bool GetNextSubmit(STC_SUBMIT &stcSubmit);
    static bool _GetNextSubmitMysql(STC_SUBMIT &stcSubmit);
    static bool _GetNextSubmitHttp(STC_SUBMIT &stcSubmit);
    static bool Compile(const STC_SUBMIT &stcSubmit);
    static bool RunProcess(const STC_SUBMIT &stcSubmit, const STC_PROINFO &stcProInfo, const string &strInFile, const string &strOutFile, STC_RESULT &stcResult);
    static void PreWork(const STC_SUBMIT &stcSubmit);
    static bool GetProInfo(int nProId,STC_PROINFO &stcProInfo);
    static bool _GetProInfoMysql(int nProId,STC_PROINFO &stcProInfo);
    static bool _GetProInfoHttp(int nProId,STC_PROINFO &stcProInfo);
    static int Compare(const char *szFile1,const char *szFile2);
    static int SpecialJudge(int nProId, string strInFile, string strOutFile);
    static bool MakeFiles(const STC_SUBMIT &stcSubmit);
    static void SetCEInfo(const STC_SUBMIT &stcSubmit);
    static void _SetCEInfoMysql(const STC_SUBMIT &stcSubmit);
    static void _SetCEInfoHttp(const STC_SUBMIT &stcSubmit);
    static void SetStatus(const STC_SUBMIT &stcSubmit,int eStatus,int nTime,int nMem);
    static void _SetStatusMysql(const STC_SUBMIT &stcSubmit,int eStatus,int nTime,int nMem);
    static void _SetStatusHttp(const STC_SUBMIT &stcSubmit,int eStatus,int nTime,int nMem);
    static void UpdateSubmit(const STC_SUBMIT &stcSubmit,bool bStatus);
    static int HaveAC(int nUserId,int nProId);
    static void SetQueueFlag(int nSubmitId, int nFlag);
    static void DelQueue(int nSubmitId);
    static void _DelQueueMysql(int nSubmitId);
    static void _DelQueueHttp(int nSubmitId);
private :
    CJudgeCore() {};
    ~CJudgeCore() {};
    static void Init_RF_Table(int nLan);
    static void IORedirect(const string &strInfile);
    static void SetLimit(const STC_PROINFO &stcProInfo);
    static void SetTimer(int nTimeLimit);
    static bool CheckSafe(const string &strCode);
    static bool IsBlank(char cT);
	
    static bool IsValidSyscall(SYSCALL_NUM_TYPE  nNum);
private :
    static int in_syscall;
    //static pid_t m_pidParent;
    //static pid_t m_pidChild;

};



#endif
