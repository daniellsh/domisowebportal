<?php
$nav = "";
$title = "Dashboard";
include '../config/database.php';
include('./components/header.php');

if ($_SESSION['type'] != 'admin') {
  header("Location: ../login.php");
  exit;
}

// Execute Users the SQL query
$sql = "SELECT * FROM users";
$users = mysqli_query($conn, $sql);
$studnets = 0;
$teachers = 0;
$courses_count = 0;
while ($row = mysqli_fetch_assoc($users)) {
  if ($row['type'] == 'student') {
    $studnets++;
  } elseif ($row['type'] == 'teacher') {
    $teachers++;
  }
}

// Execute Products the SQL query
$sql = "SELECT * FROM products";
$products = mysqli_query($conn, $sql);

$sql = "SELECT * FROM courses";
$courses = mysqli_query($conn, $sql);
$courses_count = mysqli_num_rows($courses);

$sql = "SELECT * FROM purchase LIMIT 5";
$purchase = mysqli_query($conn, $sql);
?>
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center">
    <h1 class="mb-3">Dashboard</h1>
    <a href="./sitesetting.php" class="btn btn-primary">Site Settings <i class="fa-solid fa-gear mx-2"></i></a>
  </div>
  <div class="row gx-5">
    <div class="col-xxl-3 col-md-6 mb-5">
      <div class="card card-raised text-white border-0 bg-primary">
        <div class="card-body px-4">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="me-2">
              <div class="display-5"><?= $studnets ?></div>
              <div class="card-text">Students</div>
            </div>
            <div class="icon-circle bg-primary text-white"><i class="fa-solid fa-users"></i></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xxl-3 col-md-6 mb-5">
      <div class="card card-raised text-white border-0 bg-warning">
        <div class="card-body px-4">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="me-2">
              <div class="display-5"><?= $teachers ?></div>
              <div class="card-text">Teachers</div>
            </div>
            <div class="icon-circle bg-warning text-white"><i class="fa-solid fa-user-graduate"></i></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xxl-3 col-md-6 mb-5">
      <div class="card card-raised text-white border-0 bg-secondary">
        <div class="card-body px-4">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="me-2">
              <div class="display-5"><?= $courses_count ?></div>
              <div class="card-text">Courses</div>
            </div>
            <div class="icon-circle bg-secondary text-white"><i class="fa-solid fa-film"></i></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xxl-3 col-md-6 mb-5">
      <div class="card card-raised text-white border-0 bg-info">
        <div class="card-body px-4">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="me-2">
              <div class="display-5"><?= mysqli_num_rows($products) ?></div>
              <div class="card-text">Products</div>
            </div>
            <div class="icon-circle bg-info text-white"><i class="fa-solid fa-store"></i></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12 col-lg-6 mb-3">
      <div class="card">
        <div class="card-content p-3">
          <h3>Students</h3>
          <table class="table">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Username</th>
                <th scope="col">E-mail</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // $sql = "SELECT * FROM users";
              // $users = mysqli_query($conn, $sql);
              $count = 0;
              $users = mysqli_query($conn, "SELECT * FROM users");
              while ($row = mysqli_fetch_assoc($users)) :
                if ($count == 5) break;
                if ($row['type'] != 'student') continue;
                $count++;
              ?>
                <tr>
                  <th scope="row"><?= $count ?></th>
                  <td><?= $row['username'] ?></td>
                  <td><?= $row['email'] ?></td>
                </tr>
              <?php endwhile;
              if ($count == 0) {
                echo "<tr><td colspan='4' class='text-center'>No Students found.</td></tr>";
              }
              ?>
            </tbody>
          </table>
          <a href="./users.php" class="btn btn-primary btn-sm">View All <i class="fa-solid fa-eye mx-2"></i></a>
        </div>
      </div>
    </div>
    <div class="col-md-12 col-lg-6 mb-3">
      <div class="card">
        <div class="card-content p-3">
          <h3>Teachers</h3>
          <table class="table">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Username</th>
                <th scope="col">E-mail</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql = "SELECT * FROM users";
              $users = mysqli_query($conn, $sql);
              $count = 0;
              while ($row = mysqli_fetch_assoc($users)) :
                if ($count == 5) break;
                if ($row['type'] != 'teacher') continue;
                $count++;
              ?>
                <tr>
                  <th scope="row"><?= $count ?></th>
                  <td><?= $row['username'] ?></td>
                  <td><?= $row['email'] ?></td>
                </tr>
              <?php endwhile;
              if ($count == 0) {
                echo "<tr><td colspan='4' class='text-center'>No Teachers found.</td></tr>";
              } ?>
            </tbody>
          </table>
          <a href="./users.php" class="btn btn-primary btn-sm">View All <i class="fa-solid fa-eye mx-2"></i></a>
        </div>
      </div>
    </div>
    <div class="col-md-12 col-lg-6 mb-3">
      <div class="card">
        <div class="card-content p-3">
          <h3>Courses</h3>
          <table class="table">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Course Name</th>
                <th scope="col">Course Price</th>
                <th scope="col">Course Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $count = 0;
              while ($row = mysqli_fetch_assoc($courses)) :
                if ($count == 5) break;
                $count++;
              ?>
                <tr>
                  <th scope="row"><?= $count ?></th>
                  <td><?= $row['course_name'] ?></td>
                  <td>$<?= $row['course_price'] ?></td>
                  <td><?= $row['course_date'] ?></td>
                </tr>
              <?php endwhile;
              if ($count == 0) {
                echo "<tr><td colspan='4' class='text-center'>No Teachers found.</td></tr>";
              } ?>
            </tbody>
          </table>
          <a href="./courses.php" class="btn btn-primary btn-sm">View All <i class="fa-solid fa-eye mx-2"></i></a>
        </div>
      </div>
    </div>
    <div class="col-md-12 col-lg-6 mb-3">
      <div class="card">
        <div class="card-content p-3">
          <h3>Products</h3>
          <table class="table">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Product Name</th>
                <th scope="col">Product Price</th>
                <th scope="col">In stock</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $count = 0;
              while ($row = mysqli_fetch_assoc($products)) :
                if ($count == 5) break;
                $count++;
              ?>
                <tr>
                  <th scope="row"><?= $count ?></th>
                  <td><?= $row['name'] ?></td>
                  <td>$<?= $row['price'] ?></td>
                  <td>
                    <?= $row['in_stock'] ? '<span class="badge bg-success">In Stock</span>' : '<span class="badge bg-danger">Out of Stock</span>'; ?>
                  </td>
                </tr>
              <?php endwhile;
              if ($count == 0) {
                echo "<tr><td colspan='4' class='text-center'>No Products found.</td></tr>";
              }
              ?>
            </tbody>
          </table>
          <a href="./products.php" class="btn btn-primary btn-sm">View All <i class="fa-solid fa-eye mx-2"></i></a>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card">
        <div class="card-content p-3">
          <h3>Purchases</h3>
          <table class="table">
            <thead>
              <tr>
                <th scope="col">Item code</th>
                <th scope="col">Invoice no.</th>
                <th scope="col">Purchased date</th>
                <th scope="col">Status</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = mysqli_fetch_assoc($purchase)) : ?>
                <tr>
                  <th scope="row"><?= $row['item_code'] ?></th>
                  <td><?= $row['invoice_no'] ?></td>
                  <td><?= $row['purchase_date'] ?></td>
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
          <a href="./purchases.php" class="btn btn-primary btn-sm">View All <i class="fa-solid fa-eye mx-2"></i></a>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card">
        <div class="card-content p-3">
          <h3>Rooms</h3>
          <table class="table">
            <thead>
              <tr>
                <th scope="col">Booking ID</th>
                <th scope="col">Room</th>
                <th scope="col">Booking Date</th>
                <th scope="col">Booking Status</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // Assuming $conn is the mysqli connection
              $sql = "SELECT * FROM booking LIMIT 5";
              $result = $conn->query($sql);

              if ($result->num_rows > 0) {
                // Output data of each row
                while ($row = $result->fetch_assoc()) {
                  echo "<tr>";
                  echo "<td>" . $row["booking_id"] . "</td>";
                  echo "<td>" . $row["room"] . "</td>";
                  echo "<td>" . $row["booking_date"] . "</td>";
                  echo "<td>" . $row["booking_status"] . "</td>";
                  echo "<td>";
                  echo "</tr>";
                }
              } else {
                echo "<tr><td colspan='5'>No bookings found.</td></tr>";
              }
              ?>
            </tbody>
          </table>
          <a href="./room-booking.php" class="btn btn-primary btn-sm">View All <i class="fa-solid fa-eye mx-2"></i></a>

        </div>
      </div>
    </div>
  </div>
</div>

<?php include('./components/footer.php'); ?>