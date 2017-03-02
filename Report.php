<?php

define('DB_USER', ""); // db user
define('DB_PASSWORD', ""); // db password (mention your db password here)
define('DB_DATABASE', "BeaconLog"); // database name
define('DB_SERVER', "mydbinstance1.com52r4wudge.us-west-2.rds.amazonaws.com"); // db server
/*
 * Following code will create a new product row
 * All product details are read from HTTP Post Request
 */

// Connecting to mysql database
$con = mysql_connect(DB_SERVER, DB_USER, DB_PASSWORD) or die(mysql_error());

// Selecing database
$db = mysql_select_db(DB_DATABASE) or die(mysql_error()) or die(mysql_error());
$response = array();
if (isset($_GET['Device_Name']) && isset($_GET['Device_Address']) && isset($_GET['Device_RSSI']) && isset($_GET['TimeStamp']) && isset($_GET['URL'])) {
	$dname = $_GET['Device_Name'];
	$daddr = $_GET['Device_Address'];
    $drssi = $_GET['Device_RSSI'];
    $ts = $_GET['TimeStamp'];
    $url = $_GET['URL'];
    // mysql inserting a new row
	$pwd=md5($pwd);
    $sql = "INSERT INTO SensorTag(DeviseName,DeviseAddr,DeviseRSSI,TStamp,url) VALUES('$dname','$daddr','$drssi','$ts','$url')";
	$result=mysql_query($sql) or die(mysql_error());
	/*$num_rows=mysql_num_rows($result);
	
	//echo $activation_key;
	if($num_rows==1)
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		$fname=$row['user_fname'];
		$lname=$row['user_lname'];
		$user=$row['user_ID'];
		if($user != NULL){		
	    	/*$sql1 = "SELECT device_ID FROM hwt_owner WHERE user_ID='$user';";
			$result1=mysql_query($sql1) or die(mysql_error());
			$num_rows1=mysql_num_rows($result1);
			$row1 = mysql_fetch_array($result1, MYSQL_ASSOC);
			$deviceid=$row1['device_ID'];
		if($fname!=NULL && $lname!=NULL)
		{
			$response["success"]=1;
			$response["user_id"]=$user;
			$response["device"]=$deviceid;
			$response["message"] = "successful.";

	        // echoing JSON response
	        echo json_encode($response);
		}
		else{
	        $response["success"] = 0;
	        $response["message"] = "Oops! An error occurred1.";
			echo json_encode($response);
		}
	}
	}else{
        $response["success"] = 0;
        $response["message"] = "Oops! An error occurred2.";
    
        // echoing JSON response
        echo json_encode($response);
	}
}else{
    $response["success"] = 0;
    $response["message"] = "Oops! An error occurred3.";

    // echoing JSON response
    echo json_encode($response);
}	*/
    echo $result;
}*/
mysqli_query($connection, 
     "CALL ReportGen('2016-04-29 12:45:00','2016-04-29 13:00:00')") 
?>