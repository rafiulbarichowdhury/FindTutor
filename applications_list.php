<?php
require_once __DIR__ . "/_init.php";
require_login();
$u=current_user(); if($u["role"]!=="guardian"){ header("Location: index.php"); exit; }
$tuitionId = isset($_GET["tuition_id"])? intval($_GET["tuition_id"]) : 0;
$err="";
if($tuitionId>0){
  $st=$conn->prepare("SELECT 1 FROM tuitionjob WHERE TuitionID=? AND GID=?");
  $gid=intval($u["id"]); $st->bind_param("ii",$tuitionId,$gid); $st->execute();
  if($st->get_result()->num_rows===0){ $err="You do not own this job."; $tuitionId=0; } $st->close();
}
$myJobs=[];
if($tuitionId===0){
  $s=$conn->prepare("SELECT TuitionID, Subject FROM tuitionjob WHERE GID=? ORDER BY TuitionID DESC");
  $gid=intval($u["id"]); $s->bind_param("i",$gid); $s->execute(); $rs=$s->get_result();
  while($row=$rs->fetch_assoc()) $myJobs[]=$row; $s->close();
}
$applicants=[];
if($tuitionId>0){
  $q=$conn->prepare("SELECT a.TID, a.AppliedStatus, u.Name, u.Gender, u.`Contact Number` AS Contact, t.PreferredSubject 
                     FROM apply a JOIN user u ON u.ID=a.TID JOIN teacher t ON t.TID=a.TID
                     WHERE a.TuitionID=? ORDER BY a.AppliedStatus DESC, a.TID ASC");
  $q->bind_param("i",$tuitionId); $q->execute(); $r=$q->get_result();
  while($row=$r->fetch_assoc()) $applicants[]=$row; $q->close();
}
?>
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Applicants</title>
<meta name="viewport" content="width=device-width, initial-scale=1"><link rel="stylesheet" href="styles.css" /></head>
<body>
<div class="nav container"><h2>FindTutor</h2><div><a class="btn" href="index.php">Home</a></div></div>
<div class="container card">
<h3>Applicants</h3>
<?php if($err): ?><div class="error"><?php echo htmlspecialchars($err); ?></div><?php endif; ?>
<?php if($tuitionId===0): ?>
  <div class="notice">Select one of your jobs to view applicants.</div>
  <ul>
    <?php foreach($myJobs as $j): ?>
      <li><a href="applications_list.php?tuition_id=<?php echo urlencode($j["TuitionID"]); ?>">Job #<?php echo htmlspecialchars($j["TuitionID"]); ?> — <?php echo htmlspecialchars($j["Subject"]); ?></a></li>
    <?php endforeach; ?>
  </ul>
<?php else: ?>
  <?php if(empty($applicants)): ?><div class="notice">No one has applied yet.</div>
  <?php else: ?>
    <table><thead><tr><th>Teacher ID</th><th>Name</th><th>Gender</th><th>Contact Number</th><th>Preferred Subject</th><th>Status</th><th>Action</th></tr></thead><tbody>
    <?php foreach($applicants as $a): ?>
      <tr>
        <td><?php echo htmlspecialchars($a["TID"]); ?></td>
        <td><?php echo htmlspecialchars($a["Name"]); ?></td>
        <td><?php echo htmlspecialchars($a["Gender"]); ?></td>
        <td><?php echo htmlspecialchars($a["Contact"] ?? ""); ?></td>
        <td><?php echo htmlspecialchars($a["PreferredSubject"]); ?></td>
        <td><?php echo htmlspecialchars($a["AppliedStatus"] ?? "Applied"); ?></td>
        <td>
          <?php if(($a["AppliedStatus"] ?? "Applied")!=="Confirmed"): ?>
            <a class="btn" href="applications_update.php?action=confirm&tuition_id=<?php echo urlencode($tuitionId); ?>&tid=<?php echo urlencode($a["TID"]); ?>">Confirm</a>
          <?php else: ?><span class="badge">Confirmed</span><?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?></tbody></table>
  <?php endif; ?>
<?php endif; ?>
</div></body></html>
