#!/bin/sh

ext_ip=110.
ext_ip=191.
 ext_ip=191.
local_172=172.16.2.210
limit_speed_8mb=""




/sbin/ipfw delete 1
/sbin/ipfw delete 2
/sbin/ipfw delete 3
/sbin/ipfw delete 8
/sbin/ipfw delete 64000
/sbin/ipfw delete 64001

 
/sbin/ipfw nat 123 config ip $ext_ip
 /sbin/ipfw nat 124 config ip $local_172

#/sbin/ipfw table 1 add 172.0.0.0/8


/sbin/ipfw add 3 nat 123 ip from  not table\(3\) to  $ext_ip
#/sbin/ipfw add 2 nat 124 ip from  table\(3\) to $local_172

/sbin/ipfw add 8 deny all from any to any 137-141



/sbin/ipfw add 64001 nat 123 ip from table\(1\) to not table\(3\)
#/sbin/ipfw add 64000 nat 124 ip from table\(4\) to table\(3\)

#/sbin/ipfw table 1 add 10.200.24.0/21
/sbin/ipfw table 1 flush
#/sbin/ipfw table 1 add 10.200.25.0/24
#/sbin/ipfw table 1 add 10.200.26.0/24
#/sbin/ipfw table 1 add 10.200.27.0/24
/sbin/ipfw table 1 add 10.200.5.128/25


#local interfaces for nat
/sbin/ipfw table 2 flush
/sbin/ipfw table 2 add 172.16.2.210
/sbin/ipfw table 2 add 172.168.10.1
/sbin/ipfw table 2 add 172.16.1.3
/sbin/ipfw table 2 add 172.168.1.1
/sbin/ipfw table 2 add 172.168.4.1
/sbin/ipfw table 2 add 172.168.3.1
#local networks for nat
/sbin/ipfw table 3 flush
/sbin/ipfw table 3 add 172.16.2.0/24
/sbin/ipfw table 3 add 172.168.0.0/16

/sbin/ipfw table 3 add 10.0.0.0/8


/sbin/ipfw table 4 flush
/sbin/ipfw table 4 add $local_172
/sbin/ipfw table 4 add 10.200.26.0/24
/sbin/ipfw table 4 add 10.200.25.0/24
/sbin/ipfw table 4 add 10.200.27.0/24


