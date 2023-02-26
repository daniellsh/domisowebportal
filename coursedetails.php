<?php
$nav = "course";
$title = "Course Details";
include('components/header.php');
include './config/database.php';

if (isset($_POST['buy'])) {
  if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
  } else {
    $user_id = $_SESSION['user_id'];
    $course_id = $_POST['course_id'];
    $course_price = $_POST['course_price'];
    $sessions = $_POST['sessions'];


    // $result = mysqli_query($conn, "INSERT INTO enrollments (course_id, user_id, sessions , enrollment_date , upcoming_date) VALUES ('$course_id', '$user_id','$sessions' , CURRENT_TIME , CURRENT_TIME)");
    // if ($result) {
    //   header('Location: course.php');
    //   exit();
    // } else {
    //   echo "Error placing order: " . mysqli_error($conn);
    // }

    // Product does not exist, insert a new order
    $result = mysqli_query($conn, "INSERT INTO orders (user_id, course_id, product_id, quantity , price) VALUES ('$user_id', '$course_id', null, '$sessions' , '$course_price')");
    if ($result) {
      header('Location: cart.php');
      exit();
    } else {
      echo "Error placing order: " . mysqli_error($conn);
    }
  }
}



if (!isset($_GET['id'])) {
  header('Location: course.php');
}


if (isset($_GET['id']) && !empty($_GET['id'])) {

  // Get the course ID from the query string
  $course_id = mysqli_real_escape_string($conn, $_GET['id']);

  // Prepare the SQL statement to select the course and teacher information
  $sql = "SELECT courses.*, users.first_name AS teacher_firstname , users.last_name AS teacher_lastname 
        FROM courses 
        JOIN users ON courses.teacher_id = users.id 
        WHERE courses.course_id = '" . $course_id . "'";

  // Execute the SQL statement
  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_assoc($result);
}
?>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Buy Session</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" value="<?= $row['course_id'] ?>" name="course_id" />
          <input type="hidden" value="<?= $row['course_price'] ?>" name="course_price" />
          <div class="row mb-3">
            <label for="inputEmail3" class="col-sm-4 col-form-label">Course Code</label>
            <div class="col-sm-8">
              <span>
                <?= $row['course_code'] ?>
              </span>
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-4 col-form-label">Course Detail</label>
            <div class="col-sm-8">
              <span>
                <?= substr($row['course_description'], 0, 60) ?>
              </span>
            </div>
          </div>
          <div class="row mb-3">
            <label for="inputEmail3" class="col-sm-4 col-form-label">Session Pack</label>
            <div class="col-sm-8 d-flex gap-2 align-items-center">
              <a class="btn btn-sm btn-primary rounded-full plus-btn btn-cart">+</a>
              <input type="number" id="quantity" name="sessions" value="1" style="width:60px;">
              <a class="btn btn-sm btn-primary rounded-full minus-btn btn-cart">-</a>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary" name="buy">Confirm</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="container course py-5">
  <h1>
    <?= $row['course_name'] ?>
  </h1>
  <hr>
  <div class="row">
    <div class="col-sm-12 col-md-6">
      <h4>
        <?= $row['teacher_firstname'] . ' ' . $row['teacher_lastname'] ?>
      </h4>
      <h6>Requirements and Details :</h6>
      <p style="margin-bottom: 5px">
        <?= '$' . $row['course_price'] ?>
      </p>
      <p>
        <?= $row['course_description'] ?>
      </p>
      <?php if (isset($error)): ?>
        <div class="alert alert-danger">
          <?= $error ?>
        </div>
      <?php endif; ?>
      <div class="d-flex gap-2">
        <?php

        $sql = "SELECT * FROM enrollments WHERE user_id = '$user_id' AND course_id = '$course_id'";
        $check = mysqli_query($conn, $sql);

        if (mysqli_num_rows($check) >= 1) { ?>
          <div class="btn btn-success">Joined</div>
          <?php
        } else {
          ?>
          <div class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Join</div>
          <?php
        } ?>
        <div class="btn btn-warning" onclick="copyPageLink()">Share</div>
      </div>
    </div>
    <div class="col-sm-12 col-md-6">
      <div class="row g-2">
        <div class="col-md-6">
          <img
            src="<?= empty($row['image_url_1']) ? "./assets/images/noimage.png" : './uploads/courses/' . $row['image_url_1'] ?>"
            class="img-fluid" />
        </div>
        <div class="col-md-6">
          <img
            src="<?= empty($row['image_url_2']) ? "./assets/images/noimage.png" : './uploads/courses/' . $row['image_url_2'] ?>"
            class="img-fluid" />
        </div>
        <div class="col-md-6">
          <img
            src="<?= empty($row['image_url_3']) ? "./assets/images/noimage.png" : './uploads/courses/' . $row['image_url_3'] ?>"
            class="img-fluid" />
        </div>
        <div class="col-md-6">
          <img
            src="<?= empty($row['image_url_4']) ? "./assets/images/noimage.png" : './uploads/courses/' . $row['image_url_4'] ?>"
            class="img-fluid" />
        </div>
      </div>
    </div>
  </div>
</div>

<?php include('components/footer.php'); ?>