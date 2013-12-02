#!/usr/local/bin/php
<?php
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



$q="SELECT * FROM `users`"; // WHERE `user`='".quote_smart($argv['5'])."'";
$result = mysql_query($q,$link);
if (!$result) {
    die('Invalid query: ' . mysql_error());
}
$num_rows = mysql_num_rows($result);
if($num_rows>0){

        $fp1 = fopen('/usr/local/etc/mpd5/sync_minus.txt', 'w+');
        $fp = fopen('/usr/local/etc/mpd5/sync_plus.txt','w+');

        $fp1_skm = fopen('/usr/local/etc/mpd5/sync_skm_minus.txt', 'w+');
        $fp_skm = fopen('/usr/local/etc/mpd5/sync_skm_plus.txt','w+');



$link2 = mysql_connect('10.200.205.1', 'freenibs', 'nhfdfeljvf2006');
if (!$link2) {
            die('Could not connect: ' . mysql_error());
}
/*  */
mysql_select_db("freenibs",$link2);



   while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                if($row['tp_id']!=11)
                        $deposit=$row['deposit']+ $tarif_array[$row['tp_id']]['abonka'];
                 else 
                        $deposit=$row['deposit'];       
                        $fr_ip=trim($row['ip']);
			$user=$row['username'];
			$speed=$row['speed'];        
			$packet=$row['tp_id'];
        $q_skm="SELECT `framed_ip` FROM `users` WHERE `user`='".$user."'";
        $skm_res=mysql_query($q_skm,$link2);
        while ($row = mysql_fetch_assoc($skm_res)){
            $skm_fr_ip=$row['framed_ip'];               
        }
	
        if( ($deposit<0)&&( strlen($fr_ip)>2)  ){
                        fwrite($fp1,'/sbin/ipfw table '.$table_ipfw_for_ip_blockminus.' add '.$fr_ip." ==> user $user speed $speed row deposit: ".$row['deposit']." deposit $deposit packet $packet\n");
                       $last_line = system('/sbin/ipfw table '.$table_ipfw_for_ip_blockminus.' add '.$fr_ip."", $retval);


                        fwrite($fp1_skm,'/sbin/ipfw table '.$table_ipfw_for_ip_blockminus.' add '.$skm_fr_ip." ==> user $user speed $speed row deposit: ".$row['deposit']." deposit $deposit packet $packet\n");
                       $last_line = system('/sbin/ipfw table '.$table_ipfw_for_ip_blockminus.' add '.$skm_fr_ip."", $retval);
 
        
        }elseif( ($deposit>0)&&( strlen($fr_ip)>2)  ){
                       fwrite($fp,'/sbin/ipfw table '.$table_ipfw_for_ip_blockminus.' delete '.$fr_ip." ==> user $user speed $speed  deposit $depositdeposit $deposit\n");
                       $last_line = system('/sbin/ipfw table '.$table_ipfw_for_ip_blockminus.' delete '.$fr_ip."", $retval);

       
                        fwrite($fp_skm,'/sbin/ipfw table '.$table_ipfw_for_ip_blockminus.' delete '.$skm_fr_ip." ==> user $user speed $speed  deposit $depositdeposit $deposit\n");
                       $last_line = system('/sbin/ipfw table '.$table_ipfw_for_ip_blockminus.' delete '.$skm_fr_ip."", $retval);

 
       
        }
  }
//  print_r($tarif_array[1]);
//  print_r(
        fclose($fp);
	fclose($fp1);

}
?>


