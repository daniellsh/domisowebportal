<?php
$nav = "";
$title = "Products";
include '../config/database.php';
include('./components/header.php');

if ($_SESSION['type'] !== 'admin') {
  header("Location:../login.php");
  exit();
}

if (isset($_POST['create'])) {
  $errors = array();

  // Define error messages array

  // Sanitize input data
  $product_name = htmlspecialchars($_POST['product_name']);
  $price = htmlspecialchars($_POST['price']);
  $description = htmlspecialchars($_POST['description']);
  $product_id = $_POST['product_id'];
  $in_stock = isset($_POST['in_stock']) ? 1 : 0;
  $product_code = substr(md5(uniqid(mt_rand(), true)), 0, 5);

  $success = null;
  // Validate input data
  if (empty($product_name)) {
    $errors["product_name"] = "Product Name is required";
  }
  if (empty($price)) {
    $errors["price"] = "Price is required";
  }
  if (empty($description)) {
    $errors["description"] = "Description is required";
  }
  // If no errors, insert user data into database
  if (empty($errors)) {
    $upload_dir = "../uploads/products/";
    $sql = "SELECT * FROM products WHERE id = '$product_id'";

    // Execute the query
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    if (!empty($_FILES['thumbnail']['name'])) {
      $thumbnail = $_FILES['thumbnail']['name'];
      $thumbnail_name = uniqid() . '.' . pathinfo($thumbnail, PATHINFO_EXTENSION);
      move_uploaded_file($_FILES['thumbnail']['tmp_name'], $upload_dir . $thumbnail_name);
    } else {
      $thumbnail_name = $row['thumbnail'] ?? null;
    }
    if (!empty($_FILES['image_1']['name'])) {
      $image_1 = $_FILES['image_1']['name'];
      $image_1_name = uniqid() . '.' . pathinfo($image_1, PATHINFO_EXTENSION);
      move_uploaded_file($_FILES['image_1']['tmp_name'], $upload_dir . $image_1_name);
    } else {
      $image_1_name = $row['image_1'] ?? null;
    }
    if (!empty($_FILES['image_2']['name'])) {
      $image_2 = $_FILES['image_2']['name'];
      $image_2_name = uniqid() . '.' . pathinfo($image_2, PATHINFO_EXTENSION);
      move_uploaded_file($_FILES['image_2']['tmp_name'], $upload_dir . $image_2_name);
    } else {
      $image_2_name = $row['image_2'] ?? null;
    }
    if (!empty($_FILES['image_3']['name'])) {
      $image_3 = $_FILES['image_3']['name'];
      $image_3_name = uniqid() . '.' . pathinfo($image_3, PATHINFO_EXTENSION);
      move_uploaded_file($_FILES['image_3']['tmp_name'], $upload_dir . $image_3_name);
    } else {
      $image_3_name = $row['image_3'] ?? null;
    }

    // Prepare SQL statement
    $sql = "INSERT INTO products ( name, price, in_stock, description, thumbnail, image1, image2, image3 , product_code) VALUES ('$product_name', '$price', '$in_stock', '$description', '$thumbnail_name', '$image_1_name', '$image_2_name', '$image_3_name' , '$product_code')";

    // Execute statement and check for success
    if ($conn->query($sql)) {
      $success = "Product Created successfully";
      unset($_POST);
    } else {
      echo "Something went error try again later";
    }
  }
}

if (isset($_GET['delete'])) {
  $product_id = $_GET['delete'];

  $sql = "SELECT image1, image2, image3, thumbnail FROM products WHERE id = $product_id";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $image1_path = "../uploads/products/" . $row['image1'];
    $image2_path = "../uploads/products/" . $row['image2'];
    $image3_path = "../uploads/products/" . $row['image3'];
    $thumbnail_path = "../uploads/products/" . $row['thumbnail'];

    // Delete the images from the server
    if (file_exists($image1_path)) {
      unlink($image1_path);
    }
    if (file_exists($image2_path)) {
      unlink($image2_path);
    }
    if (file_exists($image3_path)) {
      unlink($image3_path);
    }
    if (file_exists($thumbnail_path)) {
      unlink($thumbnail_path);
    }

    // Delete the product from the database
    $sql = "DELETE FROM products WHERE id = $product_id";
    if ($conn->query($sql) === TRUE) {
      $success = "Product deleted successfully";
    } else {
      echo "Error deleting product: " . $conn->error;
    }
  } else {
    echo "Product not found";
  }
}

