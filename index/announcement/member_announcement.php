<?php
session_start();

if($_SESSION['username']==""){

    header('location: login.php');
}

include('../../config/db_connect.php');
$announcements = $conn->query("SELECT * FROM announcements ORDER BY date_posted DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bangkero & Fishermen Association - Member</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: 'Arial', sans-serif;
        }
        .announcement-item {
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
            padding: 16px;
            margin-bottom: 12px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
        }
        .announcement-item h6 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #333;
            margin: 0 0 8px;
        }
        .announcement-item p {
            font-size: 0.9rem;
            color: #555;
            margin: 0 0 12px;
        }
        .link-group {
            margin-top: 8px;
        }
        .link-group a {
            font-size: 0.85rem;
            color: #007bff;
            text-decoration: none;
            margin-right: 12px;
        }
        .link-group a:hover {
            text-decoration: underline;
        }
        .navbar {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="#">Bangkero & Fishermen Association</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="http://localhost/bangkero_system/index/member.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="http://localhost/bangkero_system/index/member.php#events">Events</a></li>
                <li class="nav-item"><a class="nav-link active" href="announcement.php">Announcements</a></li>
                <li class="nav-item"><a class="nav-link" href="http://localhost/bangkero_system/index/officers.php">Officers</a></li>
                <li class="nav-item"><a class="nav-link" href="help.php">Help Page</a></li>
                <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Log Out</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Announcements Section -->
<div class="container py-4">
    <div class="mb-4">
        <h4 class="fw-bold">Latest Announcements</h4>
    </div>
    <?php while ($row = $announcements->fetch_assoc()): ?>
    <div class="announcement-item">
        <h6><?= htmlspecialchars($row['title']) ?></h6>
        <p class="mb-1">Posted on <?= date("F j, Y", strtotime($row['date_posted'])) ?> by Admin</p>
        <p><?= nl2br(htmlspecialchars(substr($row['content'], 0, 100))) ?>...</p>
        <div class="link-group">
            <a href="view_announcement.php?id=<?= $row['id'] ?>">Read More</a>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
