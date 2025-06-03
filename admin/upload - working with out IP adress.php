<?php
// admin/upload.php
require_once '../db.php';

if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'super_admin'])) {
    header("Location: login.php");
    exit;
}

$message = "";

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // ---------- Handle Delete Action ----------
    if (isset($_POST['delete_pdf'])) {
        $pdf_id = intval($_POST['pdf_id']);
        
        // Retrieve the PDF record from the database.
        $stmt = $conn->prepare("SELECT * FROM pdf_files WHERE id = :pdf_id");
        $stmt->bindParam(':pdf_id', $pdf_id, PDO::PARAM_INT);
        $stmt->execute();
        $pdf_record = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($pdf_record) {
            // Delete the file from disk if it exists.
            if (file_exists($pdf_record['file_path'])) {
                unlink($pdf_record['file_path']);
            }
            // Delete the record from the pdf_files table.
            $stmt = $conn->prepare("DELETE FROM pdf_files WHERE id = :pdf_id");
            $stmt->bindParam(':pdf_id', $pdf_id, PDO::PARAM_INT);
            if ($stmt->execute()) {
                // Log the deletion action into pdf_log with action 'delete'.
                $log_stmt = $conn->prepare("INSERT INTO pdf_log (user_id, pdf_file_id, view_time, action) VALUES (:user_id, :pdf_file_id, NOW(), 'delete')");
                $log_stmt->bindParam(':user_id', $_SESSION['user']['id']);
                $log_stmt->bindParam(':pdf_file_id', $pdf_id, PDO::PARAM_INT);
                $log_stmt->execute();
                $message = "PDF file deleted successfully.";
            } else {
                $message = "Error deleting PDF file record.";
            }
        } else {
            $message = "PDF file record not found.";
        }
    }
    // ---------- Handle Upload Action ----------
    elseif (isset($_FILES['pdf_file'])) {
        // Retrieve additional input values
        $department_id = isset($_POST['department_id']) ? intval($_POST['department_id']) : null;
        $category      = isset($_POST['category']) ? $_POST['category'] : '';
        
        // Check if "Select All" was checked for allowed users.
        $select_all    = isset($_POST['select_all']) ? true : false;
        
        // Retrieve specifically selected users, if any.
        $target_users  = isset($_POST['target_users']) ? $_POST['target_users'] : array();
        
        // If "Select All" is checked, assign file to all users (allowed_users becomes NULL).
        // Otherwise, join the selected user IDs into a comma-separated string (or leave null if none selected).
        if ($select_all) {
            $allowed_users = null;
        } else {
            $allowed_users = !empty($target_users) ? implode(",", $target_users) : null;
        }
    
        if ($_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
            $fileName = $_FILES['pdf_file']['name'];
            $tmpName  = $_FILES['pdf_file']['tmp_name'];
            
            // Determine destination folder based on category
            switch ($category) {
                case 'POLICY':
                    $folder = '../uploads/POLICY/';
                    break;
                case 'IOM':
                    $folder = '../uploads/IOM/';
                    break;
                case 'Manual':
                    $folder = '../uploads/Manual/';
                    break;
                default:
                    $folder = '../uploads/';
                    break;
            }
            // Create the folder if it does not exist
            if (!is_dir($folder)) {
                mkdir($folder, 0777, true);
            }
            $destination = $folder . basename($fileName);
    
            // Validate that the uploaded file is a PDF
            if (mime_content_type($tmpName) == 'application/pdf') {
                if (move_uploaded_file($tmpName, $destination)) {
                    // Insert values into the pdf_files table (including category)
                    $stmt = $conn->prepare("INSERT INTO pdf_files (file_name, file_path, uploaded_by, department_id, target_users, category) 
                                            VALUES (:file_name, :file_path, :uploaded_by, :department_id, :target_users, :category)");
                    $stmt->bindParam(':file_name', $fileName);
                    $stmt->bindParam(':file_path', $destination);
                    $stmt->bindParam(':uploaded_by', $_SESSION['user']['id']);
                    $stmt->bindParam(':department_id', $department_id);
                    $stmt->bindParam(':target_users', $allowed_users);
                    $stmt->bindParam(':category', $category);
                    $stmt->execute();
                    $message = "PDF file uploaded successfully.";
                } else {
                    $message = "Failed to move uploaded file.";
                }
            } else {
                $message = "Only PDF files are allowed.";
            }
        } else {
            $message = "Error uploading file.";
        }
    }
}

// Retrieve departments for the dropdown
$departments = $conn->query("SELECT * FROM departments")->fetchAll(PDO::FETCH_ASSOC);

