<?php
require_once __DIR__ . "/_init.php";
$err=""; $msg="";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name=trim($_POST["name"]??""); $email=trim($_POST["email"]??"");
  $password=trim($_POST["password"]??""); $contact=trim($_POST["contact"]??"");
  $gender=trim($_POST["gender"]??""); $location=trim($_POST["location"]??"");
  $role=trim($_POST["role"]??""); $subject=trim($_POST["subject"]??""); $institution=trim($_POST["institution"]??"");

  if($name===""||$email===""||$password===""||$contact===""||$gender===""||$location===""||$role===""){
    $err="Please fill all required fields.";
  } elseif(!in_array($role,["teacher","guardian"])) {
    $err="Invalid role.";
  } elseif($role==="teacher" && ($subject===""||$institution==="")){
    $err="Preferred subject and Current Institution are required for teachers.";
  } else {
  
    $q=$conn->prepare("SELECT 1 FROM user WHERE Email=? LIMIT 1");
    $q->bind_param("s",$email); $q->execute();
    if($q->get_result()->num_rows>0){ $err="Email already registered."; }
    $q->close();

    if($err===""){

      $res = $conn->query("SELECT COALESCE(MAX(ID), 1000000) AS mx FROM user");
      $row = $res ? $res->fetch_assoc() : null;
      $newId = (int) (($row && $row['mx'] !== null) ? $row['mx'] : 1000000) + 1;

      $inserted = false;
      $attempts = 0;
      while ($attempts < 5 && !$inserted) {
        $stmt=$conn->prepare("INSERT INTO user (ID, Name, Email, Gender, Location, Password, `Contact Number`) VALUES (?,?,?,?,?,?,?)");
        $stmt->bind_param("issssss",$newId,$name,$email,$gender,$location,$password,$contact);
        if ($stmt->execute()) {
          $inserted = true;
        } else {
          if ($conn->errno == 1062) { 
            $newId++;
            $attempts++;
            $stmt->close();
            continue;
          } else {
            $err = "Failed to create user: ".$conn->error;
          }
        }
        $stmt->close();
      }
    
      if (!$inserted && $err==="") {
        $err = "Failed to create user: ID conflict. Please try again.";
      }
      // -----------------------------------------------

      if($err===""){
        if($role==="teacher"){
          $t=$conn->prepare("INSERT INTO teacher (TID, PreferredSubject, `Current Institution`) VALUES (?,?,?)");
          $t->bind_param("iss",$newId,$subject,$institution);
          if(!$t->execute()){

            $conn->query("DELETE FROM user WHERE ID=".$newId);
            $err="User created, but failed to save teacher info: ".$conn->error;
          }
          $t->close();
        } else { 
          $g=$conn->prepare("INSERT INTO guardian (GID) VALUES (?)");
          $g->bind_param("i",$newId);
          if(!$g->execute()){

            $conn->query("DELETE FROM user WHERE ID=".$newId);
            $err="User created, but failed to save guardian info: ".$conn->error;
          }
          $g->close();
        }
        if($err===""){ $msg="Registration successful. Please login."; }
      }
    }
  }
}
?>
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Register — FindTutor</title>
<meta name="viewport" content="width=device-width, initial-scale=1"><link rel="stylesheet" href="styles.css" />
<script>
function toggleFields(){ const r=document.getElementById('role').value; document.getElementById('teacherFields').style.display=(r==='teacher')?'block':'none'; }
</script></head>
<body onload="toggleFields()">
<div class="nav container"><h2>FindTutor</h2><div><a class="btn" href="index.php">Home</a></div></div>
<div class="container card">
<h3>Create an account</h3>
<?php if($err): ?><div class="error"><?php echo htmlspecialchars($err); ?></div><?php endif; ?>
<?php if($msg): ?><div class="success"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
<form method="post">
  <label>Full Name* <input type="text" name="name" required></label>
  <label>Email* <input type="email" name="email" required></label>
  <label>Password* <input type="password" name="password" required></label>
  <label>Contact Number* <input type="text" name="contact" required></label>
  <label>Gender*
    <select name="gender" required>
      <option value="">-- select --</option>
      <option>Male</option><option>Female</option>
    </select>
  </label>
  <label>Address (Location)* <input type="text" name="location" required></label>
  <label>Register as*
    <select name="role" id="role" required onchange="toggleFields()">
      <option value="guardian">Guardian</option>
      <option value="teacher">Teacher</option>
    </select>
  </label>
  <div id="teacherFields" style="display:none;">
    <label>Preferred Subject* <input type="text" name="subject"></label>
    <label>Current Institution* <input type="text" name="institution"></label>
    <div class="notice">After registering as a teacher, go to <strong>Credentials</strong> to submit your certificates and get verified.</div>
  </div>
  <button class="btn" type="submit">Register</button>
</form>
</div></body></html>
