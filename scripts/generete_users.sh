#!/bin/sh

d=0
login_prefix=nemo
pass_prefix=2511

start_ip_1octet=10
start_ip_2octet=200
start_ip_3octet=27
start_ip_4octet=1

for num in `jot 10 1 1`
    do
        d=`expr $d + 1`
       # echo $d
    start_ip_4octet=`expr $start_ip_4octet + 1`
    
    if [ "$start_ip_4octet" -ge 254 ] 
    then
        start_ip_3octet=`expr $start_ip_3octet + 1`
        start_ip_4octet=1

    fi

    echo "$login_prefix$d \"$pass_prefix$d\" $start_ip_1octet.$start_ip_2octet.$start_ip_3octet.$start_ip_4octet/32"


        done


