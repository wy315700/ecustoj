#ifndef DATABASE_H
#define DATABASE_H



#include "mysql/mysql.h"
#include <stdio.h>

class CDatabase {
public :
    enum {
        MAX_QUERY_LEN = 65536,
    };
    class SQL_RST {
    public :
        SQL_RST(MYSQL_RES *res = NULL):m_MysqlRes(res) {};

        ~SQL_RST() {
            if (m_MysqlRes) {
                mysql_free_result(m_MysqlRes);
            }
        };
        SQL_RST& operator=(MYSQL_RES * res) {
            if (res == m_MysqlRes) {
                return *this;
            }
            if (m_MysqlRes) {
                mysql_free_result(m_MysqlRes);
            }
            m_MysqlRes = res;
            return *this;
        };
        operator MYSQL_RES*() {
            return m_MysqlRes;
        }
        SQL_RST(const SQL_RST &rst);
    private :
        MYSQL_RES *m_MysqlRes;

    };

static bool Connect(MYSQL *conn,const char *szHost,const char *szUser,const char *szPwd,const char *szDbName,int nPort);

static bool ExecQuery(MYSQL *conn,const char *szFmt,...);

static MYSQL_RES* GetResult(MYSQL *conn);

static void Close(MYSQL *conn);

private :
    CDatabase() {};
    ~CDatabase() {};
};


#endif
