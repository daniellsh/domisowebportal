<?php
require 'config/database.php';

$nav = "login";
$title = "Login";
include('components/header.php');

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit;
}

// Check if "remember me" cookie is set
// if (isset($_COOKIE['remember_me'])) {

//   // Decode cookie value and retrieve user data
//   $cookie_data = json_decode($_COOKIE['remember_me'], true);
//   $user_id = $cookie_data['user_id'];
//   $token = $cookie_data['token'];

//   // Retrieve user data from database
//   $sql = "SELECT * FROM users WHERE id = '$user_id' AND remember_token = '$token'";
//   $result = $conn->query($sql);

//   if ($result->num_rows > 0) {
//     // User found, log them in
//     $row = $result->fetch_assoc();
//     $_SESSION['user_id'] = $row['id'];
//     $_SESSION['username'] = $row['username'];
//     $_SESSION['type'] = $row['type'];
//     header("Location: index.php");
//     exit;
//   } else {
//     // Invalid cookie, remove it
//     setcookie('remember_me', '', time() - 3600);
//   }
// }
// Check if login form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
  // Retrieve user input from form
  $username = $_POST['username'];
  $password = $_POST['password'];
  $remember_me = isset($_POST['remember_me']);

  // Retrieve user data from database
  $sql = "SELECT * FROM users WHERE username = '$username'";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    // User found, verify password
    $row = $result->fetch_assoc();
    if (password_verify($password, $row['password'])) {
      // Password is correct, log user in
      $_SESSION['user_id'] = $row['id'];
      $_SESSION['username'] = $row['username'];
      $_SESSION['type'] = $row['type'];

      // Set "remember me" cookie if checked
      if ($remember_me) {
        $token = bin2hex(random_bytes(16)); // Generate random token
        setcookie('remember_me', json_encode(['username' => $row['username'], 'password' => $password]), time() + (86400 * 30)); // Set cookie for 30 days
        $sql = "UPDATE users SET remember_token = '$token' WHERE id = " . $row['id'];
        $conn->query($sql);
      }
      // print_r($_SESSION);
      session_write_close();

      header("Location: index.php");
      exit;
    } else {
      // Password is incorrect
      $error_message = "Invalid username or password";
    }
  } else {
    // User not found
    $error_message = "Invalid username or password";
  }

  // Close connection
  $conn->close();
}

if (isset($_COOKIE['remember_me'])) {
  $cookie_remember = json_decode($_COOKIE['remember_me']);
}
// die();

?>

<div class="container my-5 login">
  <?php if (isset($error_message)): ?>
    <p class="alert alert-danger">
      <?php echo $error_message; ?>
    </p>
  <?php endif; ?>
  <form method="post" action="<?= $_SERVER['PHP_SELF'] ?>">
    <div class="form-group mb-2">
      <label for="username" class="mb-1">Username:</label>
      <input type="text" placeholder="Username" class="form-control" id="username" name="username" value="<?php if (isset($_POST['username']) && !empty($_POST['username'])) {
        echo $_POST['username'];
      }
      if (isset($cookie_remember) && !isset($_POST['username'])) {
        echo $cookie_remember->username;
      } ?>"
        required>
    </div>
    <div class="form-group mb-2">
      <label for="password" class="mb-1">Password:</label>
      <input type="password" placeholder="Password" class="form-control" id="password" value="<?php if (isset($cookie_remember) && !isset($_POST['password'])) {
        echo $cookie_remember->password;
      } ?>" name="password"
        required>
    </div>
    <div class="form-check">
      <input class="form-check-input" type="checkbox" value="" name="remember_me" id="flexCheckChecked" checked>
      <label class="form-check-label" for="flexCheckChecked">
        Remember Me
      </label>
    </div>
    <button type="submit" class="btn mt-2 w-100">Login</button>
    <hr>
    <div class="d-flex justify-content-between">
      <a href="./reset-password.php" class="btn-link">Reset Password</a>
      <a href="./register.php" class="btn-link">New student sign up</a>
    </div>
  </form>
</div>

<?php include('components/footer.php'); ?>