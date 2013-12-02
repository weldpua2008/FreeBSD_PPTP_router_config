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
$tarif_array=array(
                    '1'=>array("speed"=>"1","no_change_speed"=>true,"abonka"=>0),
                    '10'=>array("speed"=>"15","no_change_speed"=>true,"abonka"=>60),
                    '11'=>array("speed"=>"100","no_change_speed"=>false,"abonka"=>85),
                    '12'=>array("speed"=>"100","no_change_speed"=>false,"abonka"=>85),
                    '13'=>array("speed"=>"4","no_change_speed"=>true,"abonka"=>100),
                    '15'=>array("speed"=>"7","no_change_speed"=>true,"abonka"=>50),
                    '51'=>array("speed"=>"1","no_change_speed"=>true,"abonka"=>250)

                    );



$q="SELECT * FROM `users` WHERE `username`='".quote_smart($argv['5'])."' LIMIT 1";
$result = mysql_query($q);
if (!$result) {
    die('Invalid query: ' . mysql_error());
}
$num_rows = mysql_num_rows($result);
if($num_rows>0){
  //      $fp1 = fopen('/usr/local/etc/mpd5/sync_minus.txt', 'w+');
   while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            /*    if($row['tp_id']!=11)
                        $deposit=$row['deposit']+ $tarif_array[$row['tp_id']]['abonka'];
                 else 
                        $deposit=$row['deposit']; */       
            $fr_ip=trim($row['ip']);
            $user="".$row['username'];
			$speed=$row['speed'];        
			$packet=$row['tp_id'];
         
	/*
        if( ($deposit<0)&&( strlen($fr_ip)>2)  ){
                        fwrite($fp1,'/sbin/ipfw table '.$table_ipfw_for_ip_blockminus.' add '.$fr_ip." ==> user $user speed $speed row deposit: ".$row['deposit']." deposit $deposit packet $packet\n");
                       $last_line = system('/sbin/ipfw table '.$table_ipfw_for_ip_blockminus.' add '.$fr_ip."", $retval);
        }elseif( ($deposit>0)&&( strlen($fr_ip)>2)  ){
                       fwrite($fp,'/sbin/ipfw table '.$table_ipfw_for_ip_blockminus.' delete '.$fr_ip." ==> user $user speed $speed  deposit $depositdeposit $deposit\n");
                       $last_line = system('/sbin/ipfw table '.$table_ipfw_for_ip_blockminus.' delete '.$fr_ip."", $retval);
        }
        */
   }

  
//        fclose($fp);

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
                 '".$interface."',
                 '".$user."'
             )
              ON duplicate KEY UPDATE
               `all_last`='0',
               `in_last`='0',
               `out_last`='0',
               `time_last`='0',
               `all_speed`= '0',
               `interface`='".$interface."',
               `user`='".$user."' ";

            mysql_query($q);



}
?>


