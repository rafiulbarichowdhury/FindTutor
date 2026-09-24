<?php
require_once __DIR__ . "/_init.php";
require_login();
$u=current_user(); if($u["role"]!=="teacher"){ header("Location: index.php"); exit; }
$tid=intval($u["id"]); $err=""; $msg=""; $cred=null;
$st=$conn->prepare("SELECT NID, PassportNumber, VerifiedStatus FROM credentials WHERE TID=?");
$st->bind_param("i",$tid); $st->execute(); $r=$st->get_result(); if($row=$r->fetch_assoc()) $cred=$row; $st->close();
$certs=[]; if($cred && $cred["NID"]){ $nid_i=intval($cred["NID"]); $cs=$conn->prepare("SELECT Certificates FROM certificates WHERE NID=?");
  $cs->bind_param("i",$nid_i); $cs->execute(); $cr=$cs->get_result(); while($c=$cr->fetch_assoc()) $certs[]=$c["Certificates"]; $cs->close(); }
if($_SERVER["REQUEST_METHOD"]==="POST"){
  $nid=trim($_POST["nid"]??""); $passport=trim($_POST["passport"]??""); $cert1=trim($_POST["cert1"]??""); $cert2=trim($_POST["cert2"]??"");
  if(!preg_match('/^123\d{7}$/',$nid)){ $err="NID must be 10 digits and start with 123."; }
  elseif($passport!=="" && !preg_match('/^1a2/i',$passport)){ $err="Passport must start with 1a2."; }
  else{
    $pat='/^.+-\d{4},\s*Roll:\s*\d+,\s*.+$/';
    if(!preg_match($pat,$cert1) || !preg_match($pat,$cert2)){ $err='Certificates must match: ExamName-YYYY, Roll: 123456, Board Name'; }
    else{
      if($cred){
        $up=$conn->prepare("UPDATE credentials SET PassportNumber=?, VerifiedStatus=NULL, AdminID=NULL, NID=? WHERE TID=?");
        $nid_i=intval($nid); $up->bind_param("sii",$passport,$nid_i,$tid); $ok=$up->execute(); $up->close(); if(!$ok){ $err="Failed to update credentials: ".$conn->error; }
      } else {
        $ins=$conn->prepare("INSERT INTO credentials (NID, PassportNumber, AdminID, TID, VerifiedStatus) VALUES (?, ?, NULL, ?, NULL)");
        $nid_i=intval($nid); $ins->bind_param("isi",$nid_i,$passport,$tid); $ok=$ins->execute(); $ins->close(); if(!$ok){ $err="Failed to save credentials: ".$conn->error; }
      }
      if($err===""){
        $del=$conn->prepare("DELETE FROM certificates WHERE NID=?"); $del->bind_param("i",$nid_i); $del->execute(); $del->close();
        foreach([$cert1,$cert2] as $cval){ $ci=$conn->prepare("INSERT INTO certificates (Certificates, NID) VALUES (?, ?)"); $ci->bind_param("si",$cval,$nid_i); $ci->execute(); $ci->close(); }
        $msg="Credentials submitted. Await admin verification."; $cred=["NID"=>$nid_i,"PassportNumber"=>$passport,"VerifiedStatus"=>null]; $certs=[$cert1,$cert2];
      }
    }
  }
}
?>
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Teacher Credentials</title>
<meta name="viewport" content="width=device-width, initial-scale=1"><link rel="stylesheet" href="styles.css" /></head>
<body>
<div class="nav container"><h2>FindTutor</h2><div><a class="btn" href="index.php">Home</a></div></div>
<div class="container card">
<h3>Submit Credentials</h3>
<?php if($err): ?><div class="error"><?php echo htmlspecialchars($err); ?></div><?php endif; ?>
<?php if($msg): ?><div class="success"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
<form method="post">
  <label>NID (must start with 123, 10 digits)* <input type="text" name="nid" value="<?php echo htmlspecialchars($cred['NID']??''); ?>" required></label>
  <label>Passport (must start with 1a2)* <input type="text" name="passport" value="<?php echo htmlspecialchars($cred['PassportNumber']??''); ?>" required></label>
  <label>Certificate #1* <input type="text" name="cert1" placeholder="ExamName-YYYY, Roll: 123456, Board Name" required></label>
  <label>Certificate #2* <input type="text" name="cert2" placeholder="ExamName-YYYY, Roll: 123456, Board Name" required></label>
  <button class="btn" type="submit">Save Credentials</button>
</form>
<?php if(!empty($certs)): ?>
  <h4>Your submitted certificates</h4>
  <ul><?php foreach($certs as $c): ?><li><?php echo htmlspecialchars($c); ?></li><?php endforeach; ?></ul>
  <p>Verification status: <strong><?php echo htmlspecialchars($cred["VerifiedStatus"] ?? "Pending"); ?></strong></p>
<?php endif; ?>
</div></body></html>
