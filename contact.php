<?php
$nav = "contact";
$title = "Contact Us";
include('components/header.php');

if (isset($_POST['send'])) {
  $to = $email_address;
  $subject = "Message From SChool";

  $message = $_POST['message'];

  $header = "From:" . $_POST['email'] . "\r\n";
  $header .= "Cc:afgh@somedomain.com \r\n";
  $header .= "MIME-Version: 1.0\r\n";
  $header .= "Content-type: text/html\r\n";

  $retval = mail($to, $subject, $message, $header);

  if ($retval == true) {
    $success =  "Message sent successfully";
  } else {
    echo "Message could not be sent...";
  }
}

?>

<div class="container my-5 contact">
  <h1>Contact Us</h1>
  <?php if (isset($success)) : ?>
    <div class="alert alert-success">
      <?= $success ?>
    </div>
  <?php endif; ?>
  <form method="post" action="<?= $_SERVER['PHP_SELF'] ?>">
    <div class="form-group mb-2">
      <label for="name" class="mb-1">Name:</label>
      <input type="text" placeholder="Name" class="form-control" id="name" name="name" required>
    </div>
    <div class="form-group mb-2">
      <label for="email" class="mb-1">Email:</label>
      <input type="email" placeholder="E-mail Address" class="form-control" id="email" name="email" required>
    </div>
    <div class="form-group mb-2">
      <label for="contact" class="mb-1">Contact:</label>
      <input type="phone" placeholder="Contact" class="form-control" id="contact" name="contact" required>
    </div>
    <div class="form-group mb-2">
      <label for="message" class="mb-1">Message:</label>
      <textarea class="form-control" id="message" placeholder="Message" name="message" rows="5" required></textarea>
    </div>
    <button type="submit" name="send" class="btn mt-2">Submit</button>
  </form>
</div>
<div class="container">
  <div id="map" height="450" style="border:0;width:100%;"></div>

</div>
<script src="https://maps.googleapis.com/maps/api/js?key=<?= $google_api ?>"></script>
<?php
// 3. Prepare the SQL query
$sql = "SELECT * FROM site_content WHERE id = 1"; // Replace "my_table" with your table name and "id" with the primary key column name

// 4. Execute the query and get the result
$result = mysqli_query($conn, $sql);

// 5. Check if the query was successful
if ($result && $result->num_rows > 0) {
  // 6. Get the data from the first row
  $row = $result->fetch_assoc();

  // 7. Print the data (replace "column_name" with the actual column name)
  $latitude = $row['latitude'];
  $longitude = $row['longitude'];
  // Add more columns as needed;
} else {
  $latitude = 37.7749;
  $longitude = -122.4194;
}
// Replace these variables with your dynamic data


echo '<script>
  function initMap() {
    var center = { lat: ' . $latitude . ', lng: ' . $longitude . ' };
    
    var map = new google.maps.Map(document.getElementById(\'map\'), {
      zoom: 10,
      center: center
    });
  }
  
  initMap();
</script>';
?>

<?php include('components/footer.php'); ?>