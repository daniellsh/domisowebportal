<?php
$nav = "about";
$title = "About Us";
include('components/header.php');
include './config/database.php';
$sql = "SELECT about_us FROM site_content WHERE id = 1";
$row = mysqli_fetch_assoc(mysqli_query($conn, $sql))
?>
<div class="container py-5 about">
  <h1>We are..</h1>
  <p>
    <?= $row['about_us'] ?? '
  Lorem ipsum dolor sit amet consectetur adipisicing elit. Earum adipisci aperiam consequatur a quibusdam quos quidem magnam libero illum, dicta error nemo veniam nostrum, beatae quo mollitia maiores eum similique.' ?>
  </p>
</div>
<?php include('components/footer.php'); ?>