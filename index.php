<?php
$nav = "home";
$title = "Home";
include './config/database.php';
include('components/header.php');
$sql = "SELECT * FROM site_content WHERE id = 1";

// Execute the query
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

?>
<!-- Start Banner -->
<div class="banner my-5">
  <div class="container">
    <img src="<?= empty($row['banner_image']) ? "./assets/images/banner.webp" : './uploads/content/' . $row['banner_image'] ?>" style="height:400px;object-fit:fill;" />
  </div>
</div>
<!-- End Banner -->


<!-- Start Slogn -->
<!-- End Slogn -->
<?php include('components/footer.php'); ?>