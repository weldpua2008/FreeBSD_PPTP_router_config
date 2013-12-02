#!/usr/local/bin/php -q
<?
$secret='Rkfdbfnehf';
#$ipfw='/sbin/ipfw';
$ifconfig='/sbin/ifconfig';
$ng='/usr/sbin/ngctl';

function my_log($message){
    global $time_start;

    $filename = '/var/log/speed_daemon.log';
    $handle = fopen($filename, 'a');
    fwrite($handle,(date('Y-m-d H:i:s').' '.$message."\n"));
//    fwrite($handle,$message."\n");
    fclose($handle);

}

$input = fgets(STDIN);
$c=strpos($input,'@');
$data=substr($input,0,$c);    
$md5=substr($input,$c+1,strlen($input)-$c-1);
$data_md5=md5(strval($data.$secret));
if ($data_md5!==$md5) {
    my_log('Invalid data -'.var_export($data,true)) ; 
    die("GTFO\r\n"); 
}

my_log($data);
#my_log(var_export($md5,true));
#my_log(var_export($data_md5,true));

//die();

$param=explode('|',$data);
$ip=$param[0];
$speed_in=$param[1];
$speed_out=$param[2];
$speed_in_ua=$param[3];
$speed_out_ua=$param[4];

$state=FALSE;
$count=0;

do {
ob_start();
passthru($ipfw.' show');
$out = explode("\n",ob_get_contents());
ob_end_clean();

$count++;

ob_start();
passthru($ifconfig);
$out = explode("\n",ob_get_contents());
ob_end_clean();

$next='';
foreach ($out as $str) {
    if ($next!=='') {
        $str=trim($str);
        $str=str_replace('    ',' ',$str);
        $str=str_replace('   ',' ',$str);
        $str=str_replace('  ',' ',$str);
        $str=str_replace('  ',' ',$str);
        $str=str_replace('  ',' ',$str);
        $str=str_replace('  ',' ',$str);
        $str=str_replace('  ',' ',$str);
        $str=str_replace('  ',' ',$str);
        $str=str_replace('  ',' ',$str);
        $str=explode(' ',$str);
        if ($str[3]==$ip) 
        {
            my_log('interface: '.$next);
            break;
        }    
    }
    if (substr($str,0,2)=='ng') $next=substr($str,0,strpos($str,':')); else $next='';
}

if ($next!='')
{
    
    $int=$next;
    ob_start();
    $speed=$speed_in*1024;
    $cbs=intval($speed/8);
    $ebs=$cbs*2;
    passthru($ng.' msg '.$int.':inet.0-0-m setconf { upstream={ cbs='.$cbs.' ebs='.$ebs.' cir='.$speed.' greenAction=1 yellowAction=1 redAction=2 mode=2 } downstream={ cbs='.$cbs.' ebs='.$ebs.' cir='.$speed.' greenAction=1 yellowAction=1 redAction=2 mode=2 } }'."\n");
    passthru($ng.' msg '.$int.':inet.0-0-mi setconf { upstream={ cbs='.$cbs.' ebs='.$ebs.' cir='.$speed.' greenAction=1 yellowAction=1 redAction=2 mode=2 } downstream={ cbs='.$cbs.' ebs='.$ebs.' cir='.$speed.' greenAction=1 yellowAction=1 redAction=2 mode=2 } }'."\n");
    passthru($ng.' msg '.$int.':inet.1-0-m setconf { upstream={ cbs='.$cbs.' ebs='.$ebs.' cir='.$speed.' greenAction=1 yellowAction=1 redAction=2 mode=2 } downstream={ cbs='.$cbs.' ebs='.$ebs.' cir='.$speed.' greenAction=1 yellowAction=1 redAction=2 mode=2 } }'."\n");
    passthru($ng.' msg '.$int.':inet.1-0-mi setconf { upstream={ cbs='.$cbs.' ebs='.$ebs.' cir='.$speed.' greenAction=1 yellowAction=1 redAction=2 mode=2 } downstream={ cbs='.$cbs.' ebs='.$ebs.' cir='.$speed.' greenAction=1 yellowAction=1 redAction=2 mode=2 } }'."\n");
    my_log('ip '.$ip.' interface '.$int.' speed '.$speed);
    my_log(ob_get_contents());
    ob_end_clean();

    $state=true;
}
else
{
    my_log('not found interface !');
    sleep(3);
//    my_log(var_export($pipes,true));
}

}
while (!$state and $count<5);

?>
