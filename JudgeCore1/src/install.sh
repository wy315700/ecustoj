#!/bin/sh
cmake .
make &&make install
mkdir /etc/eoj/
cp eoj.conf /etc/eoj/
echo "nohup /usr/local/bin/JudgeCore.exe &">>/etc/rc.local

