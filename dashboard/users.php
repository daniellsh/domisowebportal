<?php
$nav = "";
$title = "Users";
include '../config/database.php';
include('./components/header.php');

if ($_SESSION['type'] != 'admin') {
	header("Location: ../login.php");
	exit;
}

if (isset($_POST['create'])) {
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
	$password = htmlspecialchars($_POST['password']);
	$type = htmlentities($_POST['type']);
	$success = null;
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

	// Query to check if email exists
	$query = "SELECT * FROM users WHERE email = '$email'";
	$result = mysqli_query($conn, $query);

	// Check if any rows were returned
	if (mysqli_num_rows($result) > 0) {
		// Email exists
		$errors["email"] = "Email exists in database";
	}


	// If no errors, insert user data into database
	if (empty($errors)) {
		// Hash the password
		$hashed_password = password_hash($password, PASSWORD_DEFAULT);

		// Prepare SQL statement
		$sql = "INSERT INTO users (first_name, last_name, dateofbirth, gender, mobile, email, username, password, type) VALUES ('$first_name', '$last_name', '$dob', '$gender', '$mobile', '$email', '$username', '$hashed_password', '$type')";

		// Execute statement and check for success
		if ($conn->query($sql)) {
			$success = "User Created successfully";
			unset($_POST);
		} else {
			echo "Something went error try again later";
		}
	}
}

if (isset($_GET['delete'])) {
	$user_id = $_GET['delete'];

	$sql = "DELETE FROM users WHERE id = $user_id";
	$result = mysqli_query($conn, $sql);

	if ($result) {
		header('Location: users.php');
	} else {
		echo 'Error deleting user: ' . mysqli_error($conn);
	}
}

if (isset($_GET['edit'])) {
	$edit = true;
	$user_id = $_GET['edit'];

	$sql = "SELECT * FROM users WHERE id = $user_id";
	$result = mysqli_query($conn, $sql);

	if (mysqli_num_rows($result) == 1) {
		$user = mysqli_fetch_assoc($result);
	} else {
		header('Location: users.php');
		exit;
	}
}

if (isset($_POST['update'])) {
	$id = $_POST['id'];
	$first_name = $_POST['first_name'];
	$last_name = $_POST['last_name'];
	$dateofbirth = $_POST['dateofbirth'];
	$gender = $_POST['gender'];
	$mobile = $_POST['mobile'];
	$email = $_POST['email'];
	$username = $_POST['username'];

	$query = "UPDATE users SET first_name = '$first_name', last_name = '$last_name', dateofbirth = '$dateofbirth', gender = '$gender', mobile = '$mobile', email = '$email', username = '$username' WHERE id = $id";

	if (mysqli_query($conn, $query)) {
		header('Location: users.php');
	} else {
		echo "Error updating record: " . mysqli_error($conn);
	}
}
?>

