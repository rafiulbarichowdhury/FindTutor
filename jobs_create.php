<?php
require_once __DIR__ . "/_init.php";
require_login();
$u=current_user(); if($u["role"]!=="guardian"){ header("Location: index.php"); exit; }
$err=""; $msg="";
function unique_tuition_id(mysqli $conn){
  $id = time();
  $st=$conn->prepare("SELECT 1 FROM tuitionjob WHERE TuitionID=?"); $st->bind_param("i",$id); $st->execute();
  if($st->get_result()->num_rows>0){ $id = time()+rand(1,9999); } $st->close();
  return $id;
}
if($_SERVER["REQUEST_METHOD"]==="POST"){
  $salary=intval($_POST["salary"]??0); $timing=trim($_POST["timing"]??"");
  $class=trim($_POST["class"]??""); $days=trim($_POST["days"]??""); $subject=trim($_POST["subject"]??"");
  $pref=trim($_POST["preferred_teacher_gender"]??"");
  if($salary<=0||$timing===""||$class===""||$days===""||$subject===""||$pref===""){ $err="Please fill all fields."; }
  else{
    $tid=unique_tuition_id($conn); $gid=intval($u["id"]);
    $st=$conn->prepare("INSERT INTO tuitionjob (TuitionID, Salary, Timing, Class, Days, Subject, Preferred_teacher_gender, GID) VALUES (?,?,?,?,?,?,?,?)");
    $st->bind_param("iisssssi",$tid,$salary,$timing,$class,$days,$subject,$pref,$gid);
    if($st->execute()) $msg="Tuition job posted (#".$tid.")"; else $err="Failed: ".$conn->error; $st->close();
  }
}
?>
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Post Tuition Job</title>
<meta name="viewport" content="width=device-width, initial-scale=1"><link rel="stylesheet" href="styles.css" /></head>
<body>
<div class="nav container"><h2>FindTutor</h2><div><a class="btn" href="index.php">Home</a></div></div>
<div class="container card">
<h3>Post a tuition job</h3>
<?php if($err): ?><div class="error"><?php echo htmlspecialchars($err); ?></div><?php endif; ?>
<?php if($msg): ?><div class="success"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
<form method="post">
  <label>Salary (BDT)* <input type="number" name="salary" min="1" required></label>
  <label>Timing* <input type="text" name="timing" required></label>
  <label>Class* <input type="text" name="class" required></label>
  <label>Days* <input type="text" name="days" required></label>
  <label>Subject* <input type="text" name="subject" required></label>
  <label>Preferred teacher gender*
    <select name="preferred_teacher_gender" required>
      <option value="">-- select --</option><option>Any</option><option>Male</option><option>Female</option>
    </select>
  </label>
  <button class="btn" type="submit">Post Job</button>
</form>
</div></body></html>
