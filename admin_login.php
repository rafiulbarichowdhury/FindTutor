<?php
require_once __DIR__ . "/_init.php";
if (is_admin()) { header("Location: admin_dashboard.php"); exit; }
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email=trim($_POST["email"]??""); $password=trim($_POST["password"]??"");
  if($email===""||$password===""){ $error="Please enter email and password."; }
  else{
    $stmt=$conn->prepare("SELECT AdminID, Email FROM admin WHERE Email=? AND `Admin Password`=?");
    $stmt->bind_param("ss",$email,$password); $stmt->execute(); $res=$stmt->get_result();
    if($row=$res->fetch_assoc()){ $_SESSION["admin"]=["id"=>intval($row["AdminID"]),"email"=>$row["Email"]]; header("Location: admin_dashboard.php"); exit; }
    else{ $error="Invalid admin credentials."; }
    $stmt->close();
  }
}
?>
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Admin Login — FindTutor</title>
<meta name="viewport" content="width=device-width, initial-scale=1"><link rel="stylesheet" href="styles.css" /></head>
<body>
<div class="nav container"><h2>FindTutor</h2><div><a class="btn" href="index.php">Home</a></div></div>
<div class="container card">
<h3>Admin Login</h3>
<?php if($error): ?><div class="error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
<form method="post">
  <label>Email <input type="email" name="email" required></label>
  <label>Password <input type="password" name="password" required></label>
  <button class="btn" type="submit">Login</button>
</form>
</div></body></html>
