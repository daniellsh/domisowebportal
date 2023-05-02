<?php
$nav = "";
$title = "Room Booking";
include('components/header.php');
include './config/database.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

if (isset($_GET['delete'])) {
  $user_id = $_SESSION['user_id'];
  $booking_id = $_GET['delete'];
  // Build the SQL query to delete the row
  $sql = "DELETE FROM user_booking WHERE user_id = '$user_id' AND booking_id = '$booking_id'";
  // Execute the query
  if (mysqli_query($conn, $sql)) {
    $success =  "Room booking canceled successfully";
  } else {
    echo "Error deleting row: " . mysqli_error($conn);
  }
}

if (isset($_POST['roombooking'])) {
  $room = $_POST['room'];
  $date = $_POST['dateselection'];
  $user_id = $_SESSION['user_id'];

  // Perform the query
  $sql = "SELECT * FROM user_booking WHERE user_id = '$user_id' AND booking_date = '$date' AND booking_id = '$room'";
  $result = mysqli_query($conn, $sql);

  // Check if any rows were returned
  if (mysqli_num_rows($result) > 0) {
    // Record exists
    $warinig = "Room already tooken";
  } else {
    // Record does not exist
    $sql = "INSERT INTO user_booking (user_id, booking_id, booking_date) VALUES ('$user_id', '$room', '$date')";

    $result = mysqli_query($conn, $sql);

    if ($result) {
      $success = "Room booking successfully";
      header("Location: room-booking.php");
      exit();
    }
  }
}

?>
<!-- Room Booking Modal -->
<div class="modal fade" id="roomBookingModal" tabindex="-1" aria-labelledby="buyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="buyModalLabel">Room Booking</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row mb-3">
            <label for="room" class="col-sm-4 col-form-label">Room Selection</label>
            <div class="col-sm-8 d-flex gap-2 align-items-center">
              <?php

              //query to fetch booking rooms
              $sql = "SELECT DISTINCT room, booking_id FROM booking ORDER BY room ASC";

              //execute query
              $result = mysqli_query($conn, $sql);

              //check if there are any rows returned
              if (mysqli_num_rows($result) > 0) {
                //start select element
                echo "<select id='room' name='room' class='form-control'>";
                //iterate through each row
                while ($row = mysqli_fetch_assoc($result)) {
                  //add option for each room
                  echo "<option value='" . $row['booking_id'] . "'>" . $row['room'] . "</option>";
                }
                //end select element
                echo "</select>";
              } else {
                echo "No booking rooms found.";
              }
              ?>
            </div>
          </div>
          <div class="row mb-3">
            <label for="dateselection" class="col-sm-4 col-form-label">Date Selection</label>
            <div class="col-sm-8">
              <input type="date" class='form-control' name="dateselection" id="dateselection" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="roombooking" class="btn btn-primary">Confirm</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>

</div>
<div class="container py-5">
  <?php if (isset($success)) : ?>
    <div class="alert alert-success">
      <?= $success ?>
    </div>
  <?php endif; ?>

  <?php if (isset($warinig)) : ?>
    <div class="alert alert-warning">
      <?= $warinig ?>
    </div>
  <?php endif; ?>
  <h1>Room Booking</h1>
  <hr />
  <div class="card">
    <div class="card-body">
      <table class="table">
        <thead>
          <tr>
            <th scope="col">Booking ID</th>
            <th scope="col">Booking Number</th>
            <th scope="col">Room</th>
            <th scope="col">Booking Date</th>
            <th scope="col">Booking Status</th>
            <th>Handle</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Assuming $conn is the mysqli connection
          $sql = "SELECT user_booking.*, booking.booking_id , booking.booking_no , booking.room , booking.room , user_booking.booking_date , booking.booking_status FROM user_booking INNER JOIN booking ON user_booking.booking_id = booking.booking_id WHERE user_booking.user_id = '$user_id'";

          $result = $conn->query($sql);
          $count = 0;
          if ($result->num_rows > 0) {
            // Output data of each row
            while ($row = $result->fetch_assoc()) {
              $count++;
              echo "<tr>";
              echo "<td>" . $count . "</td>";
              echo "<td>" . $row["booking_no"] . "</td>";
              echo "<td>" . $row["room"] . "</td>";
              echo "<td>" . date('Y-m-d', strtotime($row["booking_date"])) . "</td>";
              echo "<td>" . $row["booking_status"] . "</td>";
              echo "<td><a href='./room-booking.php?delete=" . $row['booking_id'] . "' class = 'btn btn-dark btn-sm'>Cancel</a></td>";
              echo "</tr>";
            }
          } else {
            echo "<tr><td colspan='5' class='text-center'>No bookings found.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
  <button class="btn btn-lg btn-primary mt-4" data-bs-toggle="modal" data-bs-target="#roomBookingModal">Book Room</button>


</div>

<?php include('components/footer.php'); ?>