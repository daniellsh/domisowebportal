<?php
$nav = "";
$title = "Dashboard";
include('components/header.php');
include '../config/database.php';

if ($_SESSION['type'] != 'admin') {
  header("Location: ../login.php");
  exit;
}

if (isset($_POST['update'])) {
  // Get the new data to update
  $success = null;
  $upload_dir = "../uploads/content/";
  $sql = "SELECT * FROM site_content WHERE id = 1";

  // Execute the query
  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_assoc($result);
  $about_us = $_POST['about_us'];
  $latitude = $_POST['latitude'];
  $longitude = $_POST['longitude'];

  if (!empty($_FILES['banner_image']['name'])) {
    $banner_image = $_FILES['banner_image']['name'];
    $banner_image_name = uniqid() . '.' . pathinfo($banner_image, PATHINFO_EXTENSION);
    move_uploaded_file($_FILES['banner_image']['tmp_name'], $upload_dir . $banner_image_name);
  } else {
    $banner_image_name = $row['banner_image'];
  }
  if (!empty($_FILES['slider_image_1']['name'])) {
    $slider_image_1 = $_FILES['slider_image_1']['name'];
    $slider_image_1_name = uniqid() . '.' . pathinfo($slider_image_1, PATHINFO_EXTENSION);
    move_uploaded_file($_FILES['slider_image_1']['tmp_name'], $upload_dir . $slider_image_1_name);
  } else {
    $slider_image_1_name = $row['slider_image_1'];
  }
  if (!empty($_FILES['slider_image_2']['name'])) {
    $slider_image_2 = $_FILES['slider_image_2']['name'];
    $slider_image_2_name = uniqid() . '.' . pathinfo($slider_image_2, PATHINFO_EXTENSION);
    move_uploaded_file($_FILES['slider_image_2']['tmp_name'], $upload_dir . $slider_image_2_name);
  } else {
    $slider_image_2_name = $row['slider_image_2'];
  }
  if (!empty($_FILES['slider_image_3']['name'])) {
    $slider_image_3 = $_FILES['slider_image_3']['name'];
    $slider_image_3_name = uniqid() . '.' . pathinfo($slider_image_3, PATHINFO_EXTENSION);
    move_uploaded_file($_FILES['slider_image_3']['tmp_name'], $upload_dir . $slider_image_3_name);
  } else {
    $slider_image_3_name = $row['slider_image_3'];
  }



  // Update site content in database
  $sql = "UPDATE site_content SET about_us='$about_us', banner_image='$banner_image_name', slider_image_1='$slider_image_1_name', slider_image_2='$slider_image_2_name', slider_image_3='$slider_image_3_name' , latitude = '$latitude' , longitude = '$longitude' WHERE id=1";

  if (mysqli_query($conn, $sql)) {
    $success = "Site content updated successfully";
  } else {
    echo "Error updating site content: " . mysqli_error($conn);
  }
}

// Query to fetch data from site_content table
$sql = "SELECT * FROM site_content WHERE id = 1";

// Execute the query
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
?>
<div class="container py-5">
  <h1 class="mb-3">Site Settings</h1>
  <hr>
  <?php if (isset($success)) : ?>
    <div class="alert alert-success">
      <?= $success ?>
    </div>
  <?php endif; ?>
  <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
    <div class="row">
      <h3>Home</h3>
      <div class="mb-3">
        <label for="banner" class="form-label">Banner</label>
        <input class="form-control" id="banner" name="banner_image" type="file" />
      </div>
      <img src="<?= empty($row['banner_image']) ? "../assets/images/banner.webp" : "../uploads/content/" . $row['banner_image'] ?>" style="width: 100%;height:400px;object-fit:cover;" alt="Banner">
      <div class="row mt-4">
        <div class="col-md-4">
          <div class="mb-3">
            <label for="banner" class="form-label">Slide 1</label>
            <input class="form-control" id="banner" type="file" name="slider_image_1" />
          </div>
          <img src="<?= empty($row['slider_image_1']) ? "../assets/images/slider.jpg" : '../uploads/content/' . $row['slider_image_1'] ?>" style="width: 100%;height:200px;object-fit:cover;" alt="Banner">
        </div>
        <div class="col-md-4">
          <div class="mb-3">
            <label for="banner" class="form-label">Slide 2</label>
            <input class="form-control" id="banner" type="file" name="slider_image_2" />
          </div>
          <img src="<?= empty($row['slider_image_2']) ? "../assets/images/slider.jpg" : '../uploads/content/' . $row['slider_image_2'] ?>" style="width: 100%;height:200px;object-fit:cover;" alt="Banner">
        </div>
        <div class="col-md-4">
          <div class="mb-3">
            <label for="banner" class="form-label">Slide 3</label>
            <input class="form-control" id="banner" type="file" name="slider_image_3" />
          </div>
          <img src="<?= empty($row['slider_image_3']) ? "../assets/images/slider.jpg" : '../uploads/content/' . $row['slider_image_3'] ?>" style="width: 100%;height:200px;object-fit:cover;" alt="Banner">
        </div>
      </div>
      <hr class="my-4">
      <h3>About Us</h3>
      <div class="mb-3">
        <label for="exampleFormControlInput1" class="form-label">Content</label>
        <textarea name="about_us" class="form-control" id="exampleFormControlInput1" placeholder="Content"><?= $row['about_us'] ?></textarea>
      </div>
    </div>
    <hr />
    <h3>Google Map</h3>
    <div class="row">
      <div class="col-md-6">
        <div class="mb-3">
          <label for="banner" class="form-label">Latitude</label>
          <input class="form-control" id="banner" name="latitude" type="text" value="<?= $row['latitude'] ?>" />
        </div>
      </div>
      <div class="col-md-6">
        <div class="mb-3">
          <label for="banner" class="form-label">Longitude</label>
          <input class="form-control" id="banner" name="longitude" type="text" value="<?= $row['longitude'] ?>" />
        </div>
      </div>
    </div>
    <button class="btn btn-primary" name="update">Save</button>
  </form>
</div>

<?php include('components/footer.php'); ?>