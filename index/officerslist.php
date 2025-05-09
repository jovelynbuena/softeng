
<?php
session_start();

if($_SESSION['username']==""){

    header('location: login.php');
}
require_once('../config/db_connect.php');

// Fetch officers with member names
$query = "
    SELECT 
        officers.id,
        officers.position,
        officers.term_start,
        officers.term_end,
        officers.image,
        members.name AS member_name
    FROM officers
    JOIN members ON officers.member_id = members.id
    ORDER BY officers.position ASC
";

$result = $conn->query($query);
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

        .officer-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 10px;
        }

        .table th, .table td {
            vertical-align: middle;
        }

        .action-buttons .btn {
            margin-right: 5px;
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
                <li class="nav-item"><a class="nav-link" href="announcement/admin_announcement.php">Announcement</a></li>
                <li class="nav-item"><a class="nav-link" href="officers.php">Officers</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="managementDropdown" role="button" data-bs-toggle="dropdown">Management</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="officerslist.php">Officers List</a></li>
                        <li><a class="dropdown-item" href="upload_event.php">Event Scheduling</a></li>
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

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="add_officer.php" class="btn btn-primary">+ Assign New Officer</a>
        <h2 class="fw-bold mb-0 text-center flex-grow-1">Admin Panel — Officers List</h2>
    </div>

    <div class="table-responsive shadow-sm rounded-4">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th>#</th>
                    <th>Photo</th>
                    <th>Member Name</th>
                    <th>Position</th>
                    <th>Term Start</th>
                    <th>Term End</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php $count = 1; ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="text-center"><?= $count++ ?></td>
                            <td class="text-center">
                                <?php if (!empty($row['image'])): ?>
                                    <img src="../uploads/<?= htmlspecialchars($row['image']) ?>" class="officer-img" alt="Officer Image">
                                <?php else: ?>
                                    <img src="https://via.placeholder.com/60x60?text=No+Image" class="officer-img" alt="No Image">
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['member_name']) ?></td>
                            <td><?= ucwords(htmlspecialchars($row['position'])) ?></td>
                            <td class="text-center"><?= ($row['term_start'] !== "0000-00-00") ? htmlspecialchars($row['term_start']) : 'N/A' ?></td>
                            <td class="text-center"><?= ($row['term_end'] !== "0000-00-00") ? htmlspecialchars($row['term_end']) : 'N/A' ?></td>
                            <td class="text-center action-buttons">
                                <a href="edit_officer.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $row['id'] ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">No officers assigned yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You are about to delete this officer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'delete_officer.php?id=' + id;
            }
        });
    }

    <?php if (isset($_GET['deleted'])): ?>
        Swal.fire({
            icon: 'success',
            title: 'Deleted!',
            text: 'Officer has been deleted successfully.',
            timer: 2000,
            showConfirmButton: false
        });
    <?php endif; ?>
</script>
</body>
</html>
