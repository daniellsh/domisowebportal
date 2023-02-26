<?php
$nav = "course";
$title = "Course";
include('components/header.php');
include './config/database.php';

// Prepare the SQL statement to select the columns you want
$sql = "SELECT course_id , course_name, room_session , DATE_FORMAT(course_date, '%W') as course_date , course_price, image_url_1, course_description FROM courses ORDER BY course_id DESC";

// Execute the SQL statement
$result = mysqli_query($conn, $sql);

?>

<div class="container course py-5">
  <div class="row">
    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
      <div class="col-sm-12 col-md-4 mb-2">
        <a href="./coursedetails.php?id=<?= $row['course_id'] ?>">
          <div class="card">
            <div class="card-body">
              <h5><?= $row['course_name'] ?></h5>
              <img src="<?= empty($row['image_url_1']) ? "./assets/images/noimage.png" : './uploads/courses/' . $row['image_url_1'] ?>" class="card-img-top mt-3" alt="<?php echo $row['course_name']; ?>" />
              <div class="mt-3">
                <span class="badge bg-dark">Every <?= $row['course_date'] ?> <?php
                                                                              switch ($row['room_session']) {
                                                                                case "session-1":
                                                                                  echo "From 09:00 to 12:00";
                                                                                  break;
                                                                                case "session-2":
                                                                                  echo "From 13:00 to 16:00";
                                                                                  break;
                                                                                case "session-3":
                                                                                  echo "From 18:00 to 21:00";
                                                                                  break;
                                                                              }
                                                                              ?></span>
                <h5 class="mb-0">$<?= $row['course_price'] ?></h5>
                <p class="text-dark mb-0 text-desc"><?= $row['course_description'] ?>...</p>
              </div>
            </div>
          </div>
        </a>
      </div>
    <?php endwhile; ?>
  </div>
</div>

<?php include('components/footer.php'); ?>