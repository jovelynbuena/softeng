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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bangkero & Fishermen Association</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
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
            width: 150px;
            height: auto;
            margin-right: -0px;
            object-fit: contain;
            display: block;
            margin-top: -156px;
        }

        .hero-text h1, .hero-text p {
            color: black !important;
        }

        footer {
            background-color: #f8f9fa;
        }

        .card-img-top {
            height: 300px;
            object-fit: cover;
            transition: transform 0.3s ease-in-out;
        }

        .card-img-top:hover {
            transform: scale(1.05);
        }

        .card-body {
            padding: 15px;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .card-text {
            font-size: 1rem;
            color: #555;
            margin-bottom: 10px;
        }

        .card-info {
            font-size: 0.9rem;
            color: #777;
            margin: 5px 0;
        }

        .btn-container {
            text-align: center;
        }

        .btn-container .btn {
            margin: 10px;
        }

        .section-heading {
            margin-bottom: 2rem;
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
                <li class="nav-item"><a class="nav-link" href="http://localhost/bangkero_system/index/admin.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="http://localhost/bangkero_system/index/admin.php#events">Events</a></li>
                <li class="nav-item"><a class="nav-link" href="announcement.php">Announcement</a></li>
                <li class="nav-item"><a class="nav-link" href="http://localhost/bangkero_system/index/officers.php">Officers</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Help Page</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Settings</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="managementDropdown" role="button" data-bs-toggle="dropdown">Management</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="officerslist.php">Officers List</a></li>
                        <li><a class="dropdown-item" href="#">Event Scheduling</a></li>
                        <li><a class="dropdown-item" href="#">Utilities</a></li>
                        <li><a class="dropdown-item" href="memberlist.php">Member List</a></li>
                    </ul>
                </li>
                <a href="#" class="nav-link text-danger" onclick="delayedLogout(event)">Log Out</a>
                </ul>
        </div>
    </div>
</nav>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Announcements</title>
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
            display: flex;
            flex-direction: column;
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
            color: #007bff; /* Bootstrap primary link color */
            text-decoration: none; /* Remove underline by default */
            margin-right: 12px; /* Add spacing between links */
        }
        .link-group a:hover {
            text-decoration: underline; /* Add underline on hover for clarity */
        }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Announcements</h4>
        <a href="add_announcement.php" class="btn btn-primary btn-sm">+ Add Announcement</a> <!-- Add button remains -->
    </div>
    <?php while ($row = $announcements->fetch_assoc()): ?>
    <div class="announcement-item">
        <h6><?= htmlspecialchars($row['title']) ?></h6>
        <p class="mb-1">Posted on <?= date("F j, Y", strtotime($row['date_posted'])) ?> by Admin</p>
        <p><?= nl2br(htmlspecialchars(substr($row['content'], 0, 80))) ?>...</p>
        <div class="link-group">
            <a href="view_announcement.php?id=<?= $row['id'] ?>">Read</a>
            <a href="edit_announcement.php?id=<?= $row['id'] ?>">Edit</a>
            <a href="delete_announcement.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this announcement?');">Delete</a>
        </div>
    </div>
    <?php endwhile; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>