// Retrieve system users (for example, only those with role 'user')
$users = $conn->query("SELECT * FROM users WHERE role = 'user'")->fetchAll(PDO::FETCH_ASSOC);

// --- Retrieve PDF File List ---
// Retrieve all uploaded PDF files, along with associated department names (if any)
$sql = "SELECT pf.*, d.department_name 
        FROM pdf_files pf 
        LEFT JOIN departments d ON pf.department_id = d.id 
        ORDER BY pf.id DESC";
$stmt = $conn->query($sql);
$pdf_files = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Upload PDF - By Category</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <?php include 'navbar.php'; ?>
  <div class="container mt-4">
    <h2>Upload PDF</h2>
    <?php if ($message) { echo '<div class="alert alert-info">' . $message . '</div>'; } ?>
    <form method="POST" enctype="multipart/form-data">
      <!-- PDF file input -->
      <div class="mb-3">
        <label for="pdf_file" class="form-label">Select PDF File</label>
        <input class="form-control" name="pdf_file" type="file" id="pdf_file" accept="application/pdf" required>
      </div>
      
      <!-- Category selection -->
      <div class="mb-3">
        <label for="category" class="form-label">Select Category</label>
        <select class="form-control" name="category" id="category" required>
          <option value="">-- Select Category --</option>
          <option value="POLICY">POLICY</option>
          <option value="IOM">IOM</option>
          <option value="Manual">Manual</option>
        </select>
      </div>
      
      <!-- Department selection -->
      <div class="mb-3">
        <label for="department_id" class="form-label">Select Department</label>
        <select class="form-control" name="department_id" id="department_id" required>
          <option value="">-- Select Department --</option>
          <?php foreach ($departments as $dept) { ?>
            <option value="<?php echo $dept['id']; ?>"><?php echo htmlspecialchars($dept['department_name']); ?></option>
          <?php } ?>
        </select>
      </div>
      
      <!-- Allowed Users multi-select with "Select All" checkbox -->
      <div class="mb-3">
        <label for="target_users" class="form-label">Assign To Specific Users</label>
        <div class="form-check mb-2">
          <input class="form-check-input" type="checkbox" id="select_all" name="select_all" value="1">
          <label class="form-check-label" for="select_all">Select All</label>
        </div>
        <select class="form-control" name="target_users[]" id="target_users" multiple>
          <?php foreach ($users as $user_item) { ?>
            <option value="<?php echo $user_item['id']; ?>"><?php echo htmlspecialchars($user_item['username']); ?></option>
          <?php } ?>
        </select>
        <small class="form-text text-muted">Hold down the Ctrl (Windows) or Command (Mac) key to select multiple users.</small>
      </div>
      
      <button type="submit" class="btn btn-primary">Upload</button>
    </form>
    
    <!-- PDF File List Section -->
    <hr>
    <h2>PDF File List</h2>
    <table class="table table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>File Name</th>
          <th>Department</th>
          <th>Category</th>
          <th>Allowed Users</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($pdf_files)) {
          foreach ($pdf_files as $pdf) {
              // Process allowed users string into names.
              $allowedNames = "";
              if (!empty($pdf['target_users'])) {
                  // Convert comma-separated IDs into an array.
                  $user_ids = explode(',', $pdf['target_users']);
                  // Prepare placeholders for query.
                  $placeholders = implode(',', array_fill(0, count($user_ids), '?'));
                  $stmtUsers = $conn->prepare("SELECT username FROM users WHERE id IN ($placeholders)");
                  $stmtUsers->execute($user_ids);
                  $userNames = $stmtUsers->fetchAll(PDO::FETCH_COLUMN);
                  // Join names into a string.
                  $allowedNames = implode(', ', $userNames);
              } else {
                  $allowedNames = "All";
              }
              ?>
              <tr>
                <td><?php echo htmlspecialchars($pdf['id']); ?></td>
                <td><?php echo htmlspecialchars($pdf['file_name']); ?></td>
                <td><?php echo htmlspecialchars($pdf['department_name'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($pdf['category']); ?></td>
                <td><?php echo htmlspecialchars($allowedNames); ?></td>
                <td>
                  <!-- Delete Button Form -->
                  <form method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this PDF file?');">
                    <input type="hidden" name="pdf_id" value="<?php echo $pdf['id']; ?>">
                    <button type="submit" name="delete_pdf" class="btn btn-sm btn-danger">Delete</button>
                  </form>
                </td>
              </tr>
          <?php }
        } else { ?>
          <tr>
            <td colspan="6" class="text-center">No PDF files found.</td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</body>
</html>
