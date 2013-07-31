#include <stdio.h>

#include<stdlib.h>
#include "config.h"
#include <iostream>
#include <fstream>
#include <sstream>
#include <string.h>
#include <errno.h>
using namespace std;
string CConfig::m_strLogPath;
MYSQL  CConfig::m_MysqlConn;
string CConfig::m_strDBHost;
string CConfig::m_strDBUser;
string CConfig::m_strDBPwd;
string CConfig::m_strDBName;
int CConfig::m_nDBPort;
string CConfig::m_strDataPath;
string CConfig::m_strWorkPath;
void CConfig::read_config(string buf,const string key,string &value)
{
	if(buf.compare(0,key.length(),key)==0)
	{
		int equal=buf.find('=',0);
		if(equal==buf.npos)return;
		buf=buf.substr(equal+1,buf.length()-equal);
		istringstream is(buf,istringstream::in);
		is>>value;
	}
}
void CConfig::read_config(string buf,const string key,int &value)
{
	if(buf.compare(0,key.length(),key)==0)
	{
		int equal=buf.find('=',0);
		if(equal==buf.npos)return;
		buf=buf.substr(equal+1,buf.length()-equal);
		istringstream is(buf,istringstream::in);
		is>>value;
	}
}

bool CConfig::InitConfig(const char *szFile) {
	ifstream fin;
	string buf;
	char *i,*j;


	 m_strLogPath = "/var/onlinejudge/log/";
    //m_MysqlConn = NULL;
    m_strDBHost = "127.0.0.1";//"localhost";
    m_strDBUser = "onlinejudge";
    m_strDBPwd = "root";
    m_strDBName = "onlinejudge";
    m_nDBPort = 3306;
    m_strDataPath = "/var/onlinejudge/data/";
    m_strWorkPath = "/var/onlinejudge/work/";
	//如果有配置文件，则从配置文件里读，否则按照默认的

	fin.open(szFile);
	if (fin.fail())
	{
		perror("input file opening failed\n");
		exit(1);
	}
	while(getline(fin,buf))
	{
		if(buf[0]=='#')continue;
		read_config(buf,"logpath",m_strLogPath);
		read_config(buf,"dbhost",m_strDBHost);
		read_config(buf,"dbuser",m_strDBUser);
		read_config(buf,"dbname",m_strDBName);
		read_config(buf,"dbpwd",m_strDBPwd);
		read_config(buf,"datapath",m_strDataPath);
		read_config(buf,"workpath",m_strWorkPath);
		read_config(buf,"dbport",m_nDBPort);
	}

  //  std::cout<<m_strLogPath<<std::endl<<m_strDBHost<<std::endl<<m_strDBUser<<std::endl<<m_strDBPwd<<std::endl<<m_strDBName<<std::endl<<m_nDBPort<<std::endl<<m_strDataPath<<std::endl<<m_strWorkPath<<std::endl;
/*
   
	*/
    return true;
}
