<?php
$nav = "eshop";
$title = "E-shop";
include './config/database.php';
include('components/header.php');
?>

<div class="container course py-5">
  <h1>E-shop</h1>
  <div class="row">
    <?php
    // Query to retrieve product data
    $sql = "SELECT * FROM products ORDER BY id DESC";

    $result = mysqli_query($conn, $sql);

    // Loop through the result set and display each product in a card
    while ($row = mysqli_fetch_assoc($result)) {
    ?>
      <div class="col-sm-12 col-md-6 col-lg-3 mb-2">
        <a href="./eshopdetails.php?id=<?= $row['id'] ?>">
          <div class="card">
            <img src="<?= empty($row['thumbnail']) ? "./assets/images/noimage.png" : './uploads/products/' . $row['thumbnail'] ?>" class="card-img-top" alt="<?php echo $row['name']; ?>" />
            <div class="card-body">
              <div class="d-flex justify-content-between mb-3">
                <h5 class="mb-0"><?php echo $row['name']; ?></h5>
                <h5 class="text-dark mb-0">$<?php echo $row['price']; ?></h5>
              </div>

              <div class="d-flex justify-content-between mb-2">
                <p class="text-muted mb-0"><?= $row['in_stock'] ? '<span class="badge bg-success">In Stock</span>' : '<span class="badge bg-danger">Out of Stock</span>'; ?></p>
              </div>
            </div>
          </div>
        </a>
      </div>
    <?php
    }

    // Close the database connection
    mysqli_close($conn);
    ?>

  </div>
</div>

<?php include('components/footer.php'); ?>