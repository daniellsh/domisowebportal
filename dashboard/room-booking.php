<?php
$nav = "";
$title = "Room";
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
  $room = htmlspecialchars($_POST['room_name']);
  $room_status = htmlspecialchars($_POST['room_status']);

  $success = null;
  // Validate input data
  if (empty($room)) {
    $errors["room_name"] = "Room Name is required";
  }

  // If no errors, insert user data into database
  if (empty($errors)) {
    // Hash the password
    $room_no = substr(md5(uniqid(mt_rand(), true)), 0, 5);
    // Prepare SQL statement
    $sql = "INSERT INTO booking (room,booking_no, booking_status , booking_date) VALUES ('$room', '$room_no' , '$room_status' , NOW())";

    // Execute statement and check for success
    if ($conn->query($sql)) {
      $success = "Room Created successfully";
      unset($_POST);
      header('Location: room-booking.php');
      exit();
    } else {
      echo "Something went error try again later";
    }
  }
}

if (isset($_GET['delete'])) {
  $room_id = $_GET['delete'];

  $sql = "DELETE FROM booking WHERE booking_id = $room_id";
  $result = mysqli_query($conn, $sql);

  if ($result) {
    header('Location: room-booking.php');
    exit();
  } else {
    echo 'Error deleting user: ' . mysqli_error($conn);
  }
}

if (isset($_GET['edit'])) {
  $edit = true;
  $room_id = $_GET['edit'];

  $sql = "SELECT * FROM booking WHERE booking_id = $room_id";
  $result = mysqli_query($conn, $sql);

  if (mysqli_num_rows($result) == 1) {
    $room = mysqli_fetch_assoc($result);
  } else {
    header('Location: room-booking.php');
    exit;
  }
}

if (isset($_POST['update'])) {


  // Sanitize input data
  $id = $_POST['booking_id'];
  $room = htmlspecialchars($_POST['room_name']);
  $room_status = htmlspecialchars($_POST['room_status']);

  $success = null;

  $query = "UPDATE booking SET room = '$room', booking_status = '$room_status' WHERE booking_id = $id";

  if (mysqli_query($conn, $query)) {
    header('Location: room-booking.php');
  } else {
    echo "Error updating record: " . mysqli_error($conn);
  }
}
?>

<div class="container mt-5">
  <div class="row">
    <div class="col-md-12">
      <h2><?= (isset($edit)) ? "Edit Room" : "Add Room" ?></h2>
      <?php if (isset($success)) : ?>
        <div class="alert alert-success">
          <?= $success ?>
        </div>
      <?php endif; ?>
      <form method="post" action="<?php $_SERVER['PHP_SELF'] ?>">
        <input type="hidden" name="booking_id" value="<?= $room['booking_id'] ?>" />
        <div class="row">
          <div class="col-md-4 mb-2">
            <div class="form-group">
              <label for="room_name">Room Name:</label>
              <input type="text" class="form-control" placeholder="Room Name" id="room_name" name="room_name" value="<?= $room['room'] ?? "" ?>" required>
              <?php if (isset($errors['room_name'])) : ?>
                <small class="text-error"><?= $errors['room_name'] ?></small>
              <?php endif; ?>
            </div>
          </div>

          <div class="col-md-4 mb-2">
            <div class="form-group">
              <label for="room_status">Booking Status:</label>
              <select name="room_status" id="bookingstatus" class="form-control">

                <option value="Confirmed" <?php if (isset($room['booking_status']) && $room['booking_status'] == "Confirmed") {
                                            echo "selected";
                                          } ?>>Confirmed</option>
                <option value="Pending" <?php if (isset($room['booking_status']) && $room['booking_status'] == "Pending") {
                                          echo "selected";
                                        } ?>>Pending</option>
                <option value="Cancelled" <?php if (isset($room['booking_status']) && $room['booking_status'] == "Cancelled") {
                                            echo "selected";
                                          } ?>>Cancelled</option>
              </select>
            </div>
          </div>
        </div>
        <?php if (isset($edit)) : ?>
          <button type="submit" name="update" class="btn btn-primary mb-4">Edit Room</button>
        <?php else : ?>
          <button type="submit" name="create" class="btn btn-primary mb-4">Add Room</button>
        <?php endif; ?>
      </form>

    </div>
  </div>
  <table class="table">
    <thead>
      <tr>
        <th scope="col">Booking ID</th>
        <th scope="col">Booking Number</th>
        <th scope="col">Room</th>
        <!-- <th scope="col">Booking Date</th> -->
        <th scope="col">Booking Status</th>
        <th scope="col">Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
      // Assuming $conn is the mysqli connection
      $sql = "SELECT * FROM booking";
      $result = $conn->query($sql);

      if ($result->num_rows > 0) {
        // Output data of each row
        while ($row = $result->fetch_assoc()) {
          echo "<tr>";
          echo "<td>" . $row["booking_id"] . "</td>";
          echo "<td>" . $row["booking_no"] . "</td>";
          echo "<td>" . $row["room"] . "</td>";
          // echo "<td>" . $row["booking_date"] . "</td>";
          echo "<td>" . $row["booking_status"] . "</td>";
          echo "<td>";
          echo "<a href='./room-booking.php?edit=" . $row['booking_id'] . "' class='btn btn-primary btn-sm edit-btn mx-1' data-toggle='modal' data-target='#editModal'>Edit</a>";
          echo "<button class='btn btn-danger btn-sm delete-btn mx-1' onclick='confirmDelete(" . $row['booking_id'] . ")'>Delete</button>";
          echo "</td>";
          echo "</tr>";
        }
      } else {
        echo "<tr><td colspan='5'>No bookings found.</td></tr>";
      }
      ?>
    </tbody>
  </table>
</div>
<script>
  function confirmDelete(id) {
    if (confirm("Are you sure you want to delete this item?")) {
      window.location.href = './room-booking.php?delete=' + id;
    }
  }
</script>

<?php include('./components/footer.php'); ?>