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

<!-- Start Carousel -->

<!-- End Carousel -->
<div class="owl-carousel mb-5">
  <div class="item">
    <img style="height:600px;object-fit:cover;" src="<?= empty($row['slider_image_1']) ? "./assets/images/slider.jpg" : './uploads/content/' . $row['slider_image_1'] ?>" alt="Carousel 1">
  </div>
  <div class="item">
    <img style="height:600px;object-fit:cover;" src="<?= empty($row['slider_image_2']) ? "./assets/images/slider.jpg" : './uploads/content/' . $row['slider_image_2'] ?>" alt="Carousel 2">
  </div>
  <div class="item">
    <img style="height:600px;object-fit:cover;" src="<?= empty($row['slider_image_3']) ? "./assets/images/slider.jpg" : './uploads/content/' . $row['slider_image_3'] ?>" alt="Carousel 3">
  </div>
</div>
<!-- Start Slogn -->
<!-- End Slogn -->
<?php include('components/footer.php'); ?>