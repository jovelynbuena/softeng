<?php
include('../config/db_connect.php'); // Ensure correct path

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid request.");
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM members WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("Member not found.");
}

$member = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bangkero & Fishermen Association</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
   /* Hero Section */
.hero-section {
    background: url('images/image1.jpg') no-repeat center;
    background-size: cover;
    height: 250px;
    text-align: center;
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    padding: 20px;
}
.hero-logo {
    width: 120px;
    height: auto;
    margin-right: -28px;
    object-fit: contain;
    display: block;
    margin-top: -156px;
}
.hero-text h1 {
    color: black !important;
    font-size: 2.7rem;
    font-weight: bold;
    margin: 0;
}
.hero-text p {
    color: black !important;
    font-size: 1.2rem;
    font-weight: bold;
    margin: 0;
}
</style>
</head>
<body>
    <header class="hero-section">
        <img src="images/logo1.png" alt="Association Logo" class="hero-logo">
        <div class="hero-text">
            <h1>Bangkero and Fishermen Association</h1>
            <p>Barangay Barretto, Olongapo City</p>
        </div>
    </header>
    <div class="container mt-5">
        <h2 class="text-center">Member Details</h2>
        <div class="card p-4">
            <p><strong>ID:</strong> <?php echo htmlspecialchars($member['id']); ?></p>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($member['name']); ?></p>
            <p><strong>Contact:</strong> <?php echo htmlspecialchars($member['phone']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($member['address']); ?></p>
            <a href="admin.php" class="btn btn-secondary">Back</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
