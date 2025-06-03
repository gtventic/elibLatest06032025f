<?php
// list_pdfs.php
require_once '../db.php';  // Adjust the path if your file structure differs

// Optional: You might want to check if the user is logged in here.
// For example, uncomment the following lines if needed:
// if (!isset($_SESSION['user'])) {
//     header("Location: login.php");
//     exit;
// }

// Retrieve all non-archived PDF files, ordered by upload date (most recent first)
$sql = "SELECT * FROM pdf_files WHERE archived = 0 ORDER BY uploaded_at DESC";
$stmt = $conn->query($sql);
$pdf_files = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Uploaded PDF List</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-4">
    <h2 class="mb-4">List of Uploaded PDFs</h2>
    <?php if (!empty($pdf_files)): ?>
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>File Name</th>
            <th>Uploaded By</th>
            <th>Uploaded At</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($pdf_files as $file): ?>
            <tr>
              <td><?php echo htmlspecialchars($file['id']); ?></td>
              <td><?php echo htmlspecialchars($file['file_name']); ?></td>
              <td><?php echo htmlspecialchars($file['uploaded_by']); ?></td>
              <td><?php echo htmlspecialchars($file['uploaded_at']); ?></td>
              <td>
                <a href="view_pdf.php?id=<?php echo $file['id']; ?>" class="btn btn-primary btn-sm">View</a>
                <!-- Optionally, add other actions like Download, Archive, etc. -->
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="alert alert-info">No PDF files have been uploaded yet.</div>
    <?php endif; ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
