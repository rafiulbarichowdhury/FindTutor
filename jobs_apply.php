<?php
require_once __DIR__ . "/_init.php";
require_login();
$u=current_user();
if($u["role"]!=="teacher"){ header("Location: index.php"); exit; }
$err=""; $msg=""; $tuitionId = isset($_GET["tid"])? intval($_GET["tid"]) : 0;
if($tuitionId<=0){ $err="Invalid tuition job."; }
elseif(job_is_filled($conn,$tuitionId)){ $err="This job is already filled."; }
else{
  $tid=intval($u["id"]);
  if(!is_teacher_verified($conn,$tid)){ $err="You must be verified before applying."; }
  else{
    $teacherGender=get_user_gender($conn,$tid);
    $st=$conn->prepare("SELECT Preferred_teacher_gender FROM tuitionjob WHERE TuitionID=?");
    $st->bind_param("i",$tuitionId); $st->execute(); $res=$st->get_result();
    if(!($row=$res->fetch_assoc())){ $err="Tuition job not found."; }
    else{ $pref=$row["Preferred_teacher_gender"]; $allowed = ($pref==='Any') || (strcasecmp($pref,$teacherGender)===0);
      if(!$allowed){ $err="Not eligible due to gender preference."; }
      else{
        $ins=$conn->prepare("INSERT INTO apply (TuitionID, TID, AppliedStatus) VALUES (?, ?, 'Applied')");
        $ins->bind_param("ii",$tuitionId,$tid);
        if($ins->execute()){ $_SESSION['flash_success'] = 'Applied successfully to job #'.$tuitionId.'.'; header('Location: jobs_list.php'); exit; } else { $_SESSION['flash_error'] = ($conn->errno==1062 ? 'You already applied.' : ('Failed to apply: '.$conn->error)); header('Location: jobs_list.php'); exit; }
        $ins->close();
      }
    } $st->close();
  }
}
?>
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Apply</title>
<meta name="viewport" content="width=device-width, initial-scale=1"><link rel="stylesheet" href="styles.css" /></head>
<body>
<div class="nav container"><h2>FindTutor</h2><div><a class="btn" href="jobs_list.php">Back</a></div></div>
<div class="container card">
<?php if($err): ?><div class="error"><?php echo htmlspecialchars($err); ?></div>
<?php else: ?><div class="success"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
</div></body></html>
