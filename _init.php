<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . "/DBconnect.php";

function is_logged_in(){ return isset($_SESSION["user"]); }
function current_user(){ return $_SESSION["user"] ?? null; }
function require_login(){ if(!is_logged_in()){ header("Location: auth_login.php"); exit; } }

function is_admin(){ return isset($_SESSION["admin"]); }
function require_admin(){ if(!is_admin()){ header("Location: admin_login.php"); exit; } }

function is_teacher_verified(mysqli $conn, int $tid){
    $stmt=$conn->prepare("SELECT VerifiedStatus FROM credentials WHERE TID=? LIMIT 1");
    $stmt->bind_param("i",$tid); $stmt->execute(); $res=$stmt->get_result();
    $ok=false; if($row=$res->fetch_assoc()){ $ok = strcasecmp($row["VerifiedStatus"]??"", "Verified")==0; }
    $stmt->close(); return $ok;
}
function get_user_gender(mysqli $conn, int $id){
    $stmt=$conn->prepare("SELECT Gender FROM user WHERE ID=?");
    $stmt->bind_param("i",$id); $stmt->execute(); $r=$stmt->get_result();
    $g=null; if($row=$r->fetch_assoc()) $g=$row["Gender"]; $stmt->close(); return $g;
}
function job_is_filled(mysqli $conn, int $tuitionId){
    $stmt=$conn->prepare("SELECT 1 FROM apply WHERE TuitionID=? AND AppliedStatus='Confirmed' LIMIT 1");
    $stmt->bind_param("i",$tuitionId); $stmt->execute();
    $filled = $stmt->get_result()->num_rows>0; $stmt->close(); return $filled;
}
function tutor_of_month(mysqli $conn){
    $sql = "SELECT ur.ID AS TID, u.Name, AVG(r.Rating) AS avgRating
            FROM userreview ur
            JOIN review r ON ur.SerialNo = r.SerialNo
            JOIN teacher t ON t.TID = ur.ID
            JOIN user u ON u.ID = ur.ID
            JOIN credentials c ON c.TID = ur.ID
            WHERE c.VerifiedStatus = 'Verified'
            GROUP BY ur.ID, u.Name
            ORDER BY avgRating DESC, TID ASC
            LIMIT 1";
    $res=$conn->query($sql);
    if($res && ($row=$res->fetch_assoc())) return $row;
    return null;
}
?>