<div class="container mt-5">
	<div class="row">
		<div class="col-md-12">
			<h2><?= (isset($edit)) ? "Edit User" : "Add User" ?></h2>
			<?php if (isset($success)) : ?>
				<div class="alert alert-success">
					<?= $success ?>
				</div>
			<?php endif; ?>
			<form method="post" action="<?php $_SERVER['PHP_SELF'] ?>">
				<input type="hidden" name="id" value="<?= $user['id'] ?? "" ?>" />
				<div class="row">
					<div class="col-md-6 mb-2">
						<div class="form-group">
							<label for="first_name">First Name:</label>
							<input type="text" class="form-control" id="first_name" name="first_name" value="<?= $user['first_name'] ?? "" ?>" required>
							<?php if (isset($errors['first_name'])) : ?>
								<small class="text-error"><?= $errors['first_name'] ?></small>
							<?php endif; ?>
						</div>
					</div>
					<div class="col-md-6 mb-2">
						<div class="form-group">
							<label for="last_name">Last Name:</label>
							<input type="text" class="form-control" id="last_name" name="last_name" value="<?= $user['last_name'] ?? "" ?>" required>
							<?php if (isset($errors['last_name'])) { ?>
								<small class="text-error"><?= $errors['last_name'] ?></small>
							<?php } ?>
						</div>
					</div>
					<div class="col-md-6 mb-2">
						<div class="form-group">
							<label for="dateofbirth">Date of Birth:</label>
							<input type="date" class="form-control" id="dateofbirth" name="dateofbirth" value="<?= $user['dateofbirth'] ?? "" ?>" required>
							<?php if (isset($errors['dateofbirth'])) : ?>
								<small class="text-error"><?= $errors['dateofbirth'] ?></small>
							<?php endif; ?>
						</div>
					</div>
					<div class="col-md-6 mb-2">
						<div class="form-group mb-2">
							<label for="mobile" class="mb-1">Gender</label>
							<div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="gender" id="genderRadio1" value="male" <?php if (isset($user['gender']) && $user['gender'] == 'male') echo 'checked' ?>>
									<label class="form-check-label" for="genderRadio1">Male</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="gender" id="genderRadio2" value="female" <?php if (isset($user['gender']) && $user['gender'] == 'female') echo 'checked' ?>>
									<label class="form-check-label" for="genderRadio2">Female</label>
								</div>
							</div>
							<?php if (isset($errors['gender'])) : ?>
								<small class="text-error"><?= $errors['gender'] ?></small>
							<?php endif; ?>
						</div>
					</div>
					<div class="col-md-6 mb-2">
						<div class="form-group mb-2">
							<label for="mobile" class="mb-1">Type</label>
							<div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="type" id="typeRadio1" value="student" <?php if (isset($user['type']) && $user['type'] == 'student') echo 'checked' ?>>
									<label class="form-check-label" for="typeRadio1">Student</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="type" id="typeRadio2" value="teacher" <?php if (isset($user['type']) && $user['type'] == 'teacher') echo 'checked' ?>>
									<label class="form-check-label" for="typeRadio2">Teacher</label>
								</div>
							</div>
							<?php if (isset($errors['gender'])) : ?>
								<small class="text-error"><?= $errors['gender'] ?></small>
							<?php endif; ?>
						</div>
					</div>
					<div class="col-md-6 mb-2">
						<div class="form-group">
							<label for="mobile">Mobile:</label>
							<input type="text" class="form-control" id="mobile" name="mobile" value="<?= $user['mobile'] ?? "" ?>" required>
							<?php if (isset($errors['mobile'])) : ?>
								<small class="text-error"><?= $errors['mobile'] ?></small>
							<?php endif; ?>
						</div>
					</div>
					<div class="col-md-6 mb-2">
						<div class="form-group">
							<label for="email">Email:</label>
							<input type="email" class="form-control" id="email" name="email" value="<?= $user['email'] ?? "" ?>" required>
							<?php if (isset($errors['email'])) : ?>
								<small class="text-error"><?= $errors['email'] ?></small>
							<?php endif; ?>
						</div>
					</div>
					<div class="col-md-6 mb-2">
						<div class="form-group">
							<label for="username">Username:</label>
							<input type="text" class="form-control" id="username" name="username" value="<?= $user['username'] ?? "" ?>" required>
							<?php if (isset($errors['password'])) : ?>
								<small class="text-error"><?= $errors['password'] ?></small>
							<?php endif; ?>
						</div>
					</div>
					<div class="col-md-6 mb-2">
						<div class="form-group mb-2">
							<label for="password" class="mb-1">Password:</label>
							<input type="password" placeholder="Password" class="form-control" id="password" name="password">
							<?php if (isset($errors['password'])) : ?>
								<small class="text-error"><?= $errors['password'] ?></small>
							<?php endif; ?>
						</div>
					</div>
				</div>
				<?php if (isset($edit)) : ?>
					<button type="submit" name="update" class="btn btn-primary mb-4">Edit User</button>
				<?php else : ?>
					<button type="submit" name="create" class="btn btn-primary mb-4">Add User</button>
				<?php endif; ?>
			</form>

		</div>
	</div>
	<table class="table bg-white">
		<thead>
			<tr>
				<th>#</th>
				<th>First Name</th>
				<th>Last Name</th>
				<th>Date of Birth</th>
				<th>Gender</th>
				<th>Type</th>
				<th>Email</th>
				<th>Username</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$sql = "SELECT * FROM users WHERE type != 'admin' ";
			$result = mysqli_query($conn, $sql);

			// Loop through each user and display data in a row
			if (mysqli_num_rows($result) > 0) {
				while ($row = mysqli_fetch_assoc($result)) {
					echo "<tr>";
					echo "<td>" . $row['id'] . "</td>";
					echo "<td>" . $row['first_name'] . "</td>";
					echo "<td>" . $row['last_name'] . "</td>";
					echo "<td>" . $row['dateofbirth'] . "</td>";
					echo "<td>" . $row['gender'] . "</td>";
					echo "<td> <span class='bg-warning badge'>" . $row['type'] . "</span></td>";
					echo "<td>" . $row['email'] . "</td>";
					echo "<td>" . $row['username'] . "</td>";
					echo "<td>";
					echo "<a href='./users.php?edit=" . $row['id'] . "' class='btn btn-primary btn-sm edit-btn mx-1' data-toggle='modal' data-target='#editModal'>Edit</a>";
					echo "<button class='btn btn-danger btn-sm delete-btn mx-1' onclick='confirmDelete(" . $row['id'] . ")'>Delete</button>";
					echo "</td>";
					echo "</tr>";
				}
			} else {
				echo "<tr><td colspan='9' class='text-center'>No users found.</td></tr>";
			}

			// Close database connection
			mysqli_close($conn);
			?>
		</tbody>
	</table>
</div>
<script>
	function confirmDelete(id) {
		if (confirm("Are you sure you want to delete this item?")) {
			window.location.href = './users.php?delete=' + id;
		}
	}
</script>

<?php include('./components/footer.php'); ?>