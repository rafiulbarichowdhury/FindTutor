<?php
require_once __DIR__ . "/_init.php";
require_admin();
$adm = $_SESSION["admin"];
$pending=[];
$sql="SELECT c.NID, c.PassportNumber, c.TID, u.Name
      FROM credentials c JOIN user u ON u.ID=c.TID
      WHERE c.VerifiedStatus IS NULL OR c.VerifiedStatus <> 'Verified'
      ORDER BY c.TID ASC";
$res=$conn->query($sql);
while($res && ($row=$res->fetch_assoc())) $pending[]=$row;
$certs=[];
foreach($pending as $p){
  $nid=intval($p["NID"]);
  $st=$conn->prepare("SELECT Certificates FROM certificates WHERE NID=?");
  $st->bind_param("i",$nid); $st->execute(); $rs=$st->get_result();
  $certs[$nid]=[]; while($r=$rs->fetch_assoc()) $certs[$nid][]=$r["Certificates"]; $st->close();
}
?>
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Admin Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1"><link rel="stylesheet" href="styles.css" /></head>
<body>
<div class="nav container"><h2>FindTutor</h2>
  <div><span><?php echo htmlspecialchars($adm["email"]); ?></span> <a class="btn" href="auth_logout.php">Logout</a></div>
</div>
<div class="container card">
<h3>Pending Teacher Verifications</h3>
<p>Rules: NID starts <code>123</code>, Passport starts <code>1a2</code>, and two certificates must match <code>ExamName-YYYY, Roll: 123456, Board Name</code>. No duplicate verified NID/Passport allowed.</p>
<table>
  <thead><tr><th>TID</th><th>Name</th><th>NID</th><th>Passport</th><th>Certificates</th><th>Action</th></tr></thead>
  <tbody>
  <?php foreach($pending as $p): $nid=intval($p["NID"]); ?>
    <tr>
      <td><?php echo htmlspecialchars($p["TID"]); ?></td>
      <td><?php echo htmlspecialchars($p["Name"]); ?></td>
      <td><?php echo htmlspecialchars($nid); ?></td>
      <td><?php echo htmlspecialchars($p["PassportNumber"]); ?></td>
      <td><?php echo htmlspecialchars(implode("; ", $certs[$nid]??[])); ?></td>
      <td><a class="btn" href="admin_verify.php?nid=<?php echo urlencode($nid); ?>">Verify</a></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</div>
</body></html>
