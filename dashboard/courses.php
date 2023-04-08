<?php
$nav = "";
$title = "Courses";
include '../config/database.php';
include('./components/header.php');

if ($_SESSION['type'] != 'admin' && $_SESSION['type'] != 'teacher') {
  header("Location:../login.php");
  exit();
}

if (isset($_POST['sendmessage'])) {
  $course_id = $_POST['course_id'];
  $message = $_POST['message'];
  $sql = "SELECT user_id FROM enrollments WHERE course_id = $course_id";
  $result = mysqli_query($conn, $sql);
  $sender_id = $_SESSION['user_id'];

  // Insert a message into the messages table for each user
  $message_text = $message;
  while ($row = mysqli_fetch_assoc($result)) {
    $user_id = $row['user_id'];
    $sql = "INSERT INTO messages (course_id, user_id, message_text , sender_id) VALUES ($course_id, $user_id, '$message_text' , '$sender_id')";
    mysqli_query($conn, $sql);
    $success = "Message Send successfuly";
  }
}

if (isset($_GET['deletefile'])) {
  // Get the file ID from the URL
  $file_id = $_GET['deletefile'];

  // Retrieve the file path from the database
  $sql = "SELECT file_path FROM course_files WHERE id = $file_id";
  $result = mysqli_query($conn, $sql);
  if (mysqli_num_rows($result)) {
    $row = mysqli_fetch_assoc($result);
    $file_path = $row['file_path'];

    // Delete the file from the folder
    unlink('../uploads/drives/' . $file_path);

    // Delete the file record from the database
    $sql = "DELETE FROM course_files WHERE id = $file_id";
    mysqli_query($conn, $sql);

    $success = "File deleted successfully!";
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
  }
}

