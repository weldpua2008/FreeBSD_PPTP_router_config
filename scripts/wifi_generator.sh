#!/bin/sh
txt="/wifi.txt"
db="/wifi.db"
sqlite3="/usr/local/bin/sqlite3 "
nginx="/usr/local/www/nginx/index.html.tmp"
nginx_main="/usr/local/www/nginx/index.html"
lock="/tmp/wifi.lock"

if [ -f $lock ];then 
    echo "lock with $lock"
    exit;
fi
touch $lock    
sql_str="$sqlite3 $db"
$sqlite3 $db  "
create table IF NOT EXISTS  wifi (                                                                                             
        ip TEXT,
        login TEXT UNIQUE,
        mac TEXT,
        adress TEXT,
        last_ping TEXT,
        UP text,
        web text);"

echo "#!/bin/sh">/tmp/wifi.txt
#!echo "\n">>/tmp/wifi.txt

cat $txt|awk -v S=$sqlite3 -v D=$db  '{print  ""S" "D" \"delete from wifi where login=\047"$1"\047;insert into wifi (ip,login,mac,adress)  values  (\047"$2"\047,\047"$1"\047,\047"$3"\047,\047"$4" "$5" "$6" "$7" "$8" "$9"\047); \" \n"}' >>/tmp/wifi.txt
#cat $txt|awk -v S=$sqlite3 -v D=$db  '{print  ""S" "D" \"delete from wifi where ip=\047"$2"\047;insert into wifi (ip,login,mac,adress)  values  (\047"$2"\047,\047"$1"\047,\047"$3"\047,\047"$4" "$5" "$6" "$7" "$8" "$9"\047); \" \n"}' >>/tmp/wifi.txt


#fiil new!!!
/bin/sh /tmp/wifi.txt

login_all=`$sqlite3 $db "SELECT login FROM wifi WHERE ip LIKE '%1%' ORDER BY up ASC"`
#echo $ip
ip=""
echo "#!/bin/sh">/tmp/wifi.txt
echo "<html>
<head>
<title>Welcome</title>
</head>
<body bgcolor="white" text="black">
">$nginx
date >>$nginx

echo "<table>" >>$nginx
for login in $login_all
do
#    a=`ping -s 1024 -i 0.3 -c 10 -q $ip|grep -v PING`
#    $sqlite3 $db "UPDATE wifi SET last_ping='$a' WHERE ip='$ip'";
    ip=`$sqlite3 $db "SELECT ip FROM wifi WHERE login='$login'"`
    /wifi_generator_ip.sh $ip $login &
    sleep 2
    a=`$sqlite3 $db "SELECT last_ping FROM wifi WHERE login='$login'"`
    n=`echo $a|wc -w`
    #echo "!!!!!!!!!: $n - $a"
    #100%lost
    if [ ${n} = "14" ] 
    then
        sleep 3
#     echo "<tr bgcolor=\"#FF0000;\">" >>$nginx

         $sqlite3 $db "UPDATE wifi SET UP='down'  WHERE login='$login'";
         #login=`$sqlite3 $db "SELECT login FROM wifi WHERE login='$login'"`
         mac=`$sqlite3 $db "SELECT mac FROM wifi WHERE login='$login'"`
         adress=`$sqlite3 $db "SELECT adress FROM wifi WHERE login='$login'"`
         up="DOWN"
  #       echo "<td>$login</td><td>$adress</td><td>$up</td><td>$a</td>" >>$nginx
   #      echo "</tr>" >>$nginx
       #  $sqlite3 $db "UPDATE wifi SET UP='$up'  WHERE login='$login'";
    fi    
    if [ ${n} = "19" ] 
    then
        sleep 1
         #login=`$sqlite3 $db "SELECT login FROM wifi WHERE login='$login'"`
         mac=`$sqlite3 $db "SELECT mac FROM wifi WHERE login='$login'"`
         adress=`$sqlite3 $db "SELECT adress FROM wifi WHERE login='$login'"`
         up="up"
         loss=`echo $a|awk '{print $12}'`
         loss_number=`echo $loss|tr '%' ' '| tr '.' ' '|awk '{print $1}'|sed 's/^ //'`
         #loss_number=`expr $loss_number / 10`
         #echo "<tr border=\"1\">" >>$nginx
         bg=""
         if [ $loss_number -ge 20 ];
         then
            bg="  bgcolor=\"#FF0000;\""
             

         fi  
         if [ $loss_number -ge 40 ];
         then
            up="loss"
         fi
          $sqlite3 $db "UPDATE wifi SET UP='$up'  WHERE login='$login'";
          
#         echo "<td>$login</td><td>$adress</td><td>$up</td><td $bg>$a</td>" >>$nginx
 #        echo "</tr>" >>$nginx
    fi    
    if [ ${n} = "21" ] 
    then
        sleep 2
         #login=`$sqlite3 $db "SELECT login FROM wifi WHERE login='$login'"`
         mac=`$sqlite3 $db "SELECT mac FROM wifi WHERE login='$login'"`
         adress=`$sqlite3 $db "SELECT adress FROM wifi WHERE login='$login'"`
         up="UP"
         loss=`echo $a|awk '{print $14}'`
         loss_number=`echo $loss|tr '%' ' '| tr '.' ' '|awk '{print $1}'|sed 's/^ //'`
         #loss_number=`expr $loss_number / 10`
         #echo "*** lossss = $loss |$loss_number********** ===> $a "
         #echo "<tr border=\"1\">" >>$nginx
         bg=""
         if [ $loss_number -ge 20 ];
         then
            bg="  bgcolor=\"#FF0000;\""
             

         fi  
         if [ $loss_number -ge 40 ];
         then
            up="LOSS"
         fi
          $sqlite3 $db "UPDATE wifi SET UP='$up'  WHERE login='$login'";
          
#         echo "<td>$login</td><td>$adress</td><td>$up</td><td $bg>$a</td>" >>$nginx
 #        echo "</tr>" >>$nginx
    fi    
done    

login_all=`$sqlite3 $db "SELECT login FROM wifi WHERE ip LIKE '%1%' ORDER BY up ASC"`
for login in $login_all
do
    web=`$sqlite3 $db "SELECT web FROM wifi WHERE login='$login'"`
    echo $web >>$nginx

done    

echo "</table></body>
</html>" >>$nginx

rm $lock
cat $nginx>$nginx_main
# /bin/sh /tmp/wifi.txt
