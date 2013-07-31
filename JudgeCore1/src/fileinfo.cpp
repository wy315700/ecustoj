#include <iostream>
#include <fstream>
using namespace std;

#include "fileinfo.h"
#include "config.h"


void CFileInfo::GetFileContent(const char *szFileName,string &strInfo) {
    ifstream fin;
    fin.open(szFileName);
    string strLine;
    while(getline(fin,strLine)) {
        strInfo += strLine + "\n";
    }
}
int CFileInfo::GetProCases(int nProId, VEC_CASES &vecCases) {
    char szTmp[32] = {0};
    sprintf(szTmp,"%d",nProId);
    string strProId = szTmp;
    string strConf = CConfig::m_strDataPath + strProId + "/cases.conf";
    ifstream fin;
    fin.open(strConf.c_str());
    string strInFile,strOutFile;
    STC_CASES stcCases;
    while(fin >> strInFile) {
        fin >> strOutFile;
        stcCases.strInFile = CConfig::m_strDataPath + strProId + "/" + strInFile;
        stcCases.strOutFile = CConfig::m_strDataPath + strProId + "/" + strOutFile;
        vecCases.push_back(stcCases);
    }
    return (int)vecCases.size();
}