if (isset($_POST['create'])) {
  $errors = array();

  // Define error messages array

  // Sanitize input data
  $course_name = htmlspecialchars($_POST['course_name']);
  $price = htmlspecialchars($_POST['course_price']);
  $description = htmlspecialchars($_POST['course_description']);
  $course_id = $_POST['course_id'];
  $teacher = $_POST['teacher'];
  $course_date = $_POST['course_date'];
  $room_location = $_POST['room_location'];
  $room_session = $_POST['room_session'];
  $course_code = substr(md5(uniqid(mt_rand(), true)), 0, 5);


  $success = null;
  // Validate input data
  if (empty($course_name)) {
    $errors["course_name"] = "Course Name is required";
  }
  if (empty($price)) {
    $errors["course_price"] = "Course Price is required";
  }
  if (empty($description)) {
    $errors["course_description"] = "Course Description is required";
  }
  if (empty($course_date)) {
    $errors["course_date"] = "Course Date is required";
  }
  // Prepare SQL query to check for existing room location and session
  $query = "SELECT * FROM courses WHERE room_location = '$room_location' AND room_session = '$room_session' AND course_date = '$course_date'";

  // Execute query and get results
  $result = mysqli_query($conn, $query);

  // Check if any rows were returned
  if (mysqli_num_rows($result) > 0) {
    // Room location and session already exist in table
    $errors['room_session'] = "Room location and session already exist.";
  }

  // If no errors, insert user data into database
  if (empty($errors)) {
    $upload_dir = "../uploads/courses/";
    $sql = "SELECT * FROM courses WHERE course_id = '$course_id'";

    // Execute the query
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    if (!empty($_FILES['image_url_1']['name'])) {
      $image_url_1 = $_FILES['image_url_1']['name'];
      $image_url_1_name = uniqid() . '.' . pathinfo($image_url_1, PATHINFO_EXTENSION);
      move_uploaded_file($_FILES['image_url_1']['tmp_name'], $upload_dir . $image_url_1_name);
    } else {
      $image_url_1_name = $row['image_url_1'] ?? null;
    }

    if (!empty($_FILES['image_url_2']['name'])) {
      $image_url_2 = $_FILES['image_url_2']['name'];
      $image_url_2_name = uniqid() . '.' . pathinfo($image_url_2, PATHINFO_EXTENSION);
      move_uploaded_file($_FILES['image_url_2']['tmp_name'], $upload_dir . $image_url_2_name);
    } else {
      $image_url_2_name = $row['image_url_2'] ?? null;
    }
    if (!empty($_FILES['image_url_3']['name'])) {
      $image_url_3 = $_FILES['image_url_3']['name'];
      $image_url_3_name = uniqid() . '.' . pathinfo($image_url_1, PATHINFO_EXTENSION);
      move_uploaded_file($_FILES['image_url_3']['tmp_name'], $upload_dir . $image_url_3_name);
    } else {
      $image_url_3_name = $row['image_url_3'] ?? null;
    }
    if (!empty($_FILES['image_url_4']['name'])) {
      $image_url_4 = $_FILES['image_url_4']['name'];
      $image_url_4_name = uniqid() . '.' . pathinfo($image_url_4, PATHINFO_EXTENSION);
      move_uploaded_file($_FILES['image_url_4']['tmp_name'], $upload_dir . $image_url_4_name);
    } else {
      $image_url_4_name = $row['image_url_4'] ?? null;
    }
    // Prepare SQL statement
    $course_in_id = rand(10, 999);

    $sql = "INSERT INTO courses (course_id , course_name, course_date , room_location , room_session ,  course_price, course_description,teacher_id , course_code , image_url_1, image_url_2, image_url_3 , image_url_4) VALUES ('$course_in_id','$course_name' , '$course_date' , '$room_location' , '$room_session',  '$price' , '$description' , '$teacher' , '$course_code' , '$image_url_1_name','$image_url_2_name','$image_url_3_name','$image_url_4_name')";


    // Execute statement and check for success
    if ($conn->query($sql)) {
      // Check if files were uploaded
      if (isset($_FILES['files'])  && !empty($_FILES['files'])) {
        $errors = array();
        $success = array();

        // Loop through uploaded files
        foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {
          $file_name = $_FILES['files']['name'][$key];
          $file_size = $_FILES['files']['size'][$key];
          $file_tmp = $_FILES['files']['tmp_name'][$key];
          $file_type = $_FILES['files']['type'][$key];

          // Check for errors
          if ($file_size > 2097152) {
            $errors[] = "$file_name exceeds maximum file size of 2MB";
          }

          if (!empty($file_name)) {
            // Upload file to desired location
            $upload_dir = '../uploads/drives';
            $file_path =  time() . '_' . $file_name;
            move_uploaded_file($file_tmp, $upload_dir . '/' . $file_path);
            // Insert file details into database
            $query = "INSERT INTO course_files (course_id, file_name, file_path) VALUES ('$course_in_id', '$file_name', '$file_path')";
            mysqli_query($conn, $query);
          }
        }
      }
      $success = "Course Created successfully";
      unset($_POST);
    } else {
      echo "Something went error try again later";
    }
  }
}

if (isset($_GET['delete'])) {
  $course_id = $_GET['delete'];

  $sql = "SELECT image_url_1, image_url_2, image_url_3, image_url_4 FROM courses WHERE course_id = $course_id";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $image1_path = "../uploads/courses/" . $row['image_url_1'];
    $image2_path = "../uploads/courses/" . $row['image_url_2'];
    $image3_path = "../uploads/courses/" . $row['image_url_3'];
    $image4_path = "../uploads/courses/" . $row['image_url_4'];

    // Delete the images from the server
    if (file_exists($image1_path) && !empty($row['image_url_1'])) {
      unlink($image1_path);
    }
    if (file_exists($image2_path) && !empty($row['image_url_2'])) {
      unlink($image2_path);
    }
    if (file_exists($image3_path) && !empty($row['image_url_3'])) {
      unlink($image3_path);
    }
    if (file_exists($image4_path) && !empty($row['image_url_4'])) {

      unlink($image4_path);
    }
    // select the files with matching course_id
    $sql = "SELECT * FROM course_files WHERE course_id = $course_id";
    $result = mysqli_query($conn, $sql);

    // loop through the results and delete the corresponding files from the server
    while ($row = mysqli_fetch_assoc($result)) {
      $file_path = $row['file_path'];
      if (file_exists('../uplaods/courses/' . $file_path) && !empty($file_path)) {
        unlink('../uplaods/courses/' . $file_path);
      }
    }
    // Delete the product from the database
    $sql = "DELETE course_files, enrollments
    FROM course_files
    LEFT JOIN courses ON course_files.course_id = courses.course_id
    LEFT JOIN enrollments ON enrollments.course_id = courses.course_id
    WHERE courses.course_id = '$course_id'";

    if ($conn->query($sql)) {
      $sql = "DELETE FROM courses WHERE course_id = '$course_id'";
      mysqli_query($conn, $sql);
      $success = "Course deleted successfully";
      header("Location: ./courses.php");
      exit();
    } else {
      echo "Error deleting product: " . $conn->error;
    }
  } else {
    echo "Product not found";
  }
}

