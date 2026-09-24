<?php
require_once __DIR__ . "/_init.php";
require_login();
$u=current_user(); if($u["role"]!=="guardian"){ header("Location: index.php"); exit; }
$action=$_GET["action"]??""; $tuitionId=isset($_GET["tuition_id"])?intval($_GET["tuition_id"]):0; $tid=isset($_GET["tid"])?intval($_GET["tid"]):0;
if($action==="confirm" && $tuitionId>0 && $tid>0){
  $st=$conn->prepare("SELECT 1 FROM tuitionjob WHERE TuitionID=? AND GID=?"); $gid=intval($u["id"]); $st->bind_param("ii",$tuitionId,$gid); $st->execute();
  $owns=$st->get_result()->num_rows>0; $st->close();
  if($owns && !job_is_filled($conn,$tuitionId)){
    $u1=$conn->prepare("UPDATE apply SET AppliedStatus='Confirmed' WHERE TuitionID=? AND TID=?"); $u1->bind_param("ii",$tuitionId,$tid); $u1->execute(); $u1->close();
    $u2=$conn->prepare("UPDATE apply SET AppliedStatus='Rejected' WHERE TuitionID=? AND TID<>? AND (AppliedStatus IS NULL OR AppliedStatus='Applied')");
    $u2->bind_param("ii",$tuitionId,$tid); $u2->execute(); $u2->close();
  }
}
header("Location: applications_list.php?tuition_id=".urlencode($tuitionId)); exit;
