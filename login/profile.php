<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
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

<!-- Mirrored from bytesed.com/tf/qixer/qixer_html/dashboard_profile.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 26 Oct 2022 16:23:25 GMT -->
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title>Dashboard | Profile</title>
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

<link rel="stylesheet" href="profile.css">

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
<h4 class="title"> <?php echo $user['username']; ?> </h4>

</div>
</div>
<div class="dashboard-bottom margin-top-35 margin-bottom-50">
<ul class="dashboard-list ">
<li class="list">
<a href="./dashboard.php"> <i class="las la-th"></i> Dashboard </a>
</li>
<li class="list">
<a href="javascript:void(0)"> <i class="las la-cogs"></i> Services </a>
<ul class="submenu">
<li><a href="../exchangeplatform/exchange.html">Crypto Exchange Platform</a></li>
</ul>
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
<form action="logout.php" method="post">
	<button type="submit">
<a> <i class="las la-sign-out-alt"></i> Log out </a>
</button></form>
</li>
</ul>
<div class="dashboard-logo padding-top-20">
<a href="index.html" class="logo"> <img src="../assets/img/dashboard/dashboard-logo.png" alt=""> </a>
</div>
</div>
</div>
</div>
<div class="dashboard-right">
<div class="row">
<div class="col-lg-12">
<div class="dashboard-profile">
<div class="dashboard-profile-all">
<div class="thumb-ad">

</div>
<div class="profile-info-dashboard margin-top-40">
<div class="profile-btn-flex">
<h2 class="dashboards-title"> Profile Information </h2>
<div class="btn-wrapper">
<a href="editprofile.php" class="cmn-btn btn-bg-1"> Edit Profile </a>
</div>
</div>
<div class="dashboard-profile-detail margin-top-40">
<div class="dashboard-profile-flex">
<div class="thumbs">
<img src="../assets/img/dashboard/profile-man.jpg" alt="">
</div>
<div class="dashboard-address-details">
<ul class="details-list">
<li class="lists">
<span class="list-span"> Name: </span>
<span class="list-strong"> <?php echo $user['username']; ?> </span>
</li>
<li class="lists">
<span class="list-span"> Email: </span>
<span class="list-strong"> <?php echo $user['email']; ?> </span>
</li>
<li class="lists">
<span class="list-span"> Phone: </span>
<span class="list-strong"> 011-2234567890 </span>
 </li>
<li class="lists">
<span class="list-span"> City: </span>
<span class="list-strong"> New york </span>
</li>
<li class="lists">
<span class="list-span"> Area: </span>
<span class="list-strong"> New york </span>
</li>
<li class="lists">
<span class="list-span"> Post Code: </span>
<span class="list-strong"> 1122 </span>
</li>
<li class="lists">
<span class="list-span"> Address: </span>
<span class="list-strong"> 2608 Ritter Avenue Livonia, MI 48150 </span>
</li>
</ul>
<ul class="details-list column-count-one">
<li class="lists">
<span class="list-span"> About: </span>
<span class="list-strong"> It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English. <b>Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy.</b> </span>
<span class="para"> </span>
</li>
</ul>
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


<script data-cfasync="false" src="../../../cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script src="../assets/js/jquery-3.6.0.min.js"></script>

<script src="../assets/js/jquery-migrate.min.js"></script>

<script src="../assets/js/bootstrap.bundle.min.js"></script>

<script src="../assets/js/wow.min.js"></script>

<script src="../assets/js/slick.js"></script>

<script src="../assets/js/jquery.nice-select.js"></script>

<script src="../assets/js/jquery.nicescroll.min.js"></script>

<script src="../assets/js/main.js"></script>
</body>

<!-- Mirrored from bytesed.com/tf/qixer/qixer_html/dashboard_profile.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 26 Oct 2022 16:23:26 GMT -->
</html>