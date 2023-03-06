<?php
$nav = "eshop";
$title = "E-shop Detials";
include './config/database.php';
include('components/header.php');

if (isset($_POST['order'])) {
  if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
  } else {
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    // Check if the product already exists in the orders table
    $record = mysqli_query($conn, "SELECT * FROM orders WHERE user_id = '$user_id' AND product_id = '" . $product_id . "'");
    if (mysqli_num_rows($record) > 0) {
      // Product already exists, update its quantity
      $result = mysqli_query($conn, "UPDATE orders SET quantity = quantity + " . intval($quantity) . " WHERE product_id = '" . $product_id . "'");
      if ($result) {
        header('Location: cart.php');
        exit();
      } else {
        echo "Error placing order: " . mysqli_error($conn);
      }
    } else {
      // Product does not exist, insert a new order
      $result = mysqli_query($conn, "INSERT INTO orders (user_id, product_id, quantity , price) VALUES ('$user_id', '$product_id', '$quantity' , '$price')");
      if ($result) {
        header('Location: cart.php');
        exit();
      } else {
        echo "Error placing order: " . mysqli_error($conn);
      }
    }
  }
}

if (!isset($_GET['id'])) {
  header('Location: eshop.php');
}


if (isset($_GET['id']) && !empty($_GET['id'])) {
  // Sanitize and escape the ID parameter to prevent SQL injection
  $id = htmlspecialchars($_GET['id']);

  // Query to retrieve a single product by ID
  $sql = "SELECT * FROM products WHERE id = '$id'";
  $result = mysqli_query($conn, $sql);

  // Check if a product was found with the given ID
  if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
  } else {
    header('Location: eshop.php');
  }
}



?>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="post" action="<?= $_SERVER['PHP_SELF'] ?>">

        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Purchase</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="product_id" value="<?= $row['id'] ?>" />
          <div class="row mb-3">
            <label for="inputEmail3" class="col-sm-4 col-form-label">Item Code</label>
            <div class="col-sm-8">
              <span>EAF245</span>
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-4 col-form-label">Item</label>
            <div class="col-sm-8">
              <span><?= $row['name'] ?></span>
            </div>
          </div>
          <div class="row mb-3">
            <input type="hidden" name="price" value="<?= $row['price'] ?>" />
            <label class="col-sm-4 col-form-label">Price</label>
            <div class="col-sm-8">
              <span>$<?= $row['price'] ?></span>
            </div>
          </div>
          <div class="row mb-3">
            <label for="inputEmail3" class="col-sm-4 col-form-label">Quantity</label>
            <div class="col-sm-8 d-flex gap-2 align-items-center">
              <a class="btn btn-sm btn-primary rounded-full plus-btn">+</a>
              <input type="number" id="quantity" name="quantity" value="1" style="width:60px;">
              <a class="btn btn-sm btn-primary rounded-full minus-btn">-</a>
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <?php if ($row['in_stock']) : ?>
            <button type="submit" name="order" class="btn btn-primary">Confirm</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <?php else : ?>
            <h5 class="text-center text-danger" style="flex:1;">Out of stock</h5>
          <?php endif; ?>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="container course py-5">
  <div class="row">
    <div class="col-md-6 col-lg-4">
      <img src="<?= empty($row['thumbnail']) ? "./assets/images/noimage.png" : './uploads/products/' . $row['thumbnail'] ?>" class="img-fluid" />
    </div>
    <div class="col-md-6 col-lg-8">
      <h1 class="h1"><?= $row['name']  ?></h1>
      <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex flex-column">
          <span class="price h4">HK$<?= $row['price'] ?></span>
          <?= $row['in_stock'] ? '<span class="is-stock h4 badge bg-success">In Stock</span>' : '<span class="is-stock h4 badge bg-danger">Out of Stock</span>'; ?>
        </div>
        <div class="d-flex gap-2">
          <div class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Buy</div>
          <div class="btn btn-warning" onclick="copyPageLink()">Share</div>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <p><?= $row['description']  ?></p>
  <div class="row g-4">
    <div class="col-sm-12 col-md-4">
      <img src="<?= empty($row['image1']) ? "./assets/images/noimage.png" : './uploads/products/' . $row['image1'] ?>" class="img-fluid" />
    </div>
    <div class="col-sm-12 col-md-4">
      <img src="<?= empty($row['image2']) ? "./assets/images/noimage.png" : './uploads/products/' . $row['image2'] ?>" class="img-fluid" />
    </div>
    <div class="col-sm-12 col-md-4">
      <img src="<?= empty($row['image3']) ? "./assets/images/noimage.png" : './uploads/products/' . $row['image3'] ?>" class="img-fluid" />
    </div>
  </div>
</div>

<?php include('components/footer.php'); ?>