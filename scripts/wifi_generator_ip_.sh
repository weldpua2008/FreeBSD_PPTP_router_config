#!/bin/sh
txt="/wifi.txt"
db="/wifi.db"
sqlite3="/usr/local/bin/sqlite3 "
nginx="/usr/local/www/nginx/index.html.tmp"
nginx_main="/usr/local/www/nginx/index.html"
lock="/tmp/wifi.db.lock"
ip=$1


ip_all=`$sqlite3 $db "SELECT ip FROM wifi WHERE ip='$ip'"`
 
for ip in $ip_all
do
    a=`ping -s 1024 -i 0.3 -c 10 -q $ip|grep -v PING`
    echo $a
    echo "---------------------------------------"
    $sqlite3 $db "UPDATE wifi SET last_ping='$a' WHERE ip='$ip'";
    a=`$sqlite3 $db "SELECT last_ping FROM wifi WHERE ip='$ip'"`
    n=`echo $a|wc -w`
    #100%lost
    if [ ${n} = "14" ] 
    then
        b=`echo $a|awk '{print $1" "$2" "$12" "$13" "$14}'`
        #echo $b
        $sqlite3 $db "UPDATE wifi SET last_ping='$b' WHERE ip='$ip'";
        $sqlite3 $db "UPDATE wifi SET UP='down'  WHERE ip='$ip'";
    fi    
    if [ ${n} = "19" ] 
    then
        b=`echo $a|awk '{print $1" "$2" "$12" "$13" "$14}'`
        $sqlite3 $db "UPDATE wifi SET last_ping='$b' WHERE ip='$ip'";
        #echo $b
         login=`$sqlite3 $db "SELECT login FROM wifi WHERE ip='$ip'"`
         mac=`$sqlite3 $db "SELECT mac FROM wifi WHERE ip='$ip'"`
         adress=`$sqlite3 $db "SELECT adress FROM wifi WHERE ip='$ip'"`
         up="UP"
         loss=`echo $a|awk '{print $12}'`
         loss_number=`echo $loss|tr '%' ' '| tr '.' ' '|awk '{print $1}'|sed 's/^ //'`
         #loss_number=`expr $loss_number / 10`
        if [ $loss_number -ge 40 ];
         then
            up="LOSS"
         fi
          $sqlite3 $db "UPDATE wifi SET UP='down'  WHERE ip='$ip'";
          
    fi    
    if [ ${n} = "21" ] 
    then
         b=`echo $a|awk '{print $1" "$2" "$14" "$115" "$16}'`
         $sqlite3 $db "UPDATE wifi SET last_ping='$b' WHERE ip='$ip'";
         login=`$sqlite3 $db "SELECT login FROM wifi WHERE ip='$ip'"`
         mac=`$sqlite3 $db "SELECT mac FROM wifi WHERE ip='$ip'"`
         adress=`$sqlite3 $db "SELECT adress FROM wifi WHERE ip='$ip'"`
         up="UP"
         loss=`echo $a|awk '{print $14}'`
         loss_number=`echo $loss|tr '%' ' '| tr '.' ' '|awk '{print $1}'|sed 's/^ //'`
        if [ $loss_number -ge 40 ];
         then
            up="LOSS"
         fi
          $sqlite3 $db "UPDATE wifi SET UP='down'  WHERE ip='$ip'";
          
    fi    



done    
