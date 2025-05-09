<?php
session_start();

if ($_SESSION['username'] == "") {
    header('location: login.php');
    exit;
}

// Check role session is set
if (!isset($_SESSION['role'])) {
    // Optional: handle unknown role
    $_SESSION['role'] = 'member'; // default to member if not set
}

include('../config/db_connect.php');

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

        body {
            background-color: #f8f9fa;
        }

        .page-title {
            font-weight: 700;
            font-size: 2.5rem;
            text-align: center;
            margin: 50px 0 30px;
        }

        .officer-card {
            border: 1px solid #dee2e6;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            transition: transform 0.2s ease-in-out;
            background-color: #fff;
            height: 100%;
        }

        .officer-card:hover {
            transform: scale(1.02);
        }

        .officer-img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-bottom: 1px solid #ddd;
        }

        .officer-details {
            padding: 20px;
            text-align: center;
        }

        .officer-details h5 {
            margin-bottom: 8px;
            font-weight: bold;
        }

        .position {
            color: #555;
            font-size: 1rem;
            font-weight: 600;
        }

        .term {
            font-size: 0.9rem;
            color: #888;
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
                    <li class="nav-item">
                        <a class="nav-link" href="<?= ($_SESSION['role'] == 'admin') ? 'admin.php' : 'member.php' ?>">Home</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="#events">Events</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Announcement</a></li>
                    <li class="nav-item"><a class="nav-link" href="officers.php">Officers</a></li>
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

    <div class="container">
        <h2 class="page-title">Meet Our Officers</h2>
        <div class="row g-4 mb-5">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="officer-card">
                            <?php if (!empty($row['image'])): ?>
                                <img src="../uploads/<?= htmlspecialchars($row['image']) ?>" alt="Officer Image" class="officer-img">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/300x220?text=No+Image" alt="No Image" class="officer-img">
                            <?php endif; ?>
                            <div class="officer-details">
                                <h5><?= htmlspecialchars($row['member_name']) ?></h5>
                                <div class="position"><?= ucwords(htmlspecialchars($row['position'])) ?></div>
                                <div class="term">
                                    <?= ($row['term_start'] !== "0000-00-00") ? htmlspecialchars($row['term_start']) : 'N/A' ?>
                                    —
                                    <?= ($row['term_end'] !== "0000-00-00") ? htmlspecialchars($row['term_end']) : 'N/A' ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center text-muted">No officers have been assigned yet.</div>
            <?php endif; ?>

            <!-- Footer -->
<footer class="text-center p-3">
    <small>&copy; <?php echo date("Y"); ?> Bangkero & Fishermen Association. All rights reserved.</small>
</footer>

        </div>
    </div>
</body>
</html>
