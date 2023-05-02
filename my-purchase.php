<?php
$nav = "";
$title = "My Purchase";
include('components/header.php');
include './config/database.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}
$user_id = $_SESSION['user_id'];
// Retrieve all data from the purchase table
$sql = "SELECT * FROM purchase WHERE user_id = '$user_id' ";
$result = mysqli_query($conn, $sql);
$count = mysqli_num_rows($result);

?>
<div class="container py-5">

  <h1>My Purchase</h1>
  <hr />
  <table class="table">
    <thead>
      <tr>
        <th scope="col">Item code</th>
        <th scope="col">Invoice no.</th>
        <th scope="col">Purchased date</th>
        <th scope="col">Item</th>
        <th scope="col">Status</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = mysqli_fetch_assoc($result)) : ?>
        <tr>
          <th scope="row"><?= $row['item_code'] ?></th>
          <td><?= $row['invoice_no'] ?></td>
          <td><?= $row['purchase_date'] ?></td>
          <td><?= $row['item_name'] ?></td>
          <td><?= ucwords(str_replace('_', " ", $row['status'])) ?></td>
        </tr>
      <?php endwhile;
      if ($count <= 0) {
      ?>
        <tr>
          <th colspan="5" class="text-center">There are no purchases</th>
        </tr>
      <?php
      }
      ?>
    </tbody>
  </table>
</div>

<?php include('components/footer.php'); ?>