if (isset($_GET['edit'])) {
  $edit = true;
  $product_id = $_GET['edit'];

  $sql = "SELECT * FROM products WHERE id = $product_id";
  $result = mysqli_query($conn, $sql);

  if (mysqli_num_rows($result) == 1) {
    $product = mysqli_fetch_assoc($result);
  } else {
    header('Location: products.php');
    exit;
  }
}

if (isset($_POST['update'])) {
  $errors = array();

  // Define error messages array

  // Sanitize input data
  $product_name = htmlspecialchars($_POST['product_name']);
  $price =  intval(htmlspecialchars($_POST['price']));
  $description = htmlspecialchars($_POST['description']);
  $product_id = $_POST['product_id'];
  $in_stock = isset($_POST['in_stock']) ? 1 : 0;
  $success = null;
  // Validate input data
  if (empty($product_name)) {
    $errors["product_name"] = "Product Name is required";
  }
  if (empty($price)) {
    $errors["price"] = "Price is required";
  }
  if (empty($description)) {
    $errors["description"] = "Description is required";
  }
  // If no errors, insert user data into database
  if (empty($errors)) {
    $upload_dir = "../uploads/products/";
    $sql = "SELECT * FROM products WHERE id = '$product_id'";

    // Execute the query
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    if (!empty($_FILES['thumbnail']['name'])) {
      $thumbnail = $_FILES['thumbnail']['name'];
      $thumbnail_name = uniqid() . '.' . pathinfo($thumbnail, PATHINFO_EXTENSION);
      move_uploaded_file($_FILES['thumbnail']['tmp_name'], $upload_dir . $thumbnail_name);
    } else {
      $thumbnail_name = $row['thumbnail'] ?? null;
    }
    if (!empty($_FILES['image_1']['name'])) {
      $image_1 = $_FILES['image_1']['name'];
      $image_1_name = uniqid() . '.' . pathinfo($image_1, PATHINFO_EXTENSION);
      move_uploaded_file($_FILES['image_1']['tmp_name'], $upload_dir . $image_1_name);
    } else {
      $image_1_name = $row['image1'] ?? null;
    }
    if (!empty($_FILES['image_2']['name'])) {
      $image_2 = $_FILES['image_2']['name'];
      $image_2_name = uniqid() . '.' . pathinfo($image_2, PATHINFO_EXTENSION);
      move_uploaded_file($_FILES['image_2']['tmp_name'], $upload_dir . $image_2_name);
    } else {
      $image_2_name = $row['image2'] ?? null;
    }
    if (!empty($_FILES['image_3']['name'])) {
      $image_3 = $_FILES['image_3']['name'];
      $image_3_name = uniqid() . '.' . pathinfo($image_3, PATHINFO_EXTENSION);
      move_uploaded_file($_FILES['image_3']['tmp_name'], $upload_dir . $image_3_name);
    } else {
      $image_3_name = $row['image3'] ?? null;
    }

    // Prepare SQL statement
    $sql = "UPDATE products SET name='$product_name', price='$price', in_stock = '$in_stock', description = '$description', thumbnail = '$thumbnail_name', image1 = '$image_1_name', image2 = '$image_2_name', image3 = '$image_3_name' WHERE id = '$product_id'";

    // Execute statement and check for success
    if ($conn->query($sql)) {
      $success = "Product Updated successfully";
      unset($_POST);
      header("Refresh:0");
    } else {
      echo "Something went error try again later";
    }
  }
}
?>

