<?php
$nav = "";
$title = "Purchases";
include '../config/database.php';
include('./components/header.php');

if ($_SESSION['type'] != 'admin') {
  header("Location: ../login.php");
  exit;
}

$sql = "SELECT * FROM purchase ORDER BY id DESC";
$purchase = mysqli_query($conn, $sql);

if (isset($_POST['update'])) {
  $id = $_POST['id'];
  $status = $_POST['status'];

  $query = "UPDATE purchase SET status = '$status' WHERE id = $id";

  if (mysqli_query($conn, $query)) {
    header('Location: purchases.php');
  } else {
    echo "Error updating record: " . mysqli_error($conn);
  }
}
?>

<div class="container mt-5">
  <div class="row">

    <div class="col-md-12">
      <h2>Purchases</h2>
    </div>
  </div>
  <table class="table">
    <thead>
      <tr>
        <th scope="col">Item code</th>
        <th scope="col">Invoice no.</th>
        <th scope="col">Purchased date</th>
        <th scope="col">Item</th>
        <th scope="col">Status</th>
        <th scope="col">Handle</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = mysqli_fetch_assoc($purchase)) : ?>
        <tr>
          <th scope="row"><?= $row['item_code'] ?></th>
          <td><?= $row['invoice_no'] ?></td>
          <td><?= $row['purchase_date'] ?></td>
          <td><?= $row['item_name'] ?></td>
          <form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
            <input type="hidden" name="id" value="<?= $row['id'] ?>" />
            <td>
              <select name="status" id="status" class="form-control">
                <option value="processing" <?= $row['status'] == 'processing' ? 'selected' : "" ?>>Processing</option>
                <option value="shipping" <?= $row['status'] == 'shipping' ? 'selected' : "" ?>>Shipping</option>
                <option value="ready_for_pickup" <?= $row['status'] == 'ready_for_pickup' ? 'selected' : "" ?>>Ready for Pickup</option>
                <option value="contact_admin_staff" <?= $row['status'] == 'contact_admin_staff' ? 'selected' : "" ?>>Contact admin staff</option>
              </select>
            </td>
            <td><button type="submit" name="update" class="btn btn-info btn-sm text-white">Update</button></td>
          </form>
        </tr>
      <?php endwhile;
      ?>
    </tbody>
  </table>
</div>

<?php include('./components/footer.php'); ?>