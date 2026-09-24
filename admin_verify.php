<?php
require_once __DIR__ . "/_init.php";
require_admin();
$adm = $_SESSION["admin"];
$nid = isset($_GET["nid"])? intval($_GET["nid"]) : 0;
if($nid>0){
  $s=$conn->prepare("SELECT NID, PassportNumber, TID FROM credentials WHERE NID=?");
  $s->bind_param("i",$nid); $s->execute(); $res=$s->get_result();
  if($cred=$res->fetch_assoc()){
    $passport=$cred["PassportNumber"]??""; $tid=intval($cred["TID"]);
    if(!preg_match('/^123\d{7}$/',(string)$nid)){ $msg="NID format invalid."; }
    elseif(!preg_match('/^1a2/i',$passport)){ $msg="Passport prefix invalid."; }
    else{
      $have=[]; $st=$conn->prepare("SELECT Certificates FROM certificates WHERE NID=?");
      $st->bind_param("i",$nid); $st->execute(); $rs=$st->get_result(); while($r=$rs->fetch_assoc()) $have[]=$r["Certificates"]; $st->close();
      $pat='/^.+-\d{4},\s*Roll:\s*\d+,\s*.+$/';
      if(count($have)<2 || !preg_match($pat,$have[0]) || !preg_match($pat,$have[1])){
        $msg="Certificates missing or do not match required format.";
      } else {
        $c1=$conn->prepare("SELECT 1 FROM credentials WHERE NID=? AND VerifiedStatus='Verified' AND TID<>? LIMIT 1");
        $c1->bind_param("ii",$nid,$tid); $c1->execute(); $dupeN=$c1->get_result()->num_rows>0; $c1->close();
        $c2=$conn->prepare("SELECT 1 FROM credentials WHERE PassportNumber=? AND VerifiedStatus='Verified' AND TID<>? LIMIT 1");
        $c2->bind_param("si",$passport,$tid); $c2->execute(); $dupeP=$c2->get_result()->num_rows>0; $c2->close();
        if($dupeN){ $msg="This NID is already verified for another teacher."; }
        elseif($dupeP){ $msg="This passport number is already verified for another teacher."; }
        else{
          $up=$conn->prepare("UPDATE credentials SET VerifiedStatus='Verified', AdminID=? WHERE NID=?");
          $aid=intval($adm["id"]); $up->bind_param("ii",$aid,$nid); $up->execute(); $up->close();
          $msg="Verified successfully.";
        }
      }
    }
  } else { $msg="Credentials not found."; }
  $s->close();
}
header("Location: admin_dashboard.php"); exit;
