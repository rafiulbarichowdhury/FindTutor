<?php
require_once __DIR__ . "/_init.php";
require_login();
$u=current_user(); $uid=intval($u["id"]);
$counterparts=[];
if($u["role"]==="guardian"){
  $q=$conn->prepare("SELECT DISTINCT a.TID AS id, u.Name FROM apply a JOIN tuitionjob tj ON tj.TuitionID=a.TuitionID JOIN user u ON u.ID=a.TID WHERE tj.GID=? AND a.AppliedStatus='Confirmed'");
  $q->bind_param("i",$uid); $q->execute(); $r=$q->get_result(); while($row=$r->fetch_assoc()) $counterparts[]=["id"=>intval($row["id"]),"name"=>$row["Name"]]; $q->close();
}else{
  $q=$conn->prepare("SELECT DISTINCT tj.GID AS id, u.Name FROM apply a JOIN tuitionjob tj ON tj.TuitionID=a.TuitionID JOIN user u ON u.ID=tj.GID WHERE a.TID=? AND a.AppliedStatus='Confirmed'");
  $q->bind_param("i",$uid); $q->execute(); $r=$q->get_result(); while($row=$r->fetch_assoc()) $counterparts[]=["id"=>intval($row["id"]),"name"=>$row["Name"]]; $q->close();
}
$err=""; $msg="";
if($_SERVER["REQUEST_METHOD"]==="POST"){
  $target=intval($_POST["target_id"]??0); $rating=intval($_POST["rating"]??0); $comment=trim($_POST["comment"]??"");
  if($target<=0 || $rating<1 || $rating>5){ $err="Select a valid counterpart and rating (1-5)."; }
  else{
    $serial = time(); 
    $ins1=$conn->prepare("INSERT INTO review (SerialNo, Description, Rating) VALUES (?, ?, ?)"); $ins1->bind_param("isi",$serial,$comment,$rating);
    if($ins1->execute()){
      $ins2=$conn->prepare("INSERT INTO userreview (SerialNo, ID) VALUES (?, ?)"); $ins2->bind_param("ii",$serial,$target);
      if($ins2->execute()) $msg="Review submitted."; else $err="Failed to link review.";
      $ins2->close();
    } else { $err="Failed to save review."; }
    $ins1->close();
  }
}
?>
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Write a Review</title>
<meta name="viewport" content="width=device-width, initial-scale=1"><link rel="stylesheet" href="styles.css" /></head>
<body>
<div class="nav container"><h2>FindTutor</h2><div><a class="btn" href="index.php">Home</a></div></div>
<div class="container card">
<h3>Write a Review</h3>
<?php if($err): ?><div class="error"><?php echo htmlspecialchars($err); ?></div><?php endif; ?>
<?php if($msg): ?><div class="success"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
<?php if(empty($counterparts)): ?><p>Not eligible (requires a confirmed job).</p>
<?php else: ?>
<form method="post">
  <label>Choose <?php echo ($u["role"]==="guardian"?"Teacher":"Guardian"); ?>
    <select name="target_id" required>
      <option value="">-- select --</option>
      <?php foreach($counterparts as $c): ?><option value="<?php echo $c["id"]; ?>"><?php echo htmlspecialchars($c["name"]); ?> (ID: <?php echo $c["id"]; ?>)</option><?php endforeach; ?>
    </select>
  </label>
  <label>Rating (1-5) <input type="number" name="rating" min="1" max="5" required></label>
  <label>Comment <textarea name="comment" rows="4" placeholder="Optional"></textarea></label>
  <button class="btn" type="submit">Submit Review</button>
</form>
<?php endif; ?>
</div></body></html>
