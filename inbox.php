<?php
$nav = "";
$title = "Inbox";
include('components/header.php');
include './config/database.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}
// Delete Message
if (isset($_GET['delete'])) {
  $ids =  $_GET['delete'];

  $sql = "DELETE FROM messages WHERE message_id IN ($ids)";

  // Execute the query
  if (mysqli_query($conn, $sql)) {
    $success =  "Messages deleted successfully.";
  } else {
    echo "Error deleting messages: " . mysqli_error($conn);
  }
}


// Query to select messages for the user
$user_id = $_SESSION['user_id'];
$sql = "SELECT messages.message_id, messages.message_text, messages.sent_at , messages.sender_id, users.id, users.username , users.type
        FROM messages
        JOIN users ON messages.sender_id = users.id
        WHERE messages.user_id = $user_id
        ORDER BY messages.sent_at DESC";
$result = mysqli_query($conn, $sql);

?>

<div class="container py-5">
  <?php if (isset($success)) : ?>
    <div class="alert alert-success">
      <?= $success ?>
    </div>
  <?php endif; ?>
  <h1>Inbox</h1>
  <hr />
  <table class="table" id="inbox-table">
    <thead>
      <tr>
        <th scope="col">New</th>
        <th scope="col">Sender</th>
        <th scope="col">Date & Time</th>
        <th scope="col">Content</th>
        <th scope="col"><input type="checkbox" id="master-checkbox" /></th>
      </tr>
    </thead>
    <tbody>
      <?php if (mysqli_num_rows($result) > 0) :
        $count = 0;
        while ($row = mysqli_fetch_assoc($result)) :
          $count++;
      ?>
          <tr>
            <input type="hidden" class="inboxId" value="<?= $row['message_id'] ?>" />
            <th scope="row"><?= $count ?></th>
            <td><?= $row['type'] == 'admin' ? "Admin" : $row['username'] ?></td>
            <td><?= $row['sent_at'] ?></td>
            <td><?= $row['message_text'] ?></td>
            <td><input type="checkbox" name="deleted[]" class="checkbox" /></td>
          </tr>
        <?php
        endwhile;
      else :
        ?>
        <tr>
          <td colspan="5" class="text-center">There are not messages to show</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
  <button class="btn btn-danger btn-lg" onclick="getCheckedRows()">Delete Selected</button>
</div>

<script>
  // Get the master checkbox
  const masterCheckbox = document.querySelector('#master-checkbox');

  // Get all checkboxes in the table body
  const checkboxes = document.querySelectorAll('tbody .checkbox');

  // Add a click event listener to the master checkbox
  masterCheckbox.addEventListener('click', () => {
    // Loop through all checkboxes and set their checked property to match the master checkbox
    checkboxes.forEach((checkbox) => {
      checkbox.checked = masterCheckbox.checked;
    });
  });

  function getCheckedRows() {
    let checkedRows = [];
    let tableRows = document.querySelectorAll('#inbox-table tbody tr');
    tableRows.forEach(function(row) {
      let checkbox = row.querySelector('input[type="checkbox"]');
      if (checkbox.checked) {
        checkedRows.push(row.querySelector(".inboxId").value);
      }
    });
    window.location.href = "./inbox.php?delete=" + checkedRows.join(',')
  }
</script>
<?php include('components/footer.php'); ?>