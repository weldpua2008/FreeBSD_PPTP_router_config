#!/usr/local/bin/php -q
<?
$secret='Rkfdbfnehf';
#$ipfw='/sbin/ipfw';
$ifconfig='/sbin/ifconfig';
$ng='/usr/sbin/ngctl';


$ip="191.5.137.24";
$speed_in=5000;
$speed_out=500;
$speed_in_ua=5000;
$speed_out_ua=5000;
$int="ng69";
$state=FALSE;
$count=0;

    
    $speed_in=$speed_in*1024;
    $cbs_in=intval($speed_in/8);
    $ebs_in=$cbs_in*2;

    $speed_out=$speed_out*1024;
    $cbs_out=intval($speed_out/8);
    $ebs_out=$cbs_out*2;

    echo ''.$ng.' msg '.$int.':inet.0-0-m setconf "{ upstream={ cbs='.$cbs_in.' ebs='.$ebs_in.' cir='.$speed_in.' greenAction=1 yellowAction=1 redAction=2 mode=2 } downstream={ cbs='.($cbs_out).' ebs='.($ebs_out).' cir='.($speed_out).' greenAction=1 yellowAction=1 redAction=2 mode=2 } }"'."\n";
    echo ''.$ng.' msg '.$int.':inet.0-0-mi setconf "{ upstream={ cbs='.$cbs_in.' ebs='.$ebs_in.' cir='.$speed_in.' greenAction=1 yellowAction=1 redAction=2 mode=2 } downstream={ cbs='.($cbs_out).' ebs='.($ebs_out).' cir='.($speed_out).' greenAction=1 yellowAction=1 redAction=2 mode=2 } }"'."\n";
    echo ''.$ng.' msg '.$int.':inet.1-0-m setconf "{ upstream={ cbs='.$cbs_in.' ebs='.$ebs_in.' cir='.$speed_in.' greenAction=1 yellowAction=1 redAction=2 mode=2 } downstream={ cbs='.($cbs_out).' ebs='.($ebs_out).' cir='.($speed_out).' greenAction=1 yellowAction=1 redAction=2 mode=2 } }"'."\n";
    echo ''.$ng.' msg '.$int.':inet.1-0-mi setconf "{ upstream={ cbs='.$cbs_in.' ebs='.$ebs_in.' cir='.$speed_in.' greenAction=1 yellowAction=1 redAction=2 mode=2 } downstream={ cbs='.($cbs_out).' ebs='.($ebs_out).' cir='.($speed_out).' greenAction=1 yellowAction=1 redAction=2 mode=2 } }"'."\n";

    $state=true;

?>
