<?php
$nav = "";
$title = "My Drive";
include('components/header.php');
include './config/database.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

if (isset($_GET['deletefile'])) {
  // Get the file ID from the URL
  $file_id = $_GET['deletefile'];

  if ($_SESSION['type'] == 'admin') {
    $sql = "SELECT file_path FROM course_files WHERE id = $file_id";
  } else {
    // Retrieve the file path from the database
    $sql = "SELECT cf.file_path , c.teacher_id FROM course_files cf
  INNER JOIN courses c ON c.course_id = cf.course_id AND c.teacher_id = $user_id
  WHERE id = $file_id";
  }
  $result = mysqli_query($conn, $sql);
  if (mysqli_num_rows($result)) {
    $row = mysqli_fetch_assoc($result);

    $file_path = $row['file_path'];

    // Delete the file from the folder
    unlink('./uploads/drives/' . $file_path);

    // Delete the file record from the database
    $sql = "DELETE FROM course_files WHERE id = $file_id";
    mysqli_query($conn, $sql);

    $success = "File deleted successfully!";
    header('Location: my-drive.php');
    exit();
  }
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT cf.file_name, cf.file_path, cf.id, c.course_name , c.teacher_id
FROM course_files cf
INNER JOIN courses c ON c.course_id = cf.course_id
INNER JOIN enrollments e ON e.course_id = c.course_id
WHERE e.user_id = '$user_id'";

$result = mysqli_query($conn, $sql);
$files = [];
while ($row = mysqli_fetch_assoc($result)) {
  $files[] = $row;
}
$files = chunkArrayByKeyValue($files, "course_name");

?>

<div class="container py-5">
  <h1>My Drive</h1>
  <hr />
  <?php if (empty($files)) : ?>
    <h3 class="text-center">There are not Courses files</h3>
  <?php endif;
  $accord = 0;
  ?>
  <div class="accordion" id="accordionFlushExample">
    <?php foreach ($files as $file) :
      $accord++;
    ?>
      <div class="accordion-item mb-4">
        <h2 class="accordion-header" id="flush-headingOne">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse<?= $accord ?>" aria-expanded="false" aria-controls="flush-collapseOne">
            <?= $file[0]['course_name'] ?>
          </button>
        </h2>
        <?php
        $count = 0;

        foreach ($file as $item) :
          $count++;
          $f_name = $item['file_name'];
          $exts = explode('.', $f_name);
        ?>
          <div id="flush-collapse<?= $accord ?>" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
            <div class="d-flex justify-content-between align-items-center">
              <div class="accordion-body"><span class="badge bg-warning me-2"><?= strtoupper(end($exts)) ?></span><?= $f_name ?></div>
              <div>
                <?php if ($item['teacher_id'] == $user_id || $_SESSION['type'] == 'admin') : ?>
                  <a href="./my-drive.php?deletefile=<?= $item['id'] ?>" class="btn btn-danger btn-sm me-3">Delete</a>
                <?php endif; ?>
                <a href="./uploads/drives/<?= $item['file_path'] ?>" download class="btn btn-success btn-sm me-3">Download</a>
              </div>
            </div>
          </div>
        <?php
        endforeach;
        if ($count == 0) echo "<div class='d-flex justify-content-center align-items-center'>There are not files in this course</div>";
        ?>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php include('components/footer.php'); ?>