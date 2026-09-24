<?php
require_once __DIR__ . "/_init.php";
if (is_logged_in()) { header("Location: index.php"); exit; }
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");
    if ($email === "" || $password === "") {
        $error = "Please enter both email and password.";
    } else {
        $stmt = $conn->prepare("SELECT ID, Name FROM user WHERE Email=? AND Password=?");
        $stmt->bind_param("ss",$email,$password); $stmt->execute(); $res=$stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $id=intval($row["ID"]); $name=$row["Name"]; $role=null;
            $t=$conn->prepare("SELECT 1 FROM teacher WHERE TID=? LIMIT 1"); $t->bind_param("i",$id); $t->execute();
            if($t->get_result()->num_rows>0) $role="teacher"; $t->close();
            if($role===null){ $g=$conn->prepare("SELECT 1 FROM guardian WHERE GID=? LIMIT 1"); $g->bind_param("i",$id); $g->execute();
                if($g->get_result()->num_rows>0) $role="guardian"; $g->close(); }
            if($role===null){ $error="Your account exists but has no role."; }
            else{ $_SESSION["user"]=["id"=>$id,"name"=>$name,"role"=>$role]; header("Location: index.php"); exit; }
        } else { $error = "Invalid email or password."; }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Login — FindTutor</title>
<meta name="viewport" content="width=device-width, initial-scale=1"><link rel="stylesheet" href="styles.css" /></head>
<body>
<div class="nav container"><h2>FindTutor</h2><div><a class="btn" href="index.php">Home</a></div></div>
<div class="container card">
<h3>User Login</h3>
<?php if($error): ?><div class="error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
<form method="post">
  <label>Email <input type="email" name="email" required></label>
  <label>Password <input type="password" name="password" required></label>
  <button class="btn" type="submit">Login</button>
</form>
</div></body></html>
