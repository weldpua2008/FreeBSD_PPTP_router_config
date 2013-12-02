#!/usr/local/bin/php
<?php
define("SNMP_SERVER","10.200.205.2");
define("SNMP_COMUNITY","nas1_snmp");
define("MIB_IF_OUT","IF-MIB::ifHCOutOctets");
define("MIB_IF_IN","IF-MIB::ifHCInOctets");
define("MIB_IF_NAME","IF-MIB::ifName");
define("MIB_IF_ROUTE_INDEX",".1.3.6.1.2.1.4.21.1.2.");    


$link = mysql_connect('10.198.198.221', 'skmgroups', 'password');
$table_ipfw_for_ip_blockminus="5";
if (!$link) {
        die('Could not connect: ' . mysql_error());
}
mysql_select_db("skmgroups",$link);

function quote_smart($value)
{
    if (get_magic_quotes_gpc()) {
        $value = stripslashes($value);
    }
    if (!is_numeric($value)) {
        $value = mysql_real_escape_string($value);
    }
    return $value;
}
$tarif_array=array(
                    '1'=>array("speed"=>"1","no_change_speed"=>true,"abonka"=>0),
                    '10'=>array("speed"=>"15","no_change_speed"=>true,"abonka"=>60),
                    '11'=>array("speed"=>"100","no_change_speed"=>false,"abonka"=>85),
                    '12'=>array("speed"=>"100","no_change_speed"=>false,"abonka"=>85),
                    '13'=>array("speed"=>"4","no_change_speed"=>true,"abonka"=>100),
                    '15'=>array("speed"=>"7","no_change_speed"=>true,"abonka"=>50),
                    '51'=>array("speed"=>"1","no_change_speed"=>true,"abonka"=>250)

                    );

$f=file("/usr/local/etc/mpd5/speed_counter.ip_online");
$start=0;
$link2 = mysql_connect('10.200.205.1', 'freenibs', 'nhfdfeljvf2006');
if (!$link2) {
            die('Could not connect: ' . mysql_error());
}
/*  */
    mysql_select_db("freenibs",$link2);

foreach($f as $line){
    $ip=trim($line);
    if( (strlen($ip)>6)&& ( preg_match("/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/",$ip) )  ){


         $q="SELECT * FROM `users` WHERE `ip`='".quote_smart($ip)."'";
        $result = mysql_query($q,$link);
        //echo $q."\n";
        if (!$result) {
                die('Invalid query: ' . mysql_error());
        }
        $num_rows = mysql_num_rows($result);
        if($num_rows>0){
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
               /* if($row['tp_id']!=11)
                        $deposit=$row['deposit']+ $tarif_array[$row['tp_id']]['abonka'];
                 else 
                        $deposit=$row['deposit']; 
                        */     
            $deposit=$row['deposit'];    
            $fr_ip=trim($row['ip']);
			$user=$row['username'];
			$speed=$row['speed'];        
			$packet=$row['tp_id'];
         }
                 $mib="".MIB_IF_ROUTE_INDEX.$ip; //MIB_IF_ROUTE_INDEX
                 $index = trim(str_replace("INTEGER:","",snmp2_get(SNMP_SERVER,SNMP_COMUNITY , $mib)));

	        $interface=trim(str_replace("STRING:","",snmp2_get(SNMP_SERVER,SNMP_COMUNITY ,MIB_IF_NAME.".".$index)));  
           $q="UPDATE `speed_counter` SET `interface`='' WHERE `interface`='".$interface."'";
           //echo $q."\n";
                     mysql_query($q,$link2);

           $q="
             INSERT INTO `freenibs`.`speed_counter` (
             `ip` ,
             `all_last`,
             `in_last` ,
             `out_last` ,
             `time_last` ,
             `in_speed` ,
             `out_speed`,
             `all_speed`,
             `interface`,
             `gid`,
             `user`
             )
             VALUES (
                '".$fr_ip."',
                 '0',
                '0',
                '0',
                '0',
                '0',
                '0',
                '0',
                 '".$interface."',
                 '".$packet."',
                 '".$user."'
             )
              ON duplicate KEY UPDATE
              `ip`='".$fr_ip."',
               `gid`='".$packet."',
               `interface`='".$interface."',
               `user`='".$user."' ";

            //echo $q."\n";
            mysql_query($q,$link2);
        }


        if($start==1)
          $ip_online.=",'".trim($line)."'";
        else{
             $ip_online="'".trim($line)."'";
             $start=1;
        }
    }
              
}

//echo $ip_online;


?>


