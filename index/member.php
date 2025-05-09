<?php
session_start();

if($_SESSION['username']==""){

    header('location: login.php');
}
include('../config/db_connect.php');

// Fetch events
$sql = "SELECT * FROM events ORDER BY date ASC";
$result = $conn->query($sql); 

if (!$result) {
    die("Query failed: " . $conn->error);
}

$memberName = isset($_SESSION['member_name']) ? $_SESSION['member_name'] : 'Member';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bangkero & Fishermen Association</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

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

        footer {
            background-color: #f8f9fa;
        }

        /* Portrait image style */
        .card-img-top {
            height: 300px; /* Adjust the height to create a portrait-style image */
            object-fit: cover;
            transition: transform 0.3s ease-in-out;
        }

        .card-img-top:hover {
            transform: scale(1.05); /* Optional hover effect */
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
                    <li class="nav-item"><a class="nav-link" href="member.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#events">Events</a></li>
                    <li class="nav-item"><a class="nav-link" href="announcement/member_announcement.php">Announcement</a></li>
                    <li class="nav-item"><a class="nav-link" href="officers.php">Officers</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Help Page</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Settings</a></li>
                    <li class="nav-item">
                    <a href="#" class="nav-link text-danger" onclick="delayedLogout(event)">Log Out</a>
                    </li>
                    
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section">
        <img src="images/logo1.png" alt="Association Logo" class="hero-logo">
        <div class="hero-text">
            <h1>Bangkero and Fishermen Association</h1>
            <p>Barangay Barretto, Olongapo City</p>
        </div>
    </header>

    <!-- Welcome Message -->
    <div class="container mt-4 text-center">
        <h2>Welcome, <?php echo htmlspecialchars($memberName); ?>!</h2>
        <p>Glad to have you back. Here's what's happening:</p>
    </div>

    <!-- Events Section with Photos -->
    <div class="container mt-5" id="events">
        <h3 class="text-center mb-4">Upcoming Events</h3>
        <div class="row">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <?php if (!empty($row['event_poster'])): ?>
                            <img src="../uploads/<?php echo htmlspecialchars($row['event_poster']); ?>" class="card-img-top" alt="Event Poster">
                        <?php else: ?>
                            <img src="../uploads/default.jpg" class="card-img-top" alt="Default Event Image">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['event_name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
                            <p class="card-info"><strong>Date:</strong> <?php echo $row['date']; ?></p>
                            <p class="card-info"><strong>Time:</strong> <?php echo $row['time']; ?></p>
                            <p class="card-info"><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Quick Access Buttons -->
    <div class="container text-center mt-4 mb-5 btn-container">
        <a href="officers.php" class="btn btn-outline-primary m-2">View Officers</a>
        <a href="#" class="btn btn-outline-success m-2">Settings</a>
        <a href="#" class="nav-link text-danger" onclick="confirmLogout(event)">Log Out</a>
    </div>

    <!-- Footer -->
    <footer class="text-center p-3">
        <small>&copy; <?php echo date("Y"); ?> Bangkero & Fishermen Association. All rights reserved.</small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert Success for Login -->
    <?php if (isset($_GET['login']) && $_GET['login'] === 'success'): ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        Toast.fire({
            icon: "success",
            title: "Signed in successfully"
        });
    </script>
    <?php endif; ?>
    <script>
    function delayedLogout(event) {
        event.preventDefault();

        const overlay = document.createElement('div');
        overlay.style.position = 'fixed';
        overlay.style.top = 0;
        overlay.style.left = 0;
        overlay.style.width = '100%';
        overlay.style.height = '100%';
        overlay.style.backgroundColor = 'rgba(255, 255, 255, 0.8)';
        overlay.style.display = 'flex';
        overlay.style.alignItems = 'center';
        overlay.style.justifyContent = 'center';
        overlay.style.zIndex = 9999;
        overlay.innerHTML = `<div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Logging out...</span>
                             </div>
                             <p class="ms-2 fw-bold">Logging out...</p>`;

        document.body.appendChild(overlay);

        setTimeout(() => {
            window.location.href = 'logout.php';
        }, 1000);
    }
</script>

    
</body>
</html>
