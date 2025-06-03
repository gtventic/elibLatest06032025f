<?php
// admin/add_department.php
require_once '../db.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'super_admin') {
    header("Location: login.php");
    exit;
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $department_name = trim($_POST['department_name']);
    
    // Check if the department already exists
    $stmt = $conn->prepare("SELECT COUNT(*) FROM departments WHERE department_name = :department_name");
    $stmt->bindParam(':department_name', $department_name, PDO::PARAM_STR);
    $stmt->execute();
    $exists = $stmt->fetchColumn();
    
    if ($exists > 0) {
        $message = "Department already exists.";
    } else {
        // Insert the department if it doesn't already exist
        $stmt = $conn->prepare("INSERT INTO departments (department_name) VALUES (:department_name)");
        $stmt->bindParam(':department_name', $department_name);
        if ($stmt->execute()) {
            $message = "Department added successfully.";
        } else {
            $message = "Error adding department.";
        }
    }
}

// Retrieve all departments ordered by department name (descending)
$stmt = $conn->query("SELECT * FROM departments ORDER BY department_name DESC");
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Departments</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script>
    // JavaScript code to automatically logout the user after 1 minute of inactivity.
    var idleTime = 0;
    // Increase idle time every 1000 milliseconds (1 second)
    var idleInterval = setInterval(function() {
        idleTime++;
        if (idleTime > 60) { // 60 seconds of idle time
            window.location.href = "logout.php";
        }
    }, 1000);
    
    // Resets idle time on following events
    document.addEventListener("mousemove", resetIdleTime, false);
    document.addEventListener("mousedown", resetIdleTime, false);
    document.addEventListener("keypress", resetIdleTime, false);
    document.addEventListener("touchmove", resetIdleTime, false);
    
    function resetIdleTime() {
        idleTime = 0;
    }
  </script>
</head>
<body>
  <?php include 'navbar.php'; ?>
  <div class="container mt-4">
    <h2>Add Department</h2>
    <?php if ($message) { echo '<div class="alert alert-info">' . $message . '</div>'; } ?>
    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Department Name</label>
        <input type="text" name="department_name" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary">Add Department</button>
    </form>
    <!-- Department List -->
    <hr>
    <h2>Department List (Ordered by Name)</h2>
    <table class="table table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Department Name</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($departments)) {
          foreach ($departments as $dept) { ?>
              <tr>
                <td><?php echo htmlspecialchars($dept['id']); ?></td>
                <td><?php echo htmlspecialchars($dept['department_name']); ?></td>
              </tr>
          <?php }
        } else { ?>
          <tr>
            <td colspan="2" class="text-center">No departments found.</td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</body>
</html>
