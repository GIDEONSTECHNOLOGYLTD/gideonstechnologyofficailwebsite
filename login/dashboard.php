<?php
session_start();

// Security headers
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");

// Session security checks
if (!isset($_SESSION['initialized']) || !isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}

// Validate session age
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_unset();
    session_destroy();
    header("Location: ../login.html?expired=1");
    exit();
}
$_SESSION['last_activity'] = time();

// Prevent session fixation
if (!isset($_SESSION['created'])) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
} else if (time() - $_SESSION['created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

require_once 'config.php';

// Fetch user data securely
$user_id = (int)$_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email, is_verified FROM users WHERE id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    session_unset();
    session_destroy();
    header("Location: ../login.html");
    exit();
}

$user = $result->fetch_assoc();
$stmt->close();

// Check if email is verified
if (!$user['is_verified']) {
    $_SESSION['verification_required'] = true;
    header("Location: verification_notice.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<!-- Mirrored from bytesed.com/tf/qixer/qixer_html/contact.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 26 Oct 2022 16:23:19 GMT -->
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title>Gideons Technology | Dashboard </title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<link rel="icon"
href="../assets/icon.png"
sizes="32x32"
type="../assets/png"
>


<link rel="stylesheet" href="../assets/css/animate.css">

<link rel="stylesheet" href="../assets/css/bootstrap.min.css">

<link rel="stylesheet" href="../assets/css/slick.css">

<link rel="stylesheet" href="../assets/css/line-awesome.min.css">

<link rel="stylesheet" href="../assets/css/nice-select.css">

<link rel="stylesheet" href="../assets/css/style.css">

<link rel="stylesheet" href="dashboard.css">

<style>
  .navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .navbar-collapse {
    flex-grow: 1;
    justify-content: center;
  }
  .logo-wrapper {
    display: flex;
    align-items: center;
    margin-right: auto; /* Ensure the logo stays on the left */
  }
  .nav-right-content {
    display: flex;
    align-items: center;
    margin-left: auto; /* Ensure the right content is pushed to the far right */
  }
  .navbar-nav .menu-item-has-children .sub-menu {
    position: absolute;
    left: 0;
    top: 100%;
    z-index: 1000;
    display: none;
    min-width: 200px;
    padding: 0;
    margin: 0;
    list-style: none;
    background-color: #fff;
    border: 1px solid #ddd;
  }
  .navbar-nav .menu-item-has-children:hover .sub-menu {
    display: block;
  }
  @media (max-width: 768px) {
    .navbar-nav {
      flex-direction: column;
      align-items: center;
    }
    .nav-right-content {
      flex-direction: column;
      align-items: center;
      margin-left: 0;
    }
    .logo-wrapper {
      width: 100%;
      display: flex;
      justify-content: center;
      margin-bottom: 10px;
    }
    .navbar-collapse {
      flex-grow: 1;
      justify-content: center;
    }
    .navbar-toggler {
      margin-left: auto;
    }
    .navbar-nav .menu-item-has-children .sub-menu {
      position: static;
      width: 100%;
      border: none;
      background-color: transparent;
    }
    .navbar-nav .menu-item-has-children:hover .sub-menu {
      display: block;
      position: relative;
    }
    .nav-right-content {
      position: absolute;
      top: 10px;
      right: 10px;
      flex-direction: row;
      align-items: center;
    }
  }
</style>

<style>
    /* Existing styles */
    .dashboard-container {
        display: flex;
        min-height: 100vh;
    }
    
    .sidebar {
        width: 250px;
        background: #2c3e50;
        color: white;
        padding: 20px;
        position: fixed;
        height: 100vh;
        overflow-y: auto;
    }
    
    .main-content {
        flex: 1;
        margin-left: 250px;
        padding: 20px;
    }
    
    .sidebar-header {
        display: flex;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .sidebar-logo {
        width: 30px;
        height: 30px;
        margin-right: 10px;
    }
    
    .sidebar-menu {
        margin-top: 20px;
    }
    
    .sidebar-item {
        padding: 12px 15px;
        margin: 5px 0;
        border-radius: 5px;
        transition: background-color 0.3s;
    }
    
    .sidebar-item:hover {
        background: rgba(255, 255, 255, 0.1);
    }
    
    .active {
        background: rgba(255, 255, 255, 0.2);
    }
    
    .welcome-section {
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
        padding: 30px;
        border-radius: 10px;
        margin-bottom: 30px;
    }
    
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .action-card {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        text-align: center;
    }
    
    .action-card i {
        font-size: 24px;
        color: #3498db;
        margin-bottom: 10px;
    }
    
    .profile-section {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }
    
    .recent-activity {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    @media (max-width: 768px) {
        .sidebar {
            width: 70px;
            padding: 10px;
        }
        
        .main-content {
            margin-left: 70px;
        }
        
        .sidebar-item {
            text-align: center;
            padding: 10px;
        }
        
        .sidebar-item i {
            margin-right: 0;
        }
    }
</style>
</head>
<body>
<div class="dashboard-container">
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../assets/icon.png" alt="Logo" class="sidebar-logo">
            <span>Gideons Tech</span>
        </div>
        <div class="sidebar-menu">
            <div class="sidebar-item active">
                <i class="fa fa-home"></i>
                <span>Dashboard</span>
            </div>
            <div class="sidebar-item">
                <i class="fa fa-user"></i>
                <span>Profile</span>
            </div>
            <div class="sidebar-item">
                <i class="fa fa-shopping-cart"></i>
                <span>Orders</span>
            </div>
            <div class="sidebar-item">
                <i class="fa fa-credit-card"></i>
                <span>Payments</span>
            </div>
            <div class="sidebar-item">
                <i class="fa fa-cog"></i>
                <span>Settings</span>
            </div>
            <div class="sidebar-item">
                <i class="fa fa-sign-out"></i>
                <span>Logout</span>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="welcome-section">
            <h2>Welcome back, <?php echo htmlspecialchars($user['username']); ?>!</h2>
            <p>Your personalized dashboard</p>
        </div>

        <div class="quick-actions">
            <div class="action-card">
                <i class="fa fa-shopping-cart"></i>
                <h3>Place Order</h3>
                <p>Quickly place a new order</p>
            </div>
            <div class="action-card">
                <i class="fa fa-credit-card"></i>
                <h3>Make Payment</h3>
                <p>Process payments here</p>
            </div>
            <div class="action-card">
                <i class="fa fa-file-text-o"></i>
                <h3>View History</h3>
                <p>Check your order history</p>
            </div>
            <div class="action-card">
                <i class="fa fa-envelope"></i>
                <h3>Send Message</h3>
                <p>Contact support</p>
            </div>
        </div>

        <div class="profile-section">
            <h3>Profile Information</h3>
            <div class="profile-details">
                <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <a href="profile.php" class="btn btn-primary">Edit Profile</a>
            </div>
        </div>

        <div class="recent-activity">
            <h3>Recent Activity</h3>
            <div class="activity-list">
                <div class="activity-item">
                    <i class="fa fa-check-circle text-success"></i>
                    <div class="activity-content">
                        <p>Successfully logged in</p>
                        <small>Just now</small>
                    </div>
                </div>
                <!-- Add more activity items as needed -->
            </div>
        </div>
    </div>
</div>

<script data-cfasync="false" src="../../../cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
  <script src="../assets/js/jquery-3.6.0.min.js"></script>

  <script src="../assets/js/jquery-migrate.min.js"></script>

  <script src="../assets/js/bootstrap.bundle.min.js"></script>

  <script src="../assets/js/wow.min.js"></script>

  <script src="../assets/js/slick.js"></script>

  <script src="../assets/js/jquery.nice-select.js"></script>

  <script src="../assets/js/jquery.nicescroll.min.js"></script>

  <script src="../assets/js/main.js"></script>

</body>

</html>