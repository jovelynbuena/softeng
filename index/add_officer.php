<?php
session_start();

if($_SESSION['username']==""){

    header('location: login.php');
}
require_once('../config/db_connect.php');

// Use the correct SQL for your table structure
$query = "SELECT id, name FROM members ORDER BY name ASC";
$stmt = $conn->prepare($query);

// Check for errors in the SQL
if (!$stmt) {
    die("SQL error: " . $conn->error);
}

$stmt->execute();
$result = $stmt->get_result();
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
    flex-direction: row; /* Align elements horizontally */
    justify-content: center;
    align-items: center;
    padding: 20px;
}
/* Logo Styling */
.hero-logo {
    width: 120px; /* Slightly reduce size */
    height: auto;
    margin-right: -28px; /* Space between logo and title */
    object-fit: contain; /* Ensures it fits within the shape */
    display: block;
    margin-top: -156px; /* Moves the logo higher */
}
/* Updated Title and Subtitle */
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
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">Bangkero & Fishermen Association</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="admin.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#events">Events</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Announcement</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Officers</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="managementDropdown" role="button" data-bs-toggle="dropdown">Management</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="officerslist.php">Officers List</a></li>
                            <li><a class="dropdown-item" href="#">Event Scheduling</a></li>
                            <li><a class="dropdown-item" href="#">Utilities</a></li>
                            <li><a class="dropdown-item" href="memberlist.php">Member List</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="#">Help Page</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Settings</a></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Log Out</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="hero-section">
        <img src="images/logo1.png" alt="Association Logo" class="hero-logo">
        <div class="hero-text">
            <h1>Bangkero and Fishermen Association</h1>
            <p>Barangay Barretto, Olongapo City</p>
        </div>
    </header>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Member</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <div class="card shadow rounded-4">
            <div class="card-body p-5">
                <h2 class="text-center mb-4">Assign Officer</h2>

                <form action="assign_officers_form.php" method="GET">
    <div class="mb-3">
        <label for="member_id" class="form-label">Select Member</label>
        <select name="member_id" id="member_id" class="form-select" required>
            <option value="" disabled selected>-- Choose a Member --</option>
            <?php while ($row = $result->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($row['id']) ?>">
                    <?= htmlspecialchars($row['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    <button type="submit" class="btn btn-primary w-100">Assign as Officer</button>
</form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
