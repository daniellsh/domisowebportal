<?php
include './config/database.php';


$nav = "";
$title = "My Cart";
include('components/header.php');
if (isset($_POST['checkout'])) {

  $count = count($_POST['data']['code']);
  for ($i = 0; $i < $count; $i++) {
    $item_code = $_POST['data']['code'][$i];
    $item_name = $_POST['data']['name'][$i];
    $order_id = $_POST['data']['id'][$i];
    $status = 'processing';
    $user_id = $_SESSION['user_id'];
    $invoice_no = substr(md5(uniqid(mt_rand(), true)), 0, 5);
    $sql = "INSERT INTO purchase (item_code, invoice_no, purchase_date, item_name, status , user_id) 
        VALUES ('$item_code', '$invoice_no', CURRENT_DATE, '$item_name', '$status' , '$user_id')";
    if (mysqli_query($conn, $sql)) {
      mysqli_query($conn, "DELETE FROM orders WHERE id = '$order_id'");
    }
  }
  header('Location: my-purchase.php');
  exit();
}


$user_id = $_SESSION['user_id'];
// $sql = "SELECT orders.id, orders.quantity, orders.price, products.name , products.product_code, courses.course_name, courses.course_id
//         FROM orders 
//         INNER JOIN products ON orders.product_id = products.id AND orders.user_id = '$user_id'
//         INNER JOIN courses ON orders.course_id = courses.course_id AND orders.user_id = '$user_id'";

$sql = "SELECT orders.id, orders.quantity, orders.price, products.name, products.product_code, courses.course_name, courses.course_id
        FROM orders 
        LEFT JOIN products ON orders.product_id = products.id
        LEFT JOIN courses ON orders.course_id = courses.course_id
        WHERE orders.user_id = '$user_id'";

// Execute query
$result = mysqli_query($conn, $sql);
$total = 0;
$count = mysqli_num_rows($result);
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}


?>
<div class="container py-5">
  <h1>My Cart (
    <?= $count ?>)
  </h1>
  <hr />
  <form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
    <table class="table">
      <thead>
        <tr>
          <th scope="col">Item code</th>
          <th scope="col">Item</th>
          <th scope="col">Price (HK$)</th>
          <th scope="col">Qty</th>
          <th scope="col">Subtotal</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <tr>
            <input type="hidden" value="<?= $row['id'] ?>" name="data[id][]" />
            <td scope="row">
              <?php if (isset($row['product_code']))
                echo $row['product_code']; ?>
              <?php if (isset($row['course_id']))
                echo 'course ' . $row['course_id']; ?>
            </td>
            <input type="hidden" name="data[code][]" value="<?= $row['product_code'] ?>" />
            <td>
              <?php if (isset($row['name']))
                echo $row['name']; ?>
              <?php if (isset($row['course_name']))
                echo $row['course_name']; ?>
            </td>
            <input type="hidden" name="data[name][]" value="<?= $row['name'] ?>" />
            <input type="hidden" class="price" value="<?= $row['price'] ?>" />
            <td>$
              <?= $row['price'] ?>
            </td>
            <td class="d-flex gap-2">
              <a class="btn btn-cart btn-sm btn-primary rounded-circle cart-plus-btn">+</a>
              <input type="number" value="<?= $row['quantity'] ?>" class="quantity" name="quantity" style="width:80px" />
              <a class="btn btn-cart btn-sm btn-primary rounded-circle cart-minus-btn">-</a>
            </td>
            <td class="total">$
              <?= intval($row['price']) * intval($row['quantity']) ?>
            </td>
          </tr>
          <?php
          $total += intval($row['price']) * intval($row['quantity']);
        endwhile; ?>
      </tbody>
    </table>
    <h1 class="text-end cart-total" id="fulltotal">
      Total : $
      <?= $total ?>
    </h1>
    <button class="btn btn-primary" type="submit" name="checkout">Continue</button>
  </form>

</div>

<?php include('components/footer.php'); ?>