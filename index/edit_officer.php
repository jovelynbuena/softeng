<?php
session_start();

if($_SESSION['username']==""){

    header('location: login.php');
}
require_once('../config/db_connect.php');

// Get the officer ID
$id = $_GET['id'] ?? null;

if (!$id) {
    die("Invalid officer ID.");
}

// Fetch the officer details
$query = "
    SELECT officers.*, members.name 
    FROM officers 
    JOIN members ON officers.member_id = members.id 
    WHERE officers.id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$officer = $result->fetch_assoc();

if (!$officer) {
    die("Officer not found.");
}

// Update on form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $position = $_POST['position'];
    $term_start = $_POST['term_start'];
    $term_end = $_POST['term_end'];
    
    $image = $officer['image']; // Default to existing

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageName = basename($_FILES['image']['name']);
        $targetPath = "../uploads/" . $imageName;
        move_uploaded_file($_FILES['image']['tmp_name'], $targetPath);
        $image = $imageName;
    }

    $updateQuery = "
        UPDATE officers 
        SET position = ?, term_start = ?, term_end = ?, image = ? 
        WHERE id = ?
    ";

    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssssi", $position, $term_start, $term_end, $image, $id);
    $stmt->execute();

    header("Location: officerslist.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Officer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h2 class="mb-4">Edit Officer</h2>

    <form method="POST" enctype="multipart/form-data" class="shadow p-4 rounded bg-light">
        <div class="mb-3">
            <label class="form-label">Member Name</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($officer['name']) ?>" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Position</label>
            <input type="text" name="position" class="form-control" value="<?= htmlspecialchars($officer['position']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Term Start</label>
            <input type="date" name="term_start" class="form-control" value="<?= htmlspecialchars($officer['term_start']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Term End</label>
            <input type="date" name="term_end" class="form-control" value="<?= htmlspecialchars($officer['term_end']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Image</label><br>
            <?php if ($officer['image']): ?>
                <img src="../uploads/<?= htmlspecialchars($officer['image']) ?>" alt="Current" style="width: 80px; height: 80px; object-fit: cover; border-radius: 10px;">
            <?php else: ?>
                <p class="text-muted">No image uploaded</p>
            <?php endif; ?>
            <input type="file" name="image" class="form-control mt-2">
        </div>

        <div class="d-flex justify-content-between">
            <a href="officerslist.php" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-success">Save Changes</button>
        </div>
    </form>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
