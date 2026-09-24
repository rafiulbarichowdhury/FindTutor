<?php
require_once __DIR__ . "/_init.php";
require_login();
$flash_error = $_SESSION['flash_error'] ?? ''; $flash_success = $_SESSION['flash_success'] ?? ''; unset($_SESSION['flash_error'], $_SESSION['flash_success']);
$u=current_user();
$mine = isset($_GET["mine"]) ? intval($_GET["mine"]) : 0;
$sql="SELECT t.TuitionID, t.Salary, t.Timing, t.Class, t.Days, t.Subject, t.Preferred_teacher_gender, t.GID
      FROM tuitionjob t
      WHERE t.TuitionID NOT IN (SELECT TuitionID FROM apply WHERE AppliedStatus='Confirmed')";
if($mine && $u["role"]==="guardian"){ $sql.=" AND t.GID=?"; $stmt=$conn->prepare($sql); $gid=intval($u["id"]); $stmt->bind_param("i",$gid); }
else { $stmt=$conn->prepare($sql); }
$stmt->execute(); $res=$stmt->get_result(); $rows=[]; while($row=$res->fetch_assoc()) $rows[]=$row; $stmt->close();
$isTeacher = $u["role"]==="teacher"; $tid = $isTeacher ? intval($u["id"]) : 0;
$teacherGender = $isTeacher ? get_user_gender($conn,$tid) : null; $verified = $isTeacher ? is_teacher_verified($conn,$tid) : false;
$applied=[]; if($isTeacher){ $st=$conn->prepare("SELECT TuitionID FROM apply WHERE TID=?"); $st->bind_param("i",$tid); $st->execute(); $rs=$st->get_result();
  while($r=$rs->fetch_assoc()) $applied[intval($r["TuitionID"])]=true; $st->close(); }
?>
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Tuition Jobs</title>
<meta name="viewport" content="width=device-width, initial-scale=1"><link rel="stylesheet" href="styles.css" /></head>
<body>
<div class="nav container"><h2>FindTutor</h2><div><a class="btn" href="index.php">Home</a></div></div>
<div class="container card">
<?php if($flash_error): ?><div class="error"><?php echo htmlspecialchars($flash_error); ?></div><?php endif; ?>
<?php if($flash_success): ?><div class="success"><?php echo htmlspecialchars($flash_success); ?></div><?php endif; ?>
<h3>Tuition Jobs<?php if($mine && $u["role"]==="guardian") echo " — My Posts"; ?></h3>
<table><thead><tr><th>ID</th><th>Subject</th><th>Class</th><th>Days</th><th>Timing</th><th>Preferred Gender</th><th>Salary</th><th>Action</th></tr></thead>
<tbody>
<?php if(empty($rows)): ?><tr><td colspan="8">No jobs found.</td></tr>
<?php else: foreach($rows as $r): $allowed=true; if($isTeacher){ $pref=$r["Preferred_teacher_gender"]; if($pref!=="Any" && strcasecmp($pref,$teacherGender)!==0) $allowed=false; } ?>
<tr>
  <td><?php echo htmlspecialchars($r["TuitionID"]); ?></td>
  <td><?php echo htmlspecialchars($r["Subject"]); ?></td>
  <td><?php echo htmlspecialchars($r["Class"]); ?></td>
  <td><?php echo htmlspecialchars($r["Days"]); ?></td>
  <td><?php echo htmlspecialchars($r["Timing"]); ?></td>
  <td><?php echo htmlspecialchars($r["Preferred_teacher_gender"]); ?></td>
  <td><?php echo htmlspecialchars($r["Salary"]); ?></td>
  <td>
    <?php if($u["role"]==="teacher"): ?>
      <?php if(!$verified): ?><span class="badge">Verify to apply</span>
      <?php elseif(!$allowed): ?><span class="badge">Not eligible</span>
      <?php elseif(isset($applied[intval($r["TuitionID"])])): ?><span class="badge">Applied</span>
      <?php else: ?><a class="btn" href="jobs_apply.php?tid=<?php echo urlencode($r["TuitionID"]); ?>">Apply</a><?php endif; ?>
    <?php else: ?>
      <?php if($mine && intval($u["id"])===intval($r["GID"])): ?>
        <a class="btn" href="applications_list.php?tuition_id=<?php echo urlencode($r["TuitionID"]); ?>">Applicants</a>
      <?php else: ?><span class="badge">—</span><?php endif; ?>
    <?php endif; ?>
  </td>
</tr>
<?php endforeach; endif; ?>
</tbody></table>
</div></body></html>
