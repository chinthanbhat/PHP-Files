<?php
//$new=mysql_query("insert into STUDENT_DETAIL(NAME,UTA_ID,MOBILE_NUMBER) values('asdsdas','1001243532','98765')");
if($_POST){
include 'connection.php';      //Database connection file 
$request = $_POST;
//print_r($request);
//$new=mysql_query("insert into STUDENT_DETAIL(NAME,UTA_ID,MOBILE_NUMBER) values('$request','1001243532','98765')");
//$_POST = json_decode($request, true);
$action=$_POST['operation'];
echo $action;
if(isset($action)){
	if($action=="Register")
	{
		$name=$_POST['name'];
		$uta_id=$_POST['uta_id'];
		$mobile_number=$_POST['mobile_number'];
		$reason=$_POST['reason'];
		$advisor_id=$_POST['advisor_id'];
		$gcm_id=$_POST['token'];
		$token_number=0;
		$status="Registered";                      //Registered, Queued,missed, Completed & InProgress ,offline
		$registerUserQuery="insert into STUDENT_DETAIL(NAME,UTA_ID,MOBILE_NUMBER,GCM_REGISTRATION_ID) values('$name','$uta_id','$mobile_number','$gcm_id')";
		$registerUserExe=mysql_query($registerUserQuery);
		$registerScheduleListQuery="insert into SCHEDULE_LIST(UTA_ID,ADVISOR_ID,REASON,STATUS,TOKEN_NUMBER) values('$uta_id','$advisor_id','$reason','$status','$token_number')";
		$registerScheduleListExe=mysql_query($registerScheduleListQuery);	
		if($registerUserExe && $registerScheduleListExe){
			//if registered succesfully
			$response=Array();
			$response['status']=1;
			$response['message']="Success";
			echo (json_encode($response));
		}
		else{
			$response=Array();
			$response['status']=0;
			$response['message']="Registration failed! Please try again later";
			echo (json_encode($response));
		}
	}
	if($action=="AddMeToQueue"){
		$uta_id=$_POST['uta_id'];
		$checkQueueingIDQuery="select SCHEDULE_ID from SCHEDULE_LIST where UTA_ID='$uta_id'";
		$addMeToQueueQuery="update SCHEDULE_LIST set STATUS='Queued' where UTA_ID='$uta_id'";
		$result=mysql_query($checkQueueingIDQuery);
		if(mysql_num_rows($result)){
			$addMeToQueueExe=mysql_query($addMeToQueueQuery);
			//result handling to update each time or just a single time
			$response=Array();
			$response['status']=1;
			$response['message']="Success";
			echo (json_encode($response));
		}
		else{
			//handlimg for :register first
			$response=Array();
			$response['status']=0;
			$response['message']="Please Register and try again";
			echo (json_encode($response));
		}
	}
	if($action=="RealTimeQueue"){
		$realTimeQueueQuery="select SCHEDULE_ID,UTA_ID,STATUS from SCHEDULE_LIST where STATUS is 'Queued' and STATUS is 'Registered'";
		$realTimeQueueExe=mysql_query($realTimeQueueQuery);
		if(!$realTimeQueueExe){
			//Query returns FALSE
			$response=Array();
			$response['status']=0;
			$response['message']="Oops sorry!! Please try again";
			echo (json_encode($response));
		}
		else{
			if(mysql_num_rows($realTimeQueueExe)!=0){
				//send result to Android App
				$response=Array();
				while($row = mysql_fetch_array($realTimeQueueExe, MYSQL_NUM)) {
                        $temp=Array();
						$temp['schedule_id']=$row[0];
						$temp['uta_id']=$row[1];
						$temp['status']=$row[2];
						array_push($response,$temp);
                    }
				$response=Array();
				$response['status']=1;
				$response['message']="Success";	
				echo (json_encode($response));
			}
			else{
				//If there is no one waiting
				$response=Array();
				$response['status']=0;
				$response['message']="Queue is Empty";
				echo (json_encode($response));
			}
		}
	}
}

mysql_close();

}
?>