if (isset($_GET['edit'])) {
  $edit = true;
  $course_id = $_GET['edit'];

  if ($_SESSION['type'] == 'admin') {
    $sql = "SELECT * FROM courses WHERE course_id = $course_id";
  } else if ($_SESSION['type'] == 'teacher') {
    $teacher_id = $_SESSION['user_id'];
    $sql = "SELECT * FROM courses WHERE course_id = $course_id AND teacher_id = '$teacher_id'";
  }
  $result = mysqli_query($conn, $sql);

  if (mysqli_num_rows($result) == 1) {
    $course = mysqli_fetch_assoc($result);
    $sql = "SELECT * FROM course_files WHERE course_id = $course_id";
    $files = mysqli_query($conn, $sql);
  } else {
    header('Location: courses.php');
    exit;
  }
}

if (isset($_POST['update'])) {

  $errors = array();

  // Define error messages array

  // Sanitize input data
  $course_name = htmlspecialchars($_POST['course_name']);
  $price = htmlspecialchars($_POST['course_price']);
  $description = htmlspecialchars($_POST['course_description']);
  $course_id = $_POST['course_id'];
  $teacher = $_POST['teacher'];
  $course_date = $_POST['course_date'];
  $room_location = $_POST['room_location'];
  $room_session = $_POST['room_session'];

  $course_code = substr(md5(uniqid(mt_rand(), true)), 0, 5);


  $success = null;
  // Validate input data
  if (empty($course_name)) {
    $errors["course_name"] = "Course Name is required";
  }
  if (empty($price)) {
    $errors["course_price"] = "Course Price is required";
  }
  if (empty($description)) {
    $errors["course_description"] = "Course Description is required";
  }
  if (empty($course_date)) {
    $errors["course_date"] = "Course Date is required";
  }

  // Prepare SQL query to check for existing room location and session
  $query = "SELECT * FROM courses WHERE course_id != '$course_id' AND room_location = '$room_location' AND room_session = '$room_session' AND course_date = '$course_date'";

  // Execute query and get results
  $result = mysqli_query($conn, $query);

  // Check if any rows were returned
  if (mysqli_num_rows($result) > 0) {
    // Room location and session already exist in table
    $errors['room_session'] = "Room location and session already exist.";
  }
  // If no errors, insert user data into database
  if (empty($errors)) {

    $upload_dir = "../uploads/courses/";
    $sql = "SELECT * FROM courses WHERE course_id = '$course_id'";

    // Execute the query
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    if (!empty($_FILES['image_url_1']['name'])) {
      $image_url_1 = $_FILES['image_url_1']['name'];
      $image_url_1_name = uniqid() . '.' . pathinfo($image_url_1, PATHINFO_EXTENSION);
      move_uploaded_file($_FILES['image_url_1']['tmp_name'], $upload_dir . $image_url_1_name);
    } else {
      $image_url_1_name = $row['image_url_1'] ?? null;
    }

    if (!empty($_FILES['image_url_2']['name'])) {
      $image_url_2 = $_FILES['image_url_2']['name'];
      $image_url_2_name = uniqid() . '.' . pathinfo($image_url_2, PATHINFO_EXTENSION);
      move_uploaded_file($_FILES['image_url_2']['tmp_name'], $upload_dir . $image_url_2_name);
    } else {
      $image_url_2_name = $row['image_url_2'] ?? null;
    }
    if (!empty($_FILES['image_url_3']['name'])) {
      $image_url_3 = $_FILES['image_url_3']['name'];
      $image_url_3_name = uniqid() . '.' . pathinfo($image_url_3, PATHINFO_EXTENSION);
      move_uploaded_file($_FILES['image_url_3']['tmp_name'], $upload_dir . $image_url_3_name);
    } else {
      $image_url_3_name = $row['image_url_3'] ?? null;
    }
    if (!empty($_FILES['image_url_4']['name'])) {
      $image_url_4 = $_FILES['image_url_4']['name'];
      $image_url_4_name = uniqid() . '.' . pathinfo($image_url_4, PATHINFO_EXTENSION);
      move_uploaded_file($_FILES['image_url_4']['tmp_name'], $upload_dir . $image_url_4_name);
    } else {
      $image_url_4_name = $row['image_url_4'] ?? null;
    }


    if (isset($_FILES['files']) && !empty($_FILES['files'])) {
      $errors = array();
      $success = array();

      // Loop through uploaded files
      foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {
        $file_name = $_FILES['files']['name'][$key];
        $file_size = $_FILES['files']['size'][$key];
        $file_tmp = $_FILES['files']['tmp_name'][$key];
        $file_type = $_FILES['files']['type'][$key];

        // Check for errors
        if ($file_size > 2097152) {
          $errors[] = "$file_name exceeds maximum file size of 2MB";
        }
        if (!empty($file_name)) {
          // Upload file to desired location
          $upload_dir = '../uploads/drives';
          $file_path =  time() . '_' . $file_name;
          move_uploaded_file($file_tmp, $upload_dir . '/' . $file_path);

          // Insert file details into database
          $query = "INSERT INTO course_files (course_id, file_name, file_path) VALUES ('$course_id', '$file_name', '$file_path')";
          mysqli_query($conn, $query);
        }
      }
    }
    // Prepare SQL statement
    $sql = "UPDATE courses SET course_name = '$course_name',course_price = '$price',course_date = '$course_date' , room_location = '$room_location' ,room_session = '$room_session' ,   course_description = '$description',teacher_id = '$teacher' ,image_url_1 = '$image_url_1_name', image_url_2 = '$image_url_2_name', image_url_3 = '$image_url_3_name' , image_url_4 = '$image_url_4_name' WHERE course_id = '$course_id'";

    // Execute statement and check for success
    if ($conn->query($sql)) {
      $success = "Course Updated successfully";
      unset($_POST);
      header('Location: ' . $_SERVER['HTTP_REFERER']);
    } else {
      echo "Something went error try again later";
    }
  }
}
?>


