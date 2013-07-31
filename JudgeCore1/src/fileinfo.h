#ifndef FILEINFO_H
#define FILEINFO_H

#include <iostream>
#include <string>
#include <cstring>
#include <cstdio>
#include <cstdlib>
#include <ctime>
#include <vector>

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
#include <fcntl.h>

#include "common.h"

using namespace std;

class CFileReader{
private:
    int fd;
    char buf[1024];
    int cnt;
    int pt;
    char get(){
        if(pt + 1 == cnt) cnt = 0;
        if(cnt == 0){
            pt = -1;
            cnt = read(fd, buf, 1024);
            if(cnt < 0){
                perror("read(filereader)");
                exit(EXIT_FILEREADER_READ);
            }
            if(cnt == 0){
                return -1;
            }
        }
        pt++;
        return buf[pt];
    }
public:
    CFileReader(const char *filename){
        fd = open(filename, O_RDONLY);
        if(fd < 0){
            perror("open(filereader)");
            cout << filename << endl;
            //exit(EXIT_FILEREADER_OPEN);

        }
        pt = -1;
        cnt = 0;
    }

    char next(){
        char t = '\r';
        while(t != -1 && t == '\r') t = get();
        return t;
    }
    void ret(){
        pt--;
    }
    ~CFileReader(){
        if(close(fd) < 0){
            perror("close(filereader)");
            //exit(EXIT_FILEREADER_CLOSE);
        }
    }
};

class CFileInfo {
public :
    typedef struct _STC_CASES {
        string strInFile;
        string strOutFile;
    }STC_CASES;
    typedef vector<STC_CASES> VEC_CASES;
    static void GetFileContent(const char *szFileName,string &strInfo);
    static int GetProCases(int nProId, VEC_CASES &stcCases);
private :
    CFileInfo() {};
    ~CFileInfo() {};
};


#endif
