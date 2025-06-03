<?php
// public/view_pdf.php
require_once '../db.php';

// Ensure the user is logged in and is a regular user.
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'user') {
    header("Location: login.php");
    exit;
}

// Validate the PDF id parameter.
if (!isset($_GET['id'])) {
    die("Invalid request.");
}
$pdf_id = intval($_GET['id']);

// Retrieve the PDF details.
$stmt = $conn->prepare("SELECT * FROM pdf_files WHERE id = :id AND archived = 0");
$stmt->bindParam(':id', $pdf_id, PDO::PARAM_INT);
$stmt->execute();
$pdf = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$pdf) {
    die("PDF file not found.");
}

// Determine folder based on the category.
$folder = "";
if (!empty($pdf['category'])) {
    if ($pdf['category'] === 'POLICY') {
        $folder = 'POLICY';
    } elseif ($pdf['category'] === 'IOM') {
        $folder = 'IOM';
    } elseif ($pdf['category'] === 'Manual') {
        $folder = 'Manual';
    }
}
$fileURL = "../uploads/" . ($folder ? $folder . "/" : "") . htmlspecialchars($pdf['file_name']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View PDF - <?php echo htmlspecialchars($pdf['file_name']); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f8f9fa;
      margin: 0;
      padding: 0;
    }
    /* Disable printing by hiding content on print */
    @media print {
      body {
        display: none;
      }
    }
    iframe {
      width: 100%;
      height: 100vh;
      border: none;
    }
  </style>
  <script>
    // Disable right-click on the entire document.
    document.addEventListener("contextmenu", function(e){
      e.preventDefault();
    }, false);
    
    // Function to block prohibited keys.
    function blockKeys(e) {
      // Disable F12, Print Screen (keyCode 44),
      // Ctrl+P (print), Ctrl+S (save), Ctrl+Shift+I (inspect)
      if (e.keyCode === 123 || e.keyCode === 44 ||
          (e.ctrlKey && (e.key === 'p' || e.key === 'P' || e.key === 's' || e.key === 'S')) ||
          (e.ctrlKey && e.shiftKey && e.key === 'I')) {
        e.preventDefault();
        return false;
      }
    }
    
    // Disable keydown event
    document.addEventListener("keydown", blockKeys, false);
    // Also capture keyup events (for Print Screen key)
    document.addEventListener("keyup", function(e){
       if(e.keyCode === 44){
          e.preventDefault();
          return false;
       }
    }, false);
  </script>
</head>
<body oncontextmenu="return false;">
  <!-- The iframe loads the PDF file, using #toolbar=0 to remove its built-in toolbar where supported -->
  <iframe src="<?php echo $fileURL; ?>#toolbar=0"></iframe>
</body>
</html>
