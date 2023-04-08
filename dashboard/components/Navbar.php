<?php
session_start();
include '../config/database.php';

if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];
  $sql = "SELECT profile_image FROM users WHERE id = '$user_id'";
  $result = mysqli_query($conn, $sql);
  $user = mysqli_fetch_assoc($result);
}

?>
<nav class="navbar center navbar-expand-lg bg-body-tertiary bg-dark bg-dark py-3" data-bs-theme="dark">
  <div class="container">
    <a class="navbar-brand" href="../index.php">
      <img width="50" src="../assets/images/logo.png" />
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?= $nav == "home" ? "active" : "" ?>" aria-current="page" href="../">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $nav == "about" ? "active" : "" ?>" aria-current="page" href="../about.php">About us</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $nav == "course" ? "active" : "" ?>" aria-current="page" href="../course.php">Course</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $nav == "eshop" ? "active" : "" ?>" aria-current="page" href="../eshop.php">eShop</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $nav == "contact" ? "active" : "" ?>" aria-current="page" href="../contact.php">Contact</a>
        </li>
      </ul>
      <?php
      if (!isset($_SESSION['user_id'])) :
      ?>
        <div class="auth hidden">
          <a href="./login.php" class="btn btn-link text-white">Login</a>
          <a href="./register.php" class="btn btn-primary">Sign up</a>
        </div>
      <?php else : ?>
        <div class="dropdown">
          <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <img src="<?= empty($user['profile_image']) ? "../assets/images/profile.png" : '../uploads/profiles/' . $user['profile_image'] ?>" class="img-profile" alt="User Profile">
            <?= $_SESSION['username'] ?>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
            <h4 class="dropdown-header"><?= $_SESSION['type'] ?></h4>
            <div class="dropdown-divider"></div>
            <?php if (isset($_SESSION['type']) && $_SESSION['type'] == 'admin') : ?>
              <a class="dropdown-item" href="./index.php">Dashboard</a>
            <?php endif; ?>
            <?php if (isset($_SESSION['type']) && $_SESSION['type'] == 'teacher') : ?>
              <a class="dropdown-item" href="./courses.php">Courses</a>
            <?php endif; ?>
            <a class="dropdown-item" href="../inbox.php">Inbox</a>
            <a class="dropdown-item" href="../profile.php">Profile</a>
            <a class="dropdown-item" href="../my-course.php">My Course</a>
            <a class="dropdown-item" href="../my-purchase.php">My Purchase</a>
            <a class="dropdown-item" href="../my-drive.php">My Drive</a>
            <a class="dropdown-item" href="../room-booking.php">Room Booking</a>
            <a class="dropdown-item" href="../cart.php">My Cart</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="../logout.php">Logout</a>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</nav>