#!/usr/local/bin/php
<?php

$link = mysql_connect('10.198.198.221', 'skmgroups', 'password');
$table_ipfw_for_ip_blockminus="5";
if (!$link) {
        die('Could not connect: ' . mysql_error());
}
mysql_select_db("skmgroups");


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
/*
$tarif_array=array(
                    '1'=>array("speed"=>"1","no_change_speed"=>true,"abonka"=>0),
                    '10'=>array("speed"=>"15","no_change_speed"=>true,"abonka"=>60),
                    '11'=>array("speed"=>"100","no_change_speed"=>false,"abonka"=>85),
                    '12'=>array("speed"=>"100","no_change_speed"=>false,"abonka"=>85),
                    '13'=>array("speed"=>"4","no_change_speed"=>true,"abonka"=>100),
                    '15'=>array("speed"=>"7","no_change_speed"=>true,"abonka"=>50),
                    '51'=>array("speed"=>"1","no_change_speed"=>true,"abonka"=>250)

                    );


*/
    $fp = fopen('/usr/local/etc/mpd5/down.txt', 'w+');
       foreach($argv as $t){
                           fwrite($fp,$t." ");
            }
        fclose($fp);



 $q="SELECT * FROM `users` WHERE `username`='".quote_smart($argv['5'])."' LIMIT 1";
$result = mysql_query($q);
if (!$result) {
    die('Invalid query: ' . mysql_error());
}
$num_rows = mysql_num_rows($result);
if($num_rows>0){
   while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $fr_ip=trim($row['ip']);
            $user="".$row['username'];
			$speed=$row['speed'];        
			$packet=$row['tp_id'];
   }
$interface=$argv['1'];
$link = mysql_connect('10.200.205.1', 'freenibs', 'nhfdfeljvf2006');
if (!$link) {
            die('Could not connect: ' . mysql_error());
}
/*  */
    mysql_select_db("freenibs");
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
                 '',
                 '".$user."'
             )
              ON duplicate KEY UPDATE
               `all_last`='0',
               `in_last`='0',
               `out_last`='0',
               `time_last`='0',
               `all_speed`= '0',
               `interface`='',
               `user`='".$user."' ";

            mysql_query($q);



}
?>


