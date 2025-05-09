<?php
session_start();

if ($_SESSION['username'] == "") {
    header('location: login.php');
}

include('../config/db_connect.php');

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $conn->query("DELETE FROM members WHERE id=$id");
    header("Location: memberlist.php?deleted=1");
    exit();
}

// Fetch members based on search query
$search = "";
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $sql = "SELECT * FROM members WHERE name LIKE '%$search%' OR phone LIKE '%$search%' OR address LIKE '%$search%'";
} else {
    $sql = "SELECT * FROM members";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Member List - Bangkero & Fishermen Association</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .hero-section {
            background: url('images/image1.jpg') no-repeat center;
            background-size: cover;
            height: 250px;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .hero-logo {
            width: 120px;
            height: auto;
            margin-right: -28px;
            margin-top: -156px;
            object-fit: contain;
        }
        .hero-text h1, .hero-text p {
            color: black !important;
            font-weight: bold;
            margin: 0;
        }
        .hero-text h1 { font-size: 2.7rem; }
        .hero-text p { font-size: 1.2rem; }

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
                <li class="nav-item"><a class="nav-link" href="officerslist.php">Officers</a></li>
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
    <img src="images/logo1.png" alt="Logo" class="hero-logo">
    <div class="hero-text">
        <h1>Bangkero and Fishermen Association</h1>
        <p>Barangay Barretto, Olongapo City</p>
    </div>
</header>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="add_member.php" class="btn btn-primary">+ Add New Member</a>
        <form class="d-flex" method="GET" action="memberlist.php">
            <input type="text" name="search" class="form-control me-2" placeholder="Search members..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-outline-primary">Search</button>
        </form>
    </div>

    <div class="table-responsive shadow-sm rounded-4">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Address</th>
                    <th>More Info</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php $count = 1; ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="text-center"><?= $count++ ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                        <td><?= htmlspecialchars($row['address']) ?></td>
                        <td class="text-center">
                            <a href="view_member_info.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm">View</a>
                        </td>
                        <td class="text-center action-buttons">
                            <a href="edit_member.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <button onclick="confirmDelete(<?= $row['id'] ?>)" class="btn btn-danger btn-sm">Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center text-muted">No members found.</td></tr>
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
            text: "This member will be permanently removed.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'memberlist.php?delete=' + id;
            }
        });
    }

    <?php if (isset($_GET['deleted'])): ?>
        Swal.fire({
            icon: 'success',
            title: 'Deleted!',
            text: 'Member deleted successfully.',
            timer: 2000,
            showConfirmButton: false
        }); 
    <?php endif; ?>
</script>
</body>
</html>