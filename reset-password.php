<?php
$nav = "login";
$title = "Reset Password";
include('components/header.php');

$reset = false;
if (isset($_GET['token'])) {
  $token = $_GET['token'];


  $result = mysqli_query($conn, "SELECT * FROM users WHERE verify_token = '$token' ");
  if (mysqli_num_rows($result) >= 1) {
    $reset = true;
  } else {
    header("Location: reset-password.php");
  }
}

if (isset($_POST['submit'])) {
  $email = $_POST['email'];
  $result = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");

  if (mysqli_num_rows($result) == 0) {
    // Email doesn't exist in database
    echo "Email not found";
  } else {
    // Email exists in database, generate unique token and save to database
    $token = bin2hex(random_bytes(32));
    $user_id = $result->fetch_assoc()['id'];
    $sql = "UPDATE users SET verify_token = '$token' WHERE email = '$email'";
    mysqli_query($conn,  $sql);

    $reset_url =  $base_url . "reset-password.php?token=" . $token;

    // Send email to user with reset instructions
    // Compose the email message
    $to = $email;
    $subject = "Password Reset Link";
    $message = "Please click the following link to reset your password: $reset_url";
    $headers = "From: school@example.com\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    // Send the email
    mail($to, $subject, $message, $headers);

    $success = "Please check your email";
  }
}

if (isset($_POST['change'])) {
  // Get user's new password from form input
  $new_password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];

  if ($new_password != $confirm_password) {
    $error = "password did not match";
  } else {

    // Hash the password using PHP's password_hash function
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Get user's ID from token
    $token = $_POST['token'];
    $sql = "SELECT id FROM users WHERE verify_token = '$token'";
    $result = $conn->query($sql);
    if ($result->num_rows == 1) {
      $row = $result->fetch_assoc();
      $user_id = $row['id'];
    } else {
      die("Invalid token");
    }

    // Update user's password in database
    $sql = "UPDATE users SET password = '$hashed_password' WHERE id = $user_id";
    if ($conn->query($sql) === TRUE) {
      $success =  "Password updated successfully";
    } else {
      echo "Error updating password: " . $conn->error;
    }
  }
}
?>

<div class="container my-5 login">
  <?php if (isset($success)) : ?>
    <div class="alert alert-success">
      <?= $success ?>
    </div>
  <?php endif; ?>
  <?php if (isset($error)) : ?>
    <div class="alert alert-danger">
      <?= $error ?>
    </div>
  <?php endif; ?>
  <?php if (!$reset) : ?>
    <form method="post" action="<?= $_SERVER['PHP_SELF'] ?>">
      <div class="form-group mb-2">
        <label for="email" class="mb-1">Registered email:</label>
        <input type="email" class="form-control" id="email" name="email" required>
      </div>
      <p class="text-center">We will send you the password reset link</p>
      <button type="submit" class="btn mt-2 w-100" name="submit">Submit</button>
    </form>
  <?php else : ?>
    <form method="post" action="<?= $_SERVER['PHP_SELF'] ?>">
      <input type="hidden" name="token" value="<?= $_GET['token'] ?? "" ?>" />
      <div class="form-group mb-2">
        <label for="password" class="mb-1">New Password:</label>
        <input type="password" class="form-control" id="password" name="password" required>
      </div>
      <div class="form-group mb-2">
        <label for="password" class="mb-1">Confirm Password:</label>
        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
      </div>
      <button type="submit" class="btn mt-2 w-100" name="change">Change Password</button>
    </form>
  <?php endif; ?>
</div>

<?php include('components/footer.php'); ?>