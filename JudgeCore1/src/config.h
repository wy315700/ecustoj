#ifndef CONFIG_H
#define CONFIG_H

#include <string>
#include "mysql/mysql.h"
using namespace std;

class CConfig {
public :
    static bool InitConfig(const char *szFile);
	static void read_config(string buf,const string key,string &value);
	static void read_config(string buf,const string key,int &value);
public :
    static string m_strLogPath;

    static MYSQL m_MysqlConn;
    static string m_strDBHost;
    static string m_strDBUser;
    static string m_strDBPwd;
    static string m_strDBName;
    static int m_nDBPort;

    static string m_strDataPath;
    static string m_strWorkPath;
	
private :
    CConfig() {};
    ~CConfig() {};
};

#endif
