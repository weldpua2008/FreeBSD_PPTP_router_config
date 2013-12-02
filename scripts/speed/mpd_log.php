#!/usr/local/bin/php -q
<?
$ng='/usr/sbin/ngctl';
$ifconfig='/sbin/ifconfig';

function GetConnection()
{
    $db_host='10.198.198.221';
    $db_database='abills';
    $db_user='abills';
    $db_pass='PASSWD';
    $connect=mysql_connect($db_host,$db_user,$db_pass);
    if ($connect==false) my_log(mysql_error());
    if (!mysql_select_db($db_database,$connect)) my_log(mysql_error());
        return $connect;
};

function suff($val,$end='b'){
    if ($val>1024) {$val=($val/1024); $a='K';};
    if ($val>1024) {$val=($val/1024); $a='M';};
    if ($val>1024) {$val=($val/1024); $a='G';};
    $val=round($val,2).$a.$end;
    return $val;
}


ob_start();
passthru($ifconfig);
$out = explode("\n",ob_get_contents());
ob_end_clean();
$next='';

foreach ($out as $val){
    if ($next!=='') {
        $start=strpos($val,'--> ');
        $end=strpos($val,'netmask')-$start-5;
        $ip=substr($val,$start+4,$end);
        $interface[$next]['ip']=$ip;
        $next='';
    }
    else {
        if (substr($val,0,2)=='ng') $next=substr($val,0,strpos($val,':')); else $next='';
    }
}
unset($out);

ob_start();
passthru('/usr/bin/netstat -n -ib|grep ng|grep Link|awk \'{print $1":"$4":"$6":"$7":"$9}\' ');
$out = explode("\n",ob_get_contents());
ob_end_clean();

foreach ($out as $val){
    if ($val=='')   break;
    $tmp=explode(':',trim($val));
    $int_stat[$tmp[0]]=array('in_p'=>$tmp[1],'in_b'=>$tmp[2],'out_p'=>$tmp[3],'out_b'=>$tmp[4]);
}
$interface=array_merge_recursive($interface,$int_stat);
unset($int_stat);
unset($out);

//var_dump($interface);
//
$query='SELECT users.uid, users.id AS username, INET_NTOA(ip) AS ip FROM users INNER JOIN dv_main ON (dv_main.uid = users.uid)';

$connect=GetConnection();
$qresult=mysql_query($query,$connect);
if (!$qresult) { my_log(mysql_error()); exit(0); }

while ($result = mysql_fetch_assoc($qresult)) {
        $user_ip[$result['ip']]=array ('username'=>$result['username'],'uid'=>$result['uid']);
}

foreach ($interface as $int=>$val){
    ob_start();
    passthru($ng.' msg "'.$int.':inet.1-0-m" getconf|grep cir|awk \'{print $6}\' ');
    $out = ob_get_contents();
    ob_end_clean();
    $int_user[$int]=array('user'=>$user_ip[$val['ip']]['username'],'uid'=>$user_ip[$val['ip']]['uid'],'speed'=>substr($out,4));
}
$interface=array_merge_recursive($interface,$int_user);
unset($int_user);

ob_start();
passthru('hostname');
$filename = trim(ob_get_contents());
ob_end_clean();

// hostname |cut -d'.' -f1
ob_start();
passthru("hostname |cut -d'.' -f1|cut -d '-' -f2");
$tag= trim(ob_get_contents());
ob_end_clean();


//$filename = 'speed.htm';
$handle = fopen('/home/crio/'.$filename, 'w');
foreach ($interface as $int=>$val)
{
    $ip=$val['ip'];
    $user=$val['user'];
    $speed=suff($val['speed'],'b/s');

    $in_p=suff($val['in_p'],'p');
    $in_b=suff($val['in_b'],'B');
    $uid=$val['uid'];
    $out_p=suff($val['out_p'],'p');
    $out_b=suff($val['out_b'],'B');

    fwrite($handle,"<tr><td>MPD $tag</td><td>$int</td><td><a href='http://192.168.1.2/abills/admin/index.cgi?index=15&UID=".$uid."'>$user</a></td><td>$uid</td><td>$ip</td><td>$speed</td><td>$in_p</td><td>$in_b</td><td>$out_p</td><td>$out_b</td></tr> \n");
}    
fclose($handle);
passthru('/usr/local/bin/rsync --verbose --password-file=/usr/local/etc/rsyncd.crt /home/crio/'.$filename.' backuproot@10.198.198.221::mpd_log/');

//var_dump($interface);
?>
