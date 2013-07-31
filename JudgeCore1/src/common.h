#ifndef COMMON_H
#define COMMON_H
//退出代码
#define EXIT_OK                  0
#define EXIT_BAD_USAGE           2
#define EXIT_BAD_ARG             2
#define EXIT_BAD_TIME_LIMIT      4
#define EXIT_BAD_LANG            5
#define EXIT_BAD_MEM_LIMIT       6
#define EXIT_BAD_OUT_LIMIT       8
#define EXIT_BAD_FORK            10
#define EXIT_SETRLIMIT_TIME      12
#define EXIT_SETRLIMIT_MEM       14
#define EXIT_SETRLIMIT_STACK     16
#define EXIT_SETRLIMIT_FSIZE     17
#define EXIT_CHDIR               18
#define EXIT_FREOPEN_IN          20
#define EXIT_FREOPEN_OUT         22
#define EXIT_FREOPEN_ERR         24
#define EXIT_SETITIMER           26
#define EXIT_PTRACE_TRACEME      28
#define EXIT_EXECL               30
#define EXIT_PTRACE_GETREGS      32
#define EXIT_PTRACE_SYSCALL      34
#define EXIT_FILEREADER_OPEN     36
#define EXIT_FILEREADER_READ     38
#define EXIT_FILEREADER_CLOSE    40
#define EXIT_WAIT4               42
#define EXIT_SPJ_POPEN           44
#define EXIT_SPJ_PCLOSE          46
#define EXIT_SPJ_TIMEOUT         48
#define EXIT_SPJ_FSCANF          50
#define EXIT_SPJ_UNKNOWN         52
#define EXIT_UNKNOWN             64

#define MEGA                     1048576// 1 M



#endif