<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
    <input type="hidden" name="course_id" value="" id="course_id">

    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Send Message</h5>
          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="message-text" class="col-form-label">Message:</label>
            <textarea class="form-control" id="message-text" name="message"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <a class="btn btn-secondary" data-bs-dismiss="modal">Close</a>
          <button type="submit" name="sendmessage" class="btn btn-primary">Send message</button>
        </div>
      </div>
    </div>
  </form>
</div>

<div class="container my-5">
  <div class="row">
    <div class="col-md-12">
      <h2><?= (isset($edit)) ? "Edit Course" : "Add Course" ?> <?= (isset($edit)) ? "<a href='./courses.php'><small style='font-size:14px;'>Add Course</small></a>" : "" ?></h2>
      <?php if (isset($success)) : ?>
        <div class="alert alert-success">
          <?= $success ?>
        </div>
      <?php endif; ?>
      <form method="post" action="<?php $_SERVER['PHP_SELF'] ?>" enctype="multipart/form-data">
        <input type="hidden" name="course_id" value="<?= $course['course_id'] ?>" />
        <div class="row">
          <div class="col-md-6 mb-2">
            <div class="form-group">
              <label for="course_name">Course Name:</label>
              <input type="text" class="form-control" id="course_name" name="course_name" placeholder="Course Name" value="<?= $course['course_name'] ?? "" ?>" required>
              <?php if (isset($errors['course_name'])) : ?>
                <small class="text-error"><?= $errors['course_name'] ?></small>
              <?php endif; ?>
            </div>
          </div>
          <div class="col-md-6 mb-2">
            <div class="form-group">
              <label for="price">Course Price :</label>
              <input type="number" class="form-control" id="course_price" name="course_price" placeholder="Course Price" value="<?= $course['course_price'] ?? "" ?>" required>
              <?php if (isset($errors['course_price'])) { ?>
                <small class="text-error"><?= $errors['course_price'] ?></small>
              <?php } ?>
            </div>
          </div>
          <?php if ($_SESSION['type']  == 'admin') : ?>
            <div class="col-md-3 mb-2">
              <?php
              $sql = "SELECT * FROM users WHERE type = 'teacher' OR type = 'admin'";
              $result = $conn->query($sql);
              // Create a select element and insert the teachers as options
              echo '<div class="form-group">';
              echo '<label for="teacher">Select a teacher:</label>';
              echo '<select class="form-control" name="teacher" id="teacher">';
              while ($row = $result->fetch_assoc()) {
                echo '<option value="' . $row["id"] . '">' . $row["first_name"]  . " " .  $row['last_name'] . '</option>';
              }
              echo '</select>';
              echo '</div>';
              ?>
            </div>
          <?php elseif ($_SESSION['type']  == 'teacher') : ?>
            <input type="hidden" name="teacher" value="<?= $_SESSION['user_id'] ?>" />
          <?php endif; ?>
          <div class="col-md-3 mb-2">
            <div class="form-group">
              <label for="course_date">Course Date :</label>
              <input type="date" class="form-control" id="course_date" name="course_date" placeholder="Course Date" value="<?= $course['course_date'] ?? "" ?>" required>
              <?php if (isset($errors['course_date'])) { ?>
                <small class="text-error"><?= $errors['course_date'] ?></small>
              <?php } ?>
            </div>
          </div>
          <div class="col-md-3 mb-2">
            <div class="form-group">
              <label for="room_location">Room Location :</label>
              <select class="form-control" name="room_location">
                <?php
                $sql = "SELECT DISTINCT room , booking_id FROM booking";
                $result = mysqli_query($conn, $sql);
                if (mysqli_num_rows($result) > 0) {
                  while ($row = mysqli_fetch_assoc($result)) {
                ?>
                    <option value="<?= $row['booking_id'] ?>" <?php if (isset($course)) {
                                                                if ($course['room_location'] == $row['room']) {
                                                                  echo "selected";
                                                                }
                                                              } ?>> <?= $row['room'] ?></option>
                <?php
                  }
                }
                ?>
              </select>
              <?php if (isset($errors['room_location'])) { ?>
                <small class="text-error"><?= $errors['room_location'] ?></small>
              <?php } ?>
            </div>
          </div>
          <div class="col-md-3 mb-2">
            <div class="form-group">
              <label for="room_session">Room Sessions :</label>
              <select class="form-control" id="room_sessoin" name="room_session">
                <option value="session-1" <?php if (isset($course) && $course['room_session'] == 'session-1') echo "selected"; ?>>From 09:00 to 12:00</option>
                <option value="session-2" <?php if (isset($course) && $course['room_session'] == 'session-2') echo "selected"; ?>>From 13:00 to 16:00</option>
                <option value="session-3" <?php if (isset($course) && $course['room_session'] == 'session-3') echo "selected"; ?>>From 18:00 to 21:00</option>
              </select>
              <?php if (isset($errors['room_session'])) { ?>
                <small class="text-error"><?= $errors['room_session'] ?></small>
              <?php } ?>
            </div>
          </div>
          <div class="col-md-12 mb-2">
            <div class="form-group">
              <label for="description">Description :</label>
              <textarea name="course_description" cols="30" rows="10" class="form-control" placeholder="Description"><?= $course['course_description'] ?? "" ?></textarea>
              <?php if (isset($errors['course_description'])) : ?>
                <small class="text-error"><?= $errors['course_description'] ?></small>
              <?php endif; ?>
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <div class="mb-2">
              <label for="image-1" class="form-label">Image 1</label>
              <input class="form-control" id="image-1" type="file" name="image_url_1" />
            </div>
            <img src="<?= empty($course['image_url_1']) ? "../assets/images/noimage.png" : '../uploads/courses/' . $course['image_url_1'] ?>" style="width: 100%;height:200px;object-fit:cover;" alt="Image 1">
          </div>
          <div class="col-md-3 mb-3">
            <div class="mb-2">
              <label for="image-2" class="form-label">Image 2</label>
              <input class="form-control" id="image-2" type="file" name="image_url_2" />
            </div>
            <img src="<?= empty($course['image_url_2']) ? "../assets/images/noimage.png" : '../uploads/courses/' . $course['image_url_2'] ?>" style="width: 100%;height:200px;object-fit:cover;" alt="Image 2">
          </div>
          <div class="col-md-3 mb-3">
            <div class="mb-2">
              <label for="image-3" class="form-label">Image 3</label>
              <input class="form-control" id="image-3" type="file" name="image_url_3" />
            </div>
            <img src="<?= empty($course['image_url_3']) ? "../assets/images/noimage.png" : '../uploads/courses/' . $course['image_url_3'] ?>" style="width: 100%;height:200px;object-fit:cover;" alt="Banner">
          </div>
          <div class="col-md-3 mb-3">
            <div class="mb-2">
              <label for="image-4" class="form-label">Image 4</label>
              <input class="form-control" id="image-4" type="file" name="image_url_4" />
            </div>
            <img src="<?= empty($course['image_url_4']) ? "../assets/images/noimage.png" : '../uploads/courses/' . $course['image_url_4'] ?>" style="width: 100%;height:200px;object-fit:cover;" alt="Banner">
          </div>
        </div>
        <div class="container mb-4">
          <h3>Files</h3>
          <ul class="list-group">
            <?php if (isset($edit)) : ?>
              <li class="list-group-item">
                <ul class="list-group">
                  <?php while ($row = mysqli_fetch_assoc($files)) : ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      <div class="folder"><?= $row['file_name'] ?></div>
                      <div>
                        <a onclick="confirmDeleteFile(<?= $row['id'] ?>)" class="btn btn-danger">Delete</a>
                      </div>
                    </li>
                  <?php endwhile; ?>
                </ul>
              </li>
            <?php endif; ?>
            <div class="list-group-item">
              <li class="list-group-item">
                <input type="file" name="files[]" multiple />
              </li>
            </div>
          </ul>
        </div>

        <?php if (isset($edit)) : ?>
          <button type="submit" name="update" class="btn btn-primary mb-4">Edit Course</button>
        <?php else : ?>
          <button type="submit" name="create" class="btn btn-primary mb-4">Add Course</button>
        <?php endif; ?>
      </form>

    </div>
  </div>
  <table class="table bg-white">
    <thead>
      <tr>
        <th>#</th>
        <th>Course Name</th>
        <th>Thumbnail</th>
        <th>Price</th>
        <th>Description</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if ($_SESSION['type'] == 'admin') {
        $sql = "SELECT * FROM courses";
      } else if ($_SESSION['type'] == 'teacher') {
        $teacher_id = $_SESSION['user_id'];
        $sql = "SELECT * FROM courses WHERE teacher_id = '$teacher_id'";
      }
      $result = mysqli_query($conn, $sql);

      // Loop through each user and display data in a row
      $count = 0;
      if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
          $count++;
      ?>
          <tr>
            <td><?php echo $count; ?></td>
            <td><?php echo $row['course_name']; ?></td>
            <td><img src='<?= empty($row['image_url_1']) ? "../assets/images/noimage.png" : '../uploads/courses/' . $row['image_url_1'] ?>' class='rounded-circle border border-dark' style='width:50px;height:50px;object-fit:cover;' /></td>
            <td>$<?php echo $row['course_price']; ?></td>
            <td><?php echo substr($row['course_description'], 0, 20) . '...'; ?></td>
            <td>
              <a href='./courses.php?edit=<?php echo $row['course_id']; ?>' class='btn btn-sm btn-primary btn-sm edit-btn mx-1' data-toggle='modal' data-target='#editModal'>Edit</a>
              <button class='btn btn-danger btn-sm btn-sm delete-btn mx-1' onclick='confirmDelete(<?php echo $row['course_id']; ?>)'>Delete</button>
              <a onclick="sendMessage()" data-courseid="<?php echo $row['course_id']; ?>" class="btn send btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal">
                Message
              </a>
            </td>
          </tr>

      <?php }
      } else {
        echo "<tr><td colspan='9' class='text-center'>No Courses found.</td></tr>";
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
      window.location.href = './courses.php?delete=' + id;
    }
  }

  function confirmDeleteFile(id) {
    if (confirm("Are you sure you want to delete this File?")) {
      window.location.href = './courses.php?deletefile=' + id;
    }
  }

  function sendMessage() {
    // Get the course link element
    const courseLink = document.querySelector('.send');

    // Get the course id from the data-courseid attribute
    const courseId = courseLink.dataset.courseid;

    // Set the value of the hidden input field to the course id
    document.querySelector('#course_id').value = courseId;


  }
</script>

<?php include('./components/footer.php'); ?>