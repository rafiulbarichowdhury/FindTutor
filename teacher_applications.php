<?php
require_once __DIR__ . "/_init.php";
require_login();
$u=current_user(); if($u["role"]!=="teacher"){ header("Location: index.php"); exit; }
$tid=intval($u["id"]); $apps=[];
$stmt=$conn->prepare("SELECT a.TuitionID, a.AppliedStatus, tj.Subject, tj.Class FROM apply a JOIN tuitionjob tj ON tj.TuitionID=a.TuitionID WHERE a.TID=? ORDER BY a.TuitionID DESC");
$stmt->bind_param("i",$tid); $stmt->execute(); $res=$stmt->get_result(); while($row=$res->fetch_assoc()) $apps[]=$row; $stmt->close();
?>
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>My Applications</title>
<meta name="viewport" content="width=device-width, initial-scale=1"><link rel="stylesheet" href="styles.css" /></head>
<body>
<div class="nav container"><h2>FindTutor</h2><div><a class="btn" href="index.php">Home</a></div></div>
<div class="container card">
<h3>My Applications</h3>
<table><thead><tr><th>Tuition ID</th><th>Subject</th><th>Class</th><th>Status</th></tr></thead><tbody>
<?php if(empty($apps)): ?><tr><td colspan="4">No applications yet.</td></tr>
<?php else: foreach($apps as $a): $s=$a["AppliedStatus"]??"Applied"; ?>
<tr><td><?php echo htmlspecialchars($a["TuitionID"]); ?></td><td><?php echo htmlspecialchars($a["Subject"]); ?></td><td><?php echo htmlspecialchars($a["Class"]); ?></td><td><?php echo htmlspecialchars($s); ?></td></tr>
<?php endforeach; endif; ?>
</tbody></table>
</div></body></html>
