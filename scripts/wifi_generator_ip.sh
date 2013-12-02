#!/bin/sh
txt="/wifi.txt"
db="/wifi.db"
sqlite3="/usr/local/bin/sqlite3 "
nginx="/usr/local/www/nginx/index.html.tmp"
nginx_main="/usr/local/www/nginx/index.html"
lock="/tmp/wifi.db.lock"
ip=$1
login=$2
web=""

login_all=`$sqlite3 $db "SELECT login FROM wifi WHERE login='$login'"`
 
for login in $login_all
do
    ip=`$sqlite3 $db "SELECT ip FROM wifi WHERE login='$login'"`
    a=`ping -s 1024 -i 0.3 -c 10 -q $ip|grep -v PING`
        # login=`$sqlite3 $db "SELECT login FROM wifi WHERE login='$login'"`
    mac=`$sqlite3 $db "SELECT mac FROM wifi WHERE login='$login'"`
    adress=`$sqlite3 $db "SELECT adress FROM wifi WHERE login='$login'"`
 
    #echo $a
    #echo "---------------------------------------"
    $sqlite3 $db "UPDATE wifi SET last_ping='$a' WHERE login='$login'";
    a=`$sqlite3 $db "SELECT last_ping FROM wifi WHERE login='$login'"`
    n=`echo $a|wc -w`
    #100%lost
    if [ ${n} = "14" ] 
    then
        web="$web <tr bgcolor=\"#FF0000;\">"
        b=`echo $a|awk '{print $1" "$2" "$12" "$13" "$14}'`
        #echo $b
        up="DOWN"
        $sqlite3 $db "UPDATE wifi SET last_ping='$a' WHERE login='$login'";
        $sqlite3 $db "UPDATE wifi SET UP='$up'  WHERE login='$login'";
        web="$web <td>$login</td><td>$adress</td><td>$up</td><td>$b</td>" # >>$nginx
        web="$web </tr>" # >>$nginx
         #
    fi    
    if [ ${n} = "19" ] 
    then
        b=`echo $a|awk '{print $1" "$2" "$12" "$13" "$14}'`
        $sqlite3 $db "UPDATE wifi SET last_ping='$a' WHERE login='$login'";
        #echo $b
        # login=`$sqlite3 $db "SELECT login FROM wifi WHERE login='$login'"`
        # mac=`$sqlite3 $db "SELECT mac FROM wifi WHERE login='$login'"`
        # adress=`$sqlite3 $db "SELECT adress FROM wifi WHERE login='$login'"`
         up="UP"
         loss=`echo $a|awk '{print $12}'`
         loss_number=`echo $loss|tr '%' ' '| tr '.' ' '|awk '{print $1}'|sed 's/^ //'`
         #loss_number=`expr $loss_number / 10`
         web="$web <tr border=\"1\">"
         bg=""
         if [ $loss_number -ge 20 ];
          then
               bg="  bgcolor=\"#FF0000;\""
          fi
                
        if [ $loss_number -ge 40 ];
         then
            up="LOSS"
            bgloss="  bgcolor=\"#C0C0C0;\""

         fi
          $sqlite3 $db "UPDATE wifi SET UP='$up'  WHERE login='$login'";
          web="$web <td>$login</td><td>$adress</td><td $bgloss>$up</td><td $bg>$b</td>" #>>$nginx
          web="$web </tr>" #>>$nginx

    fi    
    if [ ${n} = "21" ] 
    then
         b=`echo $a|awk '{print $1" "$2" "$14" "$115" "$16}'`
         $sqlite3 $db "UPDATE wifi SET last_ping='$a' WHERE login='$login'";
        # login=`$sqlite3 $db "SELECT login FROM wifi WHERE login='$login'"`
        # mac=`$sqlite3 $db "SELECT mac FROM wifi WHERE login='$login'"`
        # adress=`$sqlite3 $db "SELECT adress FROM wifi WHERE login='$login'"`
         up="UP"
         loss=`echo $a|awk '{print $14}'`
         loss_number=`echo $loss|tr '%' ' '| tr '.' ' '|awk '{print $1}'|sed 's/^ //'`
          bg=""
           if [ $loss_number -ge 20 ];
            then
                       bg="  bgcolor=\"#FF0000;\""
            fi
        bgloss=''
        if [ $loss_number -ge 40 ];
         then
            up="LOSS"
            bgloss="  bgcolor=\"#C0C0C0;\""
         fi
          $sqlite3 $db "UPDATE wifi SET UP='$up'  WHERE login='$login'";
          web="$web  <td>$login</td><td>$adress</td><td $bgloss>$up</td><td $bg>$b</td>" #>>$nginx
          web="$web </tr>" #>>$nginx
          
    fi    

 $sqlite3 $db "UPDATE wifi SET web='$web' WHERE login='$login'";


done    
