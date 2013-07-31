#ifndef LOG_H
#define LOG_H

#include <stdio.h>
#include <stdarg.h>
#include <string>
using namespace std;

/*
void dp(const char *fmt, ...){
//#ifdef DEBUG
    va_list args;
    va_start(args, fmt);
    vfprintf(stderr, fmt, args);
    va_end(args);
//#endif
}
*/
class CLog {
public :
    typedef enum _E_LOG_INDEX {
        LOG_ERROR       = 1,
        LOG_WARN        = 2,
        LOG_INFO        = 3,
    }E_LOG_INDEX;
    static void Log(E_LOG_INDEX eIndex, const char *szFmt,...);
    static void Init(const string &strPath);
private :
    CLog() {};
    ~CLog() {};

private :
    static string m_strLogPath;

};

#endif
