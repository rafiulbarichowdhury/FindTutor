<?php
require_once __DIR__ . "/_init.php";
$u = current_user();
$tom = function_exists('tutor_of_month') ? tutor_of_month($conn) : null;
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>FindTutor — Home</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="styles.css" />
</head>
<body>
  <div class="nav container">
    <h2>FindTutor</h2>
    <?php if (is_admin()): ?>
      <div>
        <a class="btn" href="admin_dashboard.php">Admin Dashboard</a>
        <a class="btn" href="auth_logout.php">Logout</a>
      </div>
    <?php elseif (is_logged_in()): ?>
      <div>
        <a class="btn" href="auth_logout.php">Logout</a>
      </div>
    <?php else: ?>
      <div>
        <a class="btn" href="auth_login.php">Login</a>
        <a class="btn" href="auth_register.php">Register</a>
        <a class="btn" href="admin_login.php">Admin</a>
      </div>
    <?php endif; ?>
  </div>
  <div class="container">
    <div class="card center">
      <h1>Welcome to FindTutor</h1>
      <p>Connect guardians with verified tutors.</p>
    </div>

    <?php if ($tom): ?>
    <div class="card">
      <strong>Tutor of the Month:</strong>
      <?php echo htmlspecialchars($tom["Name"]); ?> (Avg rating: <?php echo round($tom["avgRating"],2); ?>)
    </div>
    <?php endif; ?>

    <?php if ($u): ?>
      <?php if ($u["role"] === "guardian"): ?>
        <div class="card">
          <h3>Guardian Dashboard</h3>
          <p>
            <a class="btn" href="jobs_create.php">Post Tuition Job</a>
            <a class="btn" href="jobs_list.php?mine=1">My Posted Jobs</a>
            <a class="btn" href="applications_list.php">Applicants</a>
            <a class="btn" href="reviews_create.php">Write a Review</a>
          </p>
        </div>
      <?php elseif ($u["role"] === "teacher"): ?>
        <div class="card">
          <h3>Teacher Dashboard</h3>
          <p>Status: <?php echo is_teacher_verified($conn, intval($u["id"])) ? "<span class='badge'>Verified</span>" : "<span class='badge'>Not Verified</span>"; ?></p>
          <p>
            <a class="btn" href="jobs_list.php">Browse & Apply</a>
            <a class="btn" href="teacher_credentials.php">Credentials</a>
            <a class="btn" href="teacher_applications.php">My Applications</a>
            <a class="btn" href="reviews_create.php">Write a Review</a>
          </p>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</body>
</html>