#include <stdio.h>
#include <stdlib.h>
#include <fstream>
#include <iostream>
#include <stdarg.h>
using namespace std;
#include "log.h"


string CLog::m_strLogPath;

void CLog::Log(E_LOG_INDEX eIndex, const char *szFmt,...) {
    ofstream fout;
    string strFileName = m_strLogPath + "log";
    fout.open(strFileName.c_str(),ofstream::app);
    char szInfo[256] = {0};
    va_list ap;
    va_start(ap,szFmt);
    vsprintf(szInfo,szFmt,ap);
    va_end(ap);
    fout << szInfo << endl;
    fout.close();
    return;
}

void CLog::Init(const string &strPath) {
    m_strLogPath = strPath;
}

