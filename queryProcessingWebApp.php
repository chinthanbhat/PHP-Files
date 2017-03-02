<?php
include 'connection.php';      //Database connection file 
include 'gcm.php';
$request = $_POST;
//$_POST = json_decode($request, true);
$action=$_POST['operation'];
if(isset($action)){
        if($action=="NotArrived"){
            $status=$_POST['status'];
            $uta_id=$_POST['uta_id'];
            if($status=="Registered"){
              $updateOfflineQuery="update SCHEDULE_LIST set STATUS='Offline' where UTA_ID='$uta_id'";  
              $updateOfflineExe=mysql_query($updateOfflineQuery);
              if(!$updateOfflineExe){
                //If update Fails
                $response=Array();
			    $response['status']=0;
			    $response['message']="You have missed the Queue";
			    echo (json_encode($response));
              }
              $missedQueueMessage="Sorry! You have missed the Queue";
              $gcm=new GCM();
              $gcm_id=$gcm->getGCM($uta_id);
              if(isset($gcm_id)){
                $gcm->send($gcm_id,$missedQueueMessage);
                } 
            }
            elseif($status=="Queued"){
                $updateMissedQuery="update SCHEDULE_LIST set STATUS='Missed' where UTA_ID='$uta_id'";
                $updateMissedExe=mysql_query($updateMissedQuery);
                if(!$updateMissedExe){
                    //If Update Fails
                    $response=Array();
			        $response['status']=0;
			        $response['message']="Error! Missed Status Update";
			        echo (json_encode($response));
                }
                $getScheduleIDQuery="select SCHEDULE_ID from SCHEDULE_LIST where UTA_ID='$uta_id'";
                $getScheduleIDExe=mysql_query($getScheduleIDQuery);
                if(mysql_num_rows($getScheduleIDExe)){
                    while($row = mysql_fetch_array($getScheduleIDExe, MYSQL_NUM)) {
                        $schedule_id=$row[0];
                    }
                    $getMissedIDQuery="select max(MISSED_ID) from MISSED_QUEUE where UTA_ID='$uta_id'";
                    $getMissedIDExe=mysql_query($getMissedIDQuery);
                    if(mysql_num_rows($getMissedIDExe)){
                        while($row = mysql_fetch_array($getMissedIDExe, MYSQL_NUM)) {
                            $missed_id=$row[0];
                        }
                        $missed_id=$missed_id+1;
                        $updateMissedTable="insert into MISSED_QUEUE(UTA_ID,SCHEDULE_ID,MISSED_ID) values('$uta_id','$schedule_id','$missed_id')";
                        $updateMissedTableExe=mysql_query($updateMissedTable);
                        if($updateMissedTableExe){
                            //If update fails
                            $response=Array();
			                $response['status']=0;
			                $response['message']="Error! Missed Table Update";
			                echo (json_encode($response));
                        }
                    }
                }
                $missedQueueMessage="Sorry! You have missed the Queue";
                $gcm=new GCM();
                $gcm_id=$gcm->getGCM($uta_id);
                if(isset($gcm_id)){
                  $gcm->send($gcm_id,$missedQueueMessage);
                } 
            }
         }
         if($action=="Done"){
             $update_status=$_POST['update_status'];
             $uta_id=$_POST['uta_id'];
             $updateCompletedQuery="update SCHEDULE_LIST set STATUS='$update_status' where UTA_ID='$uta_id'";
             $updateCompletedExe=mysql_query($updateCompletedQuery);
             if(!$updateCompletedExe){
                //if update fails
                $response=Array();
			    $response['status']=0;
			    $response['message']="Error! Finished Session Update";
			    echo (json_encode($response));
             }
             else{
                 if($update_status=="Completed"){
                     $getScheduleAndReasonQuery="select ADVISOR_ID,REASON from SCHEDULE_LIST where UTA_ID='$uta_id'";
                     $getScheduleAndReasonExe=mysql_query($getScheduleAndReasonQuery);
                     if(mysql_num_rows($getScheduleAndReasonExe)){
                        while($row = mysql_fetch_array($getScheduleAndReasonExe, MYSQL_NUM)) {
                            $advisor_id=$row[0];
                            $reason=$row[1];
                        }
                     }
                     $completedTableQuery="insert into COMPLETED(UTA_ID,ADVISOR_ID,REASON) values('$uta_id','$advisor_id','$reason')";
                     $completedTableExe=mysql_query($completedTableQuery);
                     if(!$completedTableExe){
                        $response=Array();
			            $response['status']=0;
			            $response['message']="Error! Completed Table Update";
			            echo (json_encode($response));    
                     }
                     else{
                        $response=Array();
			            $response['status']=1;
			            $response['message']="Success";
			            echo (json_encode($response));    
                     }

                 }
                 $completedQueueMessage="You have finished the Advising session! All the best";
                 $gcm=new GCM();
                 $gcm_id=$gcm->getGCM($uta_id);
                 if(isset($gcm_id)){
                     $gcm->send($gcm_id,$completedQueueMessage);
                } 
             }

         }
         if($action="GetIn"){
             $GetInMessage="Please get into Advisor room ";
             $uta_id=$_POST['uta_id'];
             $getGCMIdQuery="select GCM_REGISTRATION_ID from STUDENT_DETAIL where UTA_ID='$uta_id'";
             $getGCMIdExe=mysql_query($getGCMIdQuery);
             if(mysql_num_rows($getGCMIdExe)>0){
                 while($row = mysql_fetch_array($getGCMIdExe, MYSQL_NUM)) {
                            $gcm_id=$row[0];
                        }
                 $gcm=new GCM();
                 $gcm->send($gcm_id,$GetInMessage);
             }      
         }


mysql_close();

}
?>