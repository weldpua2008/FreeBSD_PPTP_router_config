#!/usr/local/bin/php -q
<?
$ng='/usr/sbin/ngctl';
$ifconfig='/sbin/ifconfig';
$time_start = microtime(1);

include_once('set.php');

function my_log($message){
            $filename = '/var/log/speed_cron.log';
            $handle = fopen($filename, 'a');
            fwrite($handle,(date('Y-m-d H:i:s').' '.$message."\n"));
            fclose($handle);
}

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

function in_time($start,$stop,$hour) {
    if ($start<$stop){                                                                                                              
        if (($hour>=$start) and ($hour<=$stop)) $result=true; else $result=false;                                                    
    }                                                                                                                               
    else {                                                                                                                          
        if (($hour>$start) or in_array($hour,range(0,$stop))) $result=true; else $result=false; 
    }                                                                                                                               
    return $result;                                                                                                                    
}

// INTERFACES
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

// --------------------- USER ----
$query="SELECT  users.uid, users.id as username, trafic_tarifs.in_speed, trafic_tarifs.out_speed, dv_main.speed, 
                groups.gid AS groupid, tarif_plans.id AS tarifid, INET_NTOA(ip) AS ip_1
    FROM  trafic_tarifs
    INNER JOIN intervals ON (trafic_tarifs.interval_id = intervals.id)
    INNER JOIN tarif_plans ON (intervals.tp_id = tarif_plans.id)
    INNER JOIN dv_main ON (tarif_plans.id = dv_main.tp_id)
    INNER JOIN users ON (dv_main.uid = users.uid)
    INNER JOIN groups ON (users.gid = groups.gid)";


$time_start2 = microtime(1);
$connect=GetConnection();
$qresult=mysql_query($query,$connect);
if (!$qresult) { my_log(mysql_error()); exit(0); }

while ($result = mysql_fetch_assoc($qresult)) {
    $user_ip[$result['ip_1']]=array ('username'=>$result['username'],'uid'=>$result['uid']);

    if ($result['speed']==0) $result['speed']=$result['in_speed'];
    $users[$result['uid']]=array(   'speed'=>$result['speed'],
                                    'groupid'=>$result['groupid'],
                                    'ip'=>$result['ip_1'],
                                    'tarifid'=>$result['tarifid']);
}

$time_start2 = microtime(1);

//-----------------------------------------------


/*
// --------------------- TRAFF IN CURRENT MONTH -------
$query="SELECT  uid,sum(if(date_format(start, '%Y-%m')=date_format(curdate(), '%Y-%m'),                                                 
                recv + 4294967296 * acct_input_gigawords, 0))/(1024*1024*1024) as t_in
                FROM dv_log group by uid";

$qresult=mysql_query($query,$connect);
while ($traff_in = mysql_fetch_assoc($qresult)) {
    $users[$traff_in['uid']]['t_in']=0;
}
*/

foreach ($interface as $key=>$val){
    $uid=$user_ip[$val['ip']]['uid'];

    $interface[$key]['speed']=$users[$uid]['speed'];
    $interface[$key]['groupid']=$users[$uid]['groupid'];
    $interface[$key]['tarifid']=$users[$uid]['tarifid'];
//    $interface[$key]['t_in']=$users[$uid]['t_in'];
    $interface[$key]['t_in']=0;
}

foreach ($interface as $int=>$val){
    $groupid=$val['groupid'];
    $tarifid=$val['tarifid'];
    $traff_in=$val['t_in'];
//  if (!isset($group[$groupid][$tarifid])) {echo $int." not in group\n"; continue;} else echo $int." group $groupid tarif $tarifid\n";

    $interface[$int]['set']=1;
    $param=explode(',',$group[$groupid][$tarifid]);

    if ($param[3]==1) {$flow=true;} else {$flow=false;} ;

    $night_k=$param[1];
    $day_k=1;
    if ($param[0]==0) {$speed=0;} else {$speed=explode('/',$param[0]);};
    $speed=$speed[0];
    if ($speed==0) $speed=$val['speed'];
    
    if (isset($param[2]))
    {
        $day_mult=explode('|',$param[2]);
        foreach ($day_mult as $val2) {
            $mk=explode('/',$val2);
            if ($traff_in>$mk[0]) $day_k=floatval(1/$mk[1]);
        }
    }

    if ($flow) {
            $filename = '/home/crio/speed/last_count';
            $handle = fopen($filename, 'r');
            $last=explode("\n",fread($handle,8192));
            $mult=$last[3];
            fclose($handle);
            log('Mult '.$mult);
            $speed=$speed*$mult;
            if ($speed>100000) $speed=100000;
    }
    else
    {
        if (in_time($time_day['start'],$time_day['end'],date('G')) ){
            $speed=$speed*$day_k;
        }
    
        if (in_time($time_night['start'],$time_night['end'],date('G'))){
            $speed=$speed*$night_k;
        }
    }
    $interface[$int]['speed']=$speed;
}

foreach ($interface as $int=>$val){
    if ($val['set']==1) {
        if ($val['speed']==0) {my_log('Interface '.$int.' speed 0 WARNING '); continue;};
        $speed=$val['speed']*1024;
        $cbs=intval($speed/8);
        $ebs=$cbs*2;
        passthru($ng.' msg '.$int.':inet.0-0-m setconf { upstream={ cbs='.$cbs.' ebs='.$ebs.' cir='.$speed.' greenAction=1 yellowAction=1 redAction=2 mode=2 } downstream={ cbs='.$cbs.' ebs='.$ebs.' cir='.$speed.' greenAction=1 yellowAction=1 redAction=2 mode=2 } }'."\n");
        passthru($ng.' msg '.$int.':inet.0-0-mi setconf { upstream={ cbs='.$cbs.' ebs='.$ebs.' cir='.$speed.' greenAction=1 yellowAction=1 redAction=2 mode=2 } downstream={ cbs='.$cbs.' ebs='.$ebs.' cir='.$speed.' greenAction=1 yellowAction=1 redAction=2 mode=2 } }'."\n");
        passthru($ng.' msg '.$int.':inet.1-0-m setconf { upstream={ cbs='.$cbs.' ebs='.$ebs.' cir='.$speed.' greenAction=1 yellowAction=1 redAction=2 mode=2 } downstream={ cbs='.$cbs.' ebs='.$ebs.' cir='.$speed.' greenAction=1 yellowAction=1 redAction=2 mode=2 } }'."\n");
        passthru($ng.' msg '.$int.':inet.1-0-mi setconf { upstream={ cbs='.$cbs.' ebs='.$ebs.' cir='.$speed.' greenAction=1 yellowAction=1 redAction=2 mode=2 } downstream={ cbs='.$cbs.' ebs='.$ebs.' cir='.$speed.' greenAction=1 yellowAction=1 redAction=2 mode=2 } }'."\n");
        my_log('Set speed '.$speed.' interface '.$int);
    }
}

//var_dump($interface);
$time_end = microtime(1);
$time = $time_end - $time_start;
my_log ("Work time - $time sec \n");

?>
