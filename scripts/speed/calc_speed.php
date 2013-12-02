#!/usr/local/bin/php -q
<?
$snmp='/usr/local/bin/snmpwalk';
$cisco='10.200.205.2';
$comm='nas1_snmp';
$dir='/root/speed/';

include($dir.'set.php');

function my_log($message){
    $filename = '/var/log/calc_speed.log';
    $handle = fopen($filename, 'a');
    fwrite($handle,(date('Y-m-d H:i:s').' '.$message."\n"));
//    fwrite($handle,$message."\n");
    fclose($handle);
}
my_log ('-----------');

ob_start();                                                         
passthru($snmp.'  -On -v 2c -c '.$comm.' '.$cisco.' IF-MIB::ifHCInOctets.12');
$count =spliti(' ',ob_get_contents());
$count=trim($count[3]);
ob_end_clean();

$filename = 'last_count';
$handle = fopen($dir.$filename, 'r');
$last=explode("\n",fread($handle,8192));
$timestamp=$last[0];
$mult=$last[3];
$last=$last[1];
fclose($handle);

$filename = 'last_speed';
$handle = fopen($dir.$filename, 'r');
$speed=array();
$file=(fread($handle,8192));
$speed=unserialize($file);
fclose($handle);
if ((!is_array($speed)) or (count($speed)<10)) {my_log('Read last_speed error !'); die();};

$cur_speed=$count-$last;
my_log('Curr speed '.$cur_speed);

for ($i = 1; $i <= 9; $i++) {
    $speed[$i]=$speed[$i+1];
    $summ+=$speed[$i];
}

$summ=($summ+$cur_speed)*8;
$summ=intval($summ/(1024*1024*600));

$hval=$link_speed*$high_limit/100;
if ($summ>$hval) {$mult=$mult-1;} else {$mult=$mult+1;};
if ($mult<1) $mult=1;

my_log('Avg '.$summ);
$speed[10]=$cur_speed;
my_log('Time '.strval(intval(microtime(true)-$timestamp)));
$time=(intval(microtime(true)-$timestamp));
if ($time>100) {my_log('Speed measures interval too high !'); };

$filename = 'last_speed';
$handle = fopen($dir.$filename, 'w'); 
fwrite($handle,serialize($speed));
fclose($handle);

if ($mult>16) $mult=16;
$timestamp=(intval(microtime(true)));
$filename = 'last_count';
$handle = fopen($dir.$filename, 'w'); 
fwrite($handle,$timestamp."\n".$count."\n".$summ."\n".$mult);
fclose($handle);
?>
