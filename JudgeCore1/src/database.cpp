#include <stdarg.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>

#include "database.h"
#include "log.h"

bool CDatabase::Connect(MYSQL *conn,const char *szHost,const char *szUser,const char *szPwd,const char *szDbName,int nPort) {

    mysql_init(conn);
    if (!conn) {
        CLog::Log(CLog::LOG_ERROR,mysql_error(conn));
        return false;
    };
    if (!mysql_real_connect(conn,szHost,szUser,szPwd,szDbName,nPort,NULL,0)) {
        CLog::Log(CLog::LOG_ERROR,mysql_error(conn));
        return false;
    }
    return true;
}


bool CDatabase::ExecQuery(MYSQL *conn,const char *szFmt,...) {
    static char szQuery[MAX_QUERY_LEN + 10] = {0};
    unsigned int uLen = 0;
    va_list ap;
    va_start(ap,szFmt);
    vsprintf(szQuery,szFmt,ap);
    va_end(ap);
    if ((uLen = strlen(szQuery)) > MAX_QUERY_LEN) {
        CLog::Log(CLog::LOG_ERROR,"Sql query too long!");
        return false;
    }
    if (mysql_real_query(conn,szQuery,uLen)) {
        CLog::Log(CLog::LOG_ERROR,mysql_error(conn));
        return false;
    }
    return true;
}

MYSQL_RES* CDatabase::GetResult(MYSQL *conn) {
    return mysql_store_result(conn);
}

void CDatabase::Close(MYSQL *conn) {
    mysql_close(conn);
}
