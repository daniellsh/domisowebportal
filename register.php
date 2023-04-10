<?php
require 'config/database.php';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit;
}

if (isset($_POST['submit'])) {
  $errors = array();

  // Define error messages array

  // Sanitize input data
  $first_name = htmlspecialchars($_POST['first_name']);
  $last_name = htmlspecialchars($_POST['last_name']);
  $dob = htmlspecialchars($_POST['dateofbirth']);
  $gender = htmlspecialchars($_POST['gender']);
  $mobile = htmlspecialchars($_POST['mobile']);
  $email = htmlspecialchars($_POST['email']);
  $username = htmlspecialchars($_POST['username']);
  $aggred = isset($_POST['aggred']) ? true : false;
  $password = htmlspecialchars($_POST['password']);
  $success = null;


  // Validate Aggred
  if (!$aggred) {
    $errors["aggred"] = "Aggred to terms is required";
  }
  // Validate input data
  if (empty($first_name)) {
    $errors["first_name"] = "First Name is required";
  }
  if (empty($last_name)) {
    $errors["last_name"] = "Last Name is required";
  }
  if (empty($dob)) {
    $errors["dateofbirth"] = "Date of Birth is required";
  }
  if (empty($gender)) {
    $errors["gender"] = "Gender is required";
  }
  if (empty($mobile)) {
    $errors["mobile"] = "Mobile is required";
  }

  if (empty($email)) {
    $errors["email"] = "Email is required";
  }
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors["email"] = "Invalid Email format";
  }
  if (empty($username)) {
    $errors["username"] = "Username is required";
  }
  if (empty($password)) {
    $errors["password"] = "Password is required";
  }

  // If no errors, insert user data into database
  if (empty($errors)) {
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare SQL statement
    $sql = "INSERT INTO users (first_name, last_name, dateofbirth, gender, mobile, email, username, password, type) VALUES ('$first_name', '$last_name', '$dob', '$gender', '$mobile', '$email', '$username', '$hashed_password', 'student')";

    // Execute statement and check for success
    if ($conn->query($sql)) {
      $success = "User registered successfully , <a href='./login.php'>Login</a>";
    } else {
      echo "Something went error try again later";
    }
  }
}
$nav = "register";
$title = "Register";
include('components/header.php');
?>

<div class="container my-5 login">
  <?php if (isset($success)) : ?>
    <div class="alert alert-success">
      <?= $success ?>
    </div>
  <?php endif; ?>
  <form method="post" action="<?= $_SERVER['PHP_SELF'] ?>">
    <div class="form-group mb-2">
      <label for="first_name" class="mb-1">First name</label>
      <input type="text" placeholder="First Name" class="form-control" id="first_name" name="first_name" value="<?php if (isset($_POST['first_name']) && !empty($_POST['first_name'])) echo $_POST['first_name'] ?>">
      <?php if (isset($errors['first_name'])) : ?>
        <small class="text-error"><?= $errors['first_name'] ?></small>
      <?php endif; ?>
    </div>
    <div class="form-group mb-2">
      <label for="last_name" class="mb-1">Last name</label>
      <input type="text" placeholder="Last Name" class="form-control" id="last_tname" name="last_name" value="<?php if (isset($_POST['last_name']) && !empty($_POST['last_name'])) echo $_POST['last_name'] ?>">
      <?php if (isset($errors['last_name'])) { ?>
        <small class="text-error"><?= $errors['last_name'] ?></small>
      <?php } ?>
    </div>
    <div class="form-group mb-2">
      <label for="dateofbirth" class="mb-1">Date of Birth</label>
      <input type="date" placeholder="Date of Birth" class="form-control" id="dataofbirth" name="dateofbirth" value="<?php if (isset($_POST['dateofbirth']) && !empty($_POST['dateofbirth'])) echo $_POST['dateofbirth'] ?>">
      <?php if (isset($errors['dateofbirth'])) : ?>
        <small class="text-error"><?= $errors['dateofbirth'] ?></small>
      <?php endif; ?>
    </div>
    <div class="form-group mb-2">
      <label for="mobile" class="mb-1">Gender</label>
      <div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="gender" id="genderRadio1" value="male" checked>
          <label class="form-check-label" for="genderRadio1">Male</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="gender" id="genderRadio2" value="female">
          <label class="form-check-label" for="genderRadio2">Female</label>
        </div>
      </div>
      <?php if (isset($errors['gender'])) : ?>
        <small class="text-error"><?= $errors['gender'] ?></small>
      <?php endif; ?>
    </div>
    <div class="form-group mb-2">
      <label for="mobile" class="mb-1">Mobile</label>
      <input type="phone" placeholder="Mobile" class="form-control" id="mobile" name="mobile" value="<?php if (isset($_POST['mobile']) && !empty($_POST['mobile'])) echo $_POST['mobile'] ?>">
      <?php if (isset($errors['mobile'])) : ?>
        <small class="text-error"><?= $errors['mobile'] ?></small>
      <?php endif; ?>
    </div>
    <div class="form-group mb-2">
      <label for="email" class="mb-1">email</label>
      <input type="email" placeholder="E-mail Address" class="form-control" id="email" name="email" value="<?php if (isset($_POST['email']) && !empty($_POST['email'])) echo $_POST['email'] ?>">
      <?php if (isset($errors['email'])) : ?>
        <small class="text-error"><?= $errors['email'] ?></small>
      <?php endif; ?>
    </div>
    <div class="form-group mb-2">
      <label for="username" class="mb-1">Username</label>
      <input type="text" placeholder="Username" class="form-control" id="username" name="username" value="<?php if (isset($_POST['username']) && !empty($_POST['username'])) echo $_POST['username'] ?>">
      <?php if (isset($errors['username'])) : ?>
        <small class="text-error"><?= $errors['username'] ?></small>
      <?php endif; ?>
    </div>
    <div class="form-group mb-2">
      <label for="password" class="mb-1">Password:</label>
      <input type="password" placeholder="Password" class="form-control" id="password" name="password" value="<?php if (isset($_POST['password']) && !empty($_POST['password'])) echo $_POST['password'] ?>">
      <?php if (isset($errors['password'])) : ?>
        <small class="text-error"><?= $errors['password'] ?></small>
      <?php endif; ?>
    </div>
    <div class="form-check">
      <label class="form-check-label" for="flexCheckChecked">
        I agreed to the terms and regulation
      </label>
      <input class="form-check-input" type="checkbox" name="aggred" id="flexCheckChecked">

    </div>
    <?php if (isset($errors['aggred'])) : ?>
      <small class="text-error"><?= $errors['aggred'] ?></small>
    <?php endif; ?>
    <div class="d-flex justify-content-between gap-2">
      <button type="submit" name="submit" class="btn mt-2" style="flex:1;">Submit</button>
      <button type="reset" class="btn mt-2 reset" style="flex:1;">Reset</button>
    </div>
  </form>
</div>

<?php include('components/footer.php'); ?>