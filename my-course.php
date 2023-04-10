<?php
$nav = "";
$title = "My Course";
include('components/header.php');
include './config/database.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}



if (isset($_POST['editsessions'])) {
  $course_id = $_POST['course_id'];
  $sessions = $_POST['sessions'];

  // update query
  $sql = "UPDATE enrollments SET sessions = $sessions WHERE course_id = '$course_id'";

  // execute query
  if (mysqli_query($conn, $sql)) {
    $success =  "Record updated successfully";
  } else {
    echo "Error updating record: " . mysqli_error($conn);
  }
}

if (isset($_POST['editschedhule'])) {
  $course_id = $_POST['course_id'];

  $upcoming = date('Y-m-d', strtotime($_POST['upcoming']));

  // update query
  $sql = "UPDATE enrollments SET upcoming_date = '$upcoming' WHERE course_id = '$course_id'";

  // execute query
  if (mysqli_query($conn, $sql)) {
    $success =  "Record updated successfully";
  } else {
    echo "Error updating record: " . mysqli_error($conn);
  }
}
$user_id = $_SESSION['user_id'];
// Prepare the SQL statement to fetch all data from courses table
$sql = "SELECT courses.*, users.first_name AS teacher_firstname , users.last_name AS teacher_lastname , enrollments.sessions AS sessions , enrollments.upcoming_date
        FROM courses 
        JOIN users ON courses.teacher_id = users.id
        JOIN enrollments ON (enrollments.course_id = courses.course_id AND enrollments.user_id = '$user_id')";

// $sql = "SELECT * FROM enrollments WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $sql);

// Execute the SQL statement and get the result set

?>
<!-- Buy Modal -->
<div class="modal fade" id="buyModal" tabindex="-1" aria-labelledby="buyModalLabel" aria-hidden="true">

  <div class="modal-dialog modal-dialog-centered">
    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
      <input type="hidden" name="course_id" class="courseId" />
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="buyModalLabel">Buy Session</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row mb-3">
            <label for="inputEmail3" class="col-sm-4 col-form-label">Course Code</label>
            <div class="col-sm-8">
              <span class="courseCode">PA106</span>
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-4 col-form-label">Course Detail</label>
            <div class="col-sm-8">
              <span class="courseDetail">Piano Calss A ABRSM Grade 3 Every Monday 5:30 pm By Ms Anita Chan</span>
            </div>
          </div>
          <div class="row mb-3">
            <label for="inputEmail3" class="col-sm-4 col-form-label">Session Pack</label>
            <div class="col-sm-8 d-flex gap-2 align-items-center">
              <a class="btn btn-sm btn-primary rounded-full plus-btn">+</a>
              <input type="number" id="quantity" name="sessions" value="1" style="width:60px;">
              <a class="btn btn-sm btn-primary rounded-full minus-btn">-</a>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="editsessions" class="btn btn-primary">Confirm</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>

</div>

<!-- Reschedule Modal -->
<div class="modal fade" id="rescheduleModal" tabindex="-1" aria-labelledby="rescheduleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
      <input type="hidden" name="course_id" class="courseId" />
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="buyModalLabel">Reschedule</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row mb-3">
            <label for="inputEmail3" class="col-sm-4 col-form-label">Course Code</label>
            <div class="col-sm-8">
              <span class="courseCode">PA106</span>
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-4 col-form-label">Course Detail</label>
            <div class="col-sm-8">
              <span class="courseDetail">Piano Calss A ABRSM Grade 3 Every Monday 5:30 pm By Ms Anita Chan</span>
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-4 col-form-label">Reschudle</label>
            <div class="col-sm-8 d-flex gap-2 align-items-center">
              <input type="date" name="upcoming" />
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="editschedhule" class="btn btn-primary">Confirm</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="container py-5">
  <?php if (isset($success)) : ?>
    <div class="alert alert-success">
      <?= $success ?>
    </div>
  <?php endif; ?>
  <h1>My Course</h1>
  <hr />
  <table class="table">
    <thead>
      <tr>
        <th scope="col">Course code</th>
        <th scope="col">Teacher</th>
        <th scope="col">Course time</th>
        <th scope="col">Session left</th>
        <th scope="col"></th>
      </tr>
    </thead>
    <tbody>
      <?php
      $count = 0;
      while ($row = mysqli_fetch_assoc($result)) :
        $count++; ?>
        <tr>
          <th scope="row"><?= $row['course_code'] ?></th>
          <td><?= $row['teacher_firstname'] ?> <?= $row['teacher_lastname'] ?></td>
          <td><?= $row['course_date'] ?></td>
          <td><?= $row['sessions'] ?></td>
          <td>
            <div class="d-flex flex-column gap-2">
              <button onclick="changeData('<?= $row['course_code'] ?>' , '<?= $row['course_description'] ?>' , '<?= $row['course_id'] ?>' , '<?= $row['upcoming_date'] ?>')" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#buyModal">Buy sessions</button>
              <button onclick="changeData('<?= $row['course_code'] ?>' , '<?= $row['course_description'] ?>' , '<?= $row['course_id'] ?>' , '<?= $row['upcoming_date'] ?>')" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#rescheduleModal">Reschedule</button>
            </div>
          </td>
        </tr>

      <?php endwhile;
      if ($count == 0) {
        echo "<tr><td colspan='6' class='text-center'>No Purchases found.</td></tr>";
      } ?>
    </tbody>
  </table>
</div>

<script>
  function changeData(courseCode, courseDetail, course__Id, upcomingInput) {
    const courseCodeSpan = document.querySelectorAll(".courseCode");
    const courseDetailSpan = document.querySelectorAll(".courseDetail");
    const courseId = document.querySelectorAll(".courseId")
    const upcoming = document.querySelectorAll('.upcoming')
    console.log(upcoming);
    for (i = 0; i < courseCodeSpan.length; i++)
      courseCodeSpan[i].innerHTML = courseCode;

    for (i = 0; i < courseId.length; i++)
      courseId[i].value = course__Id;

    for (i = 0; i < upcoming.length; i++)
      upcoming[i].innerHTML = upcomingInput;

    for (i = 0; i < courseCodeSpan.length; i++)
      courseDetailSpan[i].innerHTML = courseDetail;

  }
</script>

<?php include('components/footer.php'); ?>