<div class="container my-5">
  <div class="row">
    <div class="col-md-12">
      <h2><?= (isset($edit)) ? "Edit Product" : "Add Product" ?></h2>
      <?php if (isset($success)) : ?>
        <div class="alert alert-success">
          <?= $success ?>
        </div>
      <?php endif; ?>
      <form method="post" action="<?php $_SERVER['PHP_SELF'] ?>" enctype="multipart/form-data">
        <input type="hidden" name="product_id" value="<?= $product['id'] ?>" />
        <div class="row">
          <div class="col-md-4 mb-2">
            <div class="form-group">
              <label for="product_name">Product Name:</label>
              <input type="text" class="form-control" id="product_name" name="product_name" placeholder="Product Name" value="<?= $product['name'] ?? "" ?>" required>
              <?php if (isset($errors['product_name'])) : ?>
                <small class="text-error"><?= $errors['product_name'] ?></small>
              <?php endif; ?>
            </div>
          </div>
          <div class="col-md-4 mb-2">
            <div class="form-group">
              <label for="price">Price :</label>
              <input type="number" class="form-control" id="price" name="price" placeholder="Price" value="<?= $product['price'] ?? "" ?>" required>
              <?php if (isset($errors['price'])) { ?>
                <small class="text-error"><?= $errors['price'] ?></small>
              <?php } ?>
            </div>
          </div>
          <div class="col-md-4 mb-2 d-flex align-items-center">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="in_stock" <?php if (isset($product['in_stock'])) {
                                                                                echo $product['in_stock'] ? "checked" : "";
                                                                              } ?> id="flexCheckDefault">
              <label class="form-check-label" for="flexCheckDefault">
                In Stock
              </label>
            </div>
          </div>
          <div class="col-md-12 mb-2">
            <div class="form-group">
              <label for="description">Description :</label>
              <textarea name="description" cols="30" rows="10" class="form-control" placeholder="Description"><?= $product['description'] ?? "" ?></textarea>
              <?php if (isset($errors['description'])) : ?>
                <small class="text-error"><?= $errors['description'] ?></small>
              <?php endif; ?>
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <div class="mb-2">
              <label for="thumbnail" class="form-label">Thumbnail</label>
              <input class="form-control" id="thumbnail" type="file" name="thumbnail" />
            </div>
            <img src="<?= empty($product['thumbnail']) ? "../assets/images/noimage.png" : '../uploads/products/' . $product['thumbnail'] ?>" style="width: 100%;height:200px;object-fit:cover;" alt="Thumbnail">
          </div>
          <div class="col-md-3 mb-3">
            <div class="mb-2">
              <label for="image-1" class="form-label">Image 1</label>
              <input class="form-control" id="image-1" type="file" name="image_1" />
            </div>
            <img src="<?= empty($product['image1']) ? "../assets/images/noimage.png" : '../uploads/products/' . $product['image1'] ?>" style="width: 100%;height:200px;object-fit:cover;" alt="Image 1">
          </div>
          <div class="col-md-3 mb-3">
            <div class="mb-2">
              <label for="image-2" class="form-label">Image 2</label>
              <input class="form-control" id="image-2" type="file" name="image_2" />
            </div>
            <img src="<?= empty($product['image2']) ? "../assets/images/noimage.png" : '../uploads/products/' . $product['image2'] ?>" style="width: 100%;height:200px;object-fit:cover;" alt="Image 2">
          </div>
          <div class="col-md-3 mb-3">
            <div class="mb-2">
              <label for="image-3" class="form-label">Image 3</label>
              <input class="form-control" id="image-3" type="file" name="image_3" />
            </div>
            <img src="<?= empty($product['image3']) ? "../assets/images/noimage.png" : '../uploads/products/' . $product['image3'] ?>" style="width: 100%;height:200px;object-fit:cover;" alt="Banner">
          </div>
        </div>
        <?php if (isset($edit)) : ?>
          <button type="submit" name="update" class="btn btn-primary mb-4">Edit Product</button>
        <?php else : ?>
          <button type="submit" name="create" class="btn btn-primary mb-4">Add Product</button>
        <?php endif; ?>
      </form>

    </div>
  </div>
  <table class="table bg-white">
    <thead>
      <tr>
        <th>#</th>
        <th>Product Name</th>
        <th>Thumbnail</th>
        <th>Price</th>
        <th>Description</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $sql = "SELECT * FROM products";
      $result = mysqli_query($conn, $sql);

      // Loop through each user and display data in a row
      if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) { ?>
          <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['name']; ?></td>
            <td><img src='<?= empty($row['thumbnail']) ? "../assets/images/noimage.png" : '../uploads/products/' . $row['thumbnail'] ?>' class='rounded-circle border border-dark' style='width:50px;height:50px;object-fit:cover;' /></td>
            <td>$<?php echo $row['price']; ?></td>
            <td><?php echo substr($row['description'], 0, 20) . '...'; ?></td>
            <td><?php echo $row['in_stock'] == 1 ? "<span class='badge bg-success'>In Stock</span>" : "<span class='badge bg-danger'>Out of Stock</span>"; ?></td>
            <td>
              <a href='./products.php?edit=<?php echo $row['id']; ?>' class='btn btn-primary btn-sm edit-btn mx-1' data-toggle='modal' data-target='#editModal'>Edit</a>
              <button class='btn btn-danger btn-sm delete-btn mx-1' onclick='confirmDelete(<?php echo $row['id']; ?>)'>Delete</button>
            </td>
          </tr>

      <?php }
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
      window.location.href = './products.php?delete=' + id;
    }
  }
</script>

<?php include('./components/footer.php'); ?>