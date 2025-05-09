<?php 
include('../config/db_connect.php');

// Optional: Enable error reporting for mysqli (for debugging only, remove on production)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $middle_initial = $_POST['middle_initial'];
    $last_name = $_POST['last_name'];
    $name = $first_name . ' ' . $middle_initial . ' ' . $last_name;

    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username)) {
        echo "Username is required!";
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $work_type = $_POST['work_type'];
    $license_number = $_POST['license_number'] ?? '';
    $boat_name = $_POST['boat_name'];
    $fishing_area = $_POST['fishing_area'];
    $emergency_name = $_POST['emergency_name'];
    $emergency_phone = $_POST['emergency_phone'];
    $email = $_POST['email'];
    $agreement = isset($_POST['agreement']) ? 1 : 0;

    // Check if email already exists in members
    $email_check = $conn->prepare("SELECT id FROM members WHERE email = ?");
    if (!$email_check) {
        die("Prepare failed (email check): " . $conn->error);
    }
    $email_check->bind_param("s", $email);
    $email_check->execute();
    $email_check->store_result();
    if ($email_check->num_rows > 0) {
        echo "Email already exists. Please use a different email.";
        exit();
    }
    $email_check->close();

    // Handle image upload
    $image_name = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $target_dir = '../uploads/';
        $target_file = $target_dir . $image_name;

        $check = getimagesize($image_tmp);
        if ($check !== false) {
            if (!move_uploaded_file($image_tmp, $target_file)) {
                echo "Error uploading the image.";
                exit();
            }
        } else {
            echo "File is not an image.";
            exit();
        }
    }

    // Insert into members
    $stmt = $conn->prepare("INSERT INTO members (name, dob, gender, phone, address, work_type, license_number, boat_name, fishing_area, emergency_name, emergency_phone, agreement, image, email) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        die("Prepare failed (members insert): " . $conn->error);
    }

    $stmt->bind_param("ssssssssssssss", $name, $dob, $gender, $phone, $address, $work_type, $license_number, $boat_name, $fishing_area, $emergency_name, $emergency_phone, $agreement, $image_name, $email);

    if ($stmt->execute()) {
        $member_id = $conn->insert_id;

        // Insert into users
        $role = 'member'; 
        $created_at = date("Y-m-d H:i:s");

        $stmt_user = $conn->prepare("INSERT INTO users (member_id, username, password_hash, role, created_at, email) VALUES (?, ?, ?, ?, ?, ?)");
        
        if (!$stmt_user) {
            die("Prepare failed (users insert): " . $conn->error);
        }

        $stmt_user->bind_param("isssss", $member_id, $username, $hashed_password, $role, $created_at, $email);

        if ($stmt_user->execute()) {
            header("Location: memberlist.php");
            exit();
        } else {
            if ($conn->errno == 1062) {
                echo "Username already exists. Please choose another.";
            } else {
                echo "Error inserting into users table: " . $stmt_user->error;
            }
        }

        $stmt_user->close();
    } else {
        echo "Error inserting into members table: " . $stmt->error;
    }

    $stmt->close();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Member - Bangkero & Fishermen Association</title>
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
                <li class="nav-item"><a class="nav-link" href="#members">Members</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Announcement</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Officers</a></li>
                <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Log Out</a></li>
            </ul>
        </div>
    </div>
</nav>

<header class="hero-section">
    <img src="images/logo1.png" alt="Association Logo" class="hero-logo">
    <div class="hero-text">
        <h1>Add Member</h1>
        <p>Bangkero and Fishermen Association</p>
    </div>
</header>

<div class="container mt-4">
    <div class="bg-light p-4 rounded shadow mx-auto" style="max-width:800px;">
        <h3 class="text-center text-primary mb-4">Add Member</h3>
        <form action="add_member.php" method="POST" enctype="multipart/form-data">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">First Name</label>
                    <input type="text" class="form-control" name="first_name" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Middle Initial</label>
                    <input type="text" class="form-control" name="middle_initial">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Last Name</label>
                    <input type="text" class="form-control" name="last_name" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" class="form-control" name="dob" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Gender</label>
                    <select class="form-select" name="gender">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Phone Number</label>
                <input type="text" class="form-control" name="phone" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-control" name="email" required>
             </div>
            
            <div class="mb-3">
                <label class="form-label">Address</label>
                <input type="text" class="form-control" name="address" required>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Type of Work</label>
                    <select class="form-select" name="work_type">
                        <option value="Fisherman">Fisherman</option>
                        <option value="Bangkero">Bangkero</option>
                        <option value="Both">Both</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">License Number</label>
                    <input type="number" class="form-control" name="license_number">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Boat Ownership</label>
                    <select class="form-select" name="boat_ownership">
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Boat Name (if any)</label>
                    <input type="text" class="form-control" name="boat_name">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Fishing Area / Route</label>
                <input type="text" class="form-control" name="fishing_area">
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Emergency Contact Name</label>
                    <input type="text" class="form-control" name="emergency_name">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Emergency Contact Phone</label>
                    <input type="text" class="form-control" name="emergency_phone">
                </div>
            </div>

            <div class="mb-3">
                 <label class="form-label">Username</label>
                 <input type="text" class="form-control" name="username" required>
                </div>

            <div class="mb-3">
                 <label class="form-label">Password</label>
                 <input type="password" class="form-control" name="password" required>
                </div>


            <div class="mb-3">
                <label class="form-label">Upload Image</label>
                <input type="file" class="form-control" name="image" accept="image/*" required>
            </div>

            <div class="form-check mb-4">
                <input type="checkbox" class="form-check-input" name="agreement" required>
                <label class="form-check-label">I agree to follow the association’s rules</label>
            </div>

            <button type="submit" class="btn btn-primary w-100">Submit</button>
        </form>
    </div>
</div>

</body>
</html>
