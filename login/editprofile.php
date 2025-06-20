<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
    $stmt->bind_param('ssi', $username, $email, $user_id);
    if ($stmt->execute()) {
        echo "Profile updated successfully.";
    } else {
        echo "Error updating profile.";
    }
    $stmt->close();
}

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<!-- Mirrored from bytesed.com/tf/qixer/qixer_html/dashboard_editprofile.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 26 Oct 2022 16:23:26 GMT -->
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title>Gideons Technology | Edit profile</title>

<link rel="icon"
href="../assets/icon.png"
sizes="32x32"
type="../assets/png"
>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="../assets/css/animate.css">

<link rel="stylesheet" href="../assets/css/bootstrap.min.css">

<link rel="stylesheet" href="../assets/css/slick.css">

<link rel="stylesheet" href="../assets/css/line-awesome.min.css">

<link rel="stylesheet" href="../assets/css/nice-select.css">

<link rel="stylesheet" href="../assets/css/style.css">

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
</head>
<body>

<div class="preloader" id="preloader">
<div class="preloader-inner">
<div class="loader_bars">
<span></span>
</div>
</div>
</div>

<div class="body-overlay"></div>
<div class="dashboard-area dashboard-padding">
<div class="container-fluid">
<div class="dashboard-contents-wrapper">
<div class="dashboard-icon">
<div class="sidebar-icon">
<i class="las la-bars"></i>
</div>
</div>
<div class="dashboard-left-content">
<div class="dashboard-close-main">
<div class="close-bars"> <i class="las la-times"></i> </div>
<div class="dashboard-top padding-top-40">
<div class="thumb">
<img src="../assets/img/dashboard/authors.jpg" alt="">
</div>
<div class="author-content">
<h4 class="title"> <?php echo $_SESSION['user_username']; ?> </h4>

</div>
</div>
<div class="dashboard-bottom margin-top-35 margin-bottom-50">
<ul class="dashboard-list ">
<li class="list">
<a href="./dashboard.php"> <i class="las la-th"></i> Dashboard </a>
</li>
<li class="list">
<a href="javascript:void(0)"> <i class="las la-cogs"></i> Services </a>
</li>
<li class="list">
<a href="dashboard_order.html"> <i class="las la-tasks"></i> Order Status </a>
</li>
<li class="list">
<a href="dashboard_payout.html"> <i class="las la-dollar-sign"></i> Payment and Financials </a>
</li>
<li class="list active has-children open show">
<a href="javascript:void(0)"> <i class="las la-user"></i> Profile </a>
<ul class="submenu">
    <li class="list selected"> <a href="profile.php"> Profile </a> </li>
    <li class="list"> <a href="editprofile.php"> Edit Profile </a> </li>
</ul>
</li>
<li class="list">
<a href="dashboard_settings.html"> <i class="las la-cog"></i> Settings </a>
</li>
<li class="list">
<a href="dashboard_review.html"> <i class="lar la-star"></i> Policy Status </a>
</li>
<li class="list">
<a href="javascript:void(0)"> <i class="las la-sign-out-alt"></i> Log Out </a>
</li>
</ul>
<div class="dashboard-logo padding-top-20">
<a href="index.html" class="logo"> <img src="../assets/img/dashboard/dashboard-logo.png" alt=""> </a>
</div>
</div>
</div>
</div><div class="dashboard-right">
<div class="profile-dashboards">
<div class="row">
<div class="col-lg-12">
<div class="edit-profile">
<div class="profile-info-dashboard">
<div class="profile-btn-flex">
<h2 class="dashboards-title"> Edit Profile </h2>
<div class="btn-wrapper">
<a href="dashboard_profile.html" class="cmn-btn btn-bg-1"> View Profile </a>
</div>
</div>
<div class="dashboard-profile-flex">
<div class="thumbs margin-top-40">
<img src=".//assets/img/dashboard/profile-man.jpg" alt="">
<div class="edit-thumb">
<a href="javascript:void(0)"> <i class="lar la-image"></i> </a>
</div>
</div>
<div class="dashboard-address-details">
<form method="POST">
<div class="single-dashboard-input">
<div class="single-info-input margin-top-30">
<label class="info-title"> Your Name* </label>
<input class="form--control" type="text" name="username" value="<?php echo $user['username']; ?>" required>
</div>
<div class="single-info-input margin-top-30">
<label class="info-title"> Your Email* </label>
<input class="form--control" type="email" name="email" value="<?php echo $user['email']; ?>" required>
</div>
</div>
<div class="single-dashboard-input">
<div class="single-info-input margin-top-30">
 <label class="info-title"> Phone Number* </label>
<input class="form--control" type="tel" placeholder="Type Your Number">
</div>
<div class="single-info-input margin-top-30">
<label class="info-title"> Your City* </label>
<select>
<option value="1">New York</option>
<option value="2">London</option>
<option value="2">Eden Garden</option>
<option value="2">Paris</option>
<option value="2">Barcelona</option>
</select>
</div>
</div>
<div class="single-dashboard-input">
<div class="single-info-input margin-top-30">
<label class="info-title"> Your Area* </label>
<input class="form--control" type="text" placeholder="Type Your Area">
</div>
<div class="single-info-input margin-top-30">
<label class="info-title"> Post Code* </label>
<input class="form--control" type="tel" placeholder="Type Post Code">
</div>
</div>
<div class="single-dashboard-input">
<div class="single-info-input margin-top-30">
<label class="info-title"> Your Address* </label>
<input class="form--control" type="text" placeholder="Type Your Address">
</div>
</div>
<div class="single-dashboard-input">
<div class="single-info-input margin-top-30">
<label class="info-title"> About* </label>
<textarea class="form--control textarea--form" name="message" placeholder="Type Note"></textarea>
</div>
</div>
<div class="btn-wrapper margin-top-35">
<button type="submit" class="cmn-btn btn-bg-1"> Save Changes </button>
</div>
</form>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>


<div class="back-to-top">
<span class="back-top"><i class="las la-angle-up"></i></span>
</div>


<script src="../assets/js/jquery-3.6.0.min.js"></script>

<script src="../assets/js/jquery-migrate.min.js"></script>

<script src="../assets/js/bootstrap.bundle.min.js"></script>

<script src="../assets/js/wow.min.js"></script>

<script src="../assets/js/slick.js"></script>

<script src="../assets/js/jquery.nice-select.js"></script>

<script src="../assets/js/jquery.nicescroll.min.js"></script>

<script src="../assets/js/main.js"></script>
</body>

<!-- Mirrored from bytesed.com/tf/qixer/qixer_html/dashboard_editprofile.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 26 Oct 2022 16:23:26 GMT -->
</html>