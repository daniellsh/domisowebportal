<?php
include 'config/database.php';
$nav = "";
$title = "Profile";
include('components/header.php');


if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
} else {
  $user_id = $_SESSION['user_id'];
  $sql = "SELECT * FROM users WHERE id = $user_id";
  $result = mysqli_query($conn, $sql);

  // Check if user exists
  if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    // User data
    $first_name = $row['first_name'];
    $last_name = $row['last_name'];
    $date_of_birth = $row['dateofbirth'];
    $gender = $row['gender'];
    $mobile = $row['mobile'];
    $email = $row['email'];
    $username = $row['username'];
    $profile_image = $row['profile_image'];
  } else {
    echo "User not found";
  }
}

if (isset($_POST['update'])) {
  // Update user data
  $success = null;
  $error = null;
  $user_id = $_POST['user_id'];
  $first_name = $_POST['first_name'];
  $last_name = $_POST['last_name'];
  $date_of_birth = $_POST['date_of_birth'];
  $gender = $_POST['gender'];
  $mobile = $_POST['mobile'];
  $email = $_POST['email'];
  $username = $_POST['username'];
  $profile_image = null;

  if (isset($_FILES['profile_image']) && !empty($_FILES['profile_image']['name'])) {
    $file_name = $_FILES['profile_image']['name'];
    $file_tmp = $_FILES['profile_image']['tmp_name'];
    $file_size = $_FILES['profile_image']['size'];
    $file_type = $_FILES['profile_image']['type'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Check if file is an image
    if (!getimagesize($file_tmp)) {
      echo "Error: File is not an image.";
      exit();
    }

    // Check file size
    if ($file_size > 5000000) {
      $error = "Error: File size is too large.";
      exit();
    }

    // Allow only specific file extensions
    $allowed_ext = array("jpg", "jpeg", "png", "gif");
    if (!in_array($file_ext, $allowed_ext)) {
      $error = "Error: Only JPG, JPEG, PNG, and GIF files are allowed.";
      exit();
    }

    // Upload the file to the server
    $newname = uniqid() . "." . $file_ext;
    $file_dest = "uploads/profiles/" . $newname;
    if (move_uploaded_file($file_tmp, $file_dest)) {
      $profile_image = $newname;
    } else {
      $error = "Error uploading file.";
    }
    $sql = "UPDATE users SET profile_image = '$profile_image', username = '$username' , first_name = '$first_name', last_name = '$last_name', dateofbirth = '$date_of_birth', gender = '$gender', mobile = '$mobile', email = '$email' WHERE id = $user_id";
  } else {
    $sql = "UPDATE users SET  username = '$username' , first_name = '$first_name', last_name = '$last_name', dateofbirth = '$date_of_birth', gender = '$gender', mobile = '$mobile', email = '$email' WHERE id = $user_id";
  }


  if (mysqli_query($conn, $sql)) {
    $success = "User data updated successfully";
  } else {
    echo "Error updating user data: " . mysqli_error($conn);
  }
}

?>

<div class="container course py-5">
  <h1>Profile</h1>
  <hr />
  <form method="post" action="<?= $_SERVER['PHP_SELF'] ?>" enctype="multipart/form-data">
    <input type="hidden" value="<?= $row['id'] ?>" name="user_id" />
    <?php if (isset($success)) : ?>
      <div class="alert alert-success">
        <?= $success ?>
      </div>
    <?php endif; ?>
    <div class="row">
      <div class="col-sm-12 col-md-3">
        <img src="<?= empty($row["profile_image"]) ? "./assets/images/profile.png" : './uploads/profiles/' . $row["profile_image"] ?>" class="img-fluid rounded-circle border border-dark mb-4" />
        <input type="file" name="profile_image" />
      </div>
      <div class="col-sm-12 col-md-9">
        <div class="row mb-3">
          <div class="col-sm-12 col-md-6">
            <label for="firstname" class="form-label">First Name </label>
            <input type="text" id="firstname" name="first_name" value="<?= $first_name ?>" class="form-control" placeholder="First name" aria-label="First name">
          </div>
          <div class="col-sm-12 col-md-6">
            <label for="lastname" class="form-label">Last Name </label>
            <input type="text" id="lastname" name="last_name" value="<?= $last_name ?>" class="form-control" placeholder="Last name" aria-label="Last name">
          </div>
        </div>
        <div class="row mb-3 ">
          <div class="">
            <label for="date" class="form-label">Date of Birth </label>
            <input type="date" id="date" value="<?= $date_of_birth ?>" name="date_of_birth" class="form-control" placeholder="Date of Birth" aria-label="Date of Birth">
          </div>
          <div class="col-sm-12 col-md-6">
            <label for="lastname" class="form-label">Gender</label>
            <div class="form-group mb-2">
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="gender" id="inlineRadio1" value="male" <?php if ($gender == "male") {
                                                                                                            echo "checked";
                                                                                                          } ?>>
                <label class="form-check-label" for="inlineRadio1">Male</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="gender" id="inlineRadio2" value="female" <?php if ($gender == "female") {
                                                                                                              echo "checked";
                                                                                                            } ?>>
                <label class="form-check-label" for="inlineRadio2">Female</label>
              </div>
            </div>
          </div>
        </div>
        <div class="row mb-3 ">
          <div class="col-sm-12 col-md-6">
            <label for="mobile" class="form-label">Mobile </label>
            <input type="phone" value="<?= $mobile ?>" id="mobile" name="mobile" class="form-control" placeholder="Mobile" aria-label="Mobile">
          </div>
          <div class="col-sm-12 col-md-6">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" value="<?= $email ?>" id="email" name="email" class="form-control" placeholder="E-mail Address" aria-label="E-mail">
          </div>
        </div>
        <div class="row mb-3 ">
          <div class="col-sm-12 col-md-6">
            <label for="username" class="form-label">Username </label>
            <input type="phone" id="username" value="<?= $username ?>" name="username" class="form-control" placeholder="Username" aria-label="Username">
          </div>
          <div class="col-sm-12 col-md-6">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" class="form-control" placeholder="Password" aria-label="Password">
          </div>
        </div>
        <div class="d-flex gap-2">
          <button class="btn btn-primary btn-lg" type="submit" name="update">Update</button>
          <a class="btn btn-secondary btn-lg" href="./index.php">Cancel</a>
        </div>
      </div>
    </div>
  </form>
</div>

<?php include('components/footer.php'); ?>