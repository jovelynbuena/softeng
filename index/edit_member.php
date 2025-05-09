<?php
session_start();

if($_SESSION['username']==""){

    header('location: login.php');
}
include('../config/db_connect.php');

// Redirect if no ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid or missing member ID.");
}

$memberId = intval($_GET['id']);

// Fetch member data
$stmt = $conn->prepare("SELECT * FROM members WHERE id = ?");
$stmt->bind_param("i", $memberId);
$stmt->execute();
$result = $stmt->get_result();
$member = $result->fetch_assoc();

if (!$member) {
    die("Member not found.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize and collect input
    $firstName = trim($_POST['first_name']);
    $middleInitial = trim($_POST['middle_initial']);
    $lastName = trim($_POST['last_name']);
    $fullName = "$firstName $middleInitial $lastName";

    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $workType = $_POST['work_type'];
    $licenseNumber = $_POST['license_number'];
    $boatName = $_POST['boat_name'];
    $fishingArea = $_POST['fishing_area'];
    $emergencyName = $_POST['emergency_name'];
    $emergencyPhone = $_POST['emergency_phone'];
    $agreement = isset($_POST['agreement']) ? 1 : 0;

    // Image upload
    $imageFileName = $member['image']; // fallback to existing image

    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['image']['tmp_name'];
        $originalName = basename($_FILES['image']['name']);
        $imageFileName = time() . '_' . $originalName;
        $uploadPath = "../uploads/" . $imageFileName;

        $imageInfo = getimagesize($tmpName);
        if ($imageInfo !== false) {
            if (!move_uploaded_file($tmpName, $uploadPath)) {
                die("Failed to upload image.");
            }
        } else {
            die("Invalid image file.");
        }
    }

    // Update query
    $update = $conn->prepare("
        UPDATE members SET 
            name = ?, dob = ?, gender = ?, phone = ?, email = ?, address = ?, 
            work_type = ?, license_number = ?, boat_name = ?, fishing_area = ?, 
            emergency_name = ?, emergency_phone = ?, agreement = ?, image = ?
        WHERE id = ?
    ");

    $update->bind_param(
        "ssssssssssssssi",
        $fullName, $dob, $gender, $phone, $email, $address,
        $workType, $licenseNumber, $boatName, $fishingArea,
        $emergencyName, $emergencyPhone, $agreement, $imageFileName,
        $memberId
    );

    if ($update->execute()) {
        header("Location: memberlist.php");
        exit();
    } else {
        die("Error updating member: " . $update->error);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Member</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow p-4 rounded-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="memberlist.php" class="btn btn-secondary">← Back</a>
                    <h4 class="text-center w-100 m-0">Edit Member Info</h4>
                </div>

    <!-- FORM STARTS HERE -->
    <form method="POST" enctype="multipart/form-data">
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">First Name</label>
                <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars(explode(' ', $member['name'])[0]) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Middle Initial</label>
                <input type="text" name="middle_initial" class="form-control" value="<?= htmlspecialchars(explode(' ', $member['name'])[1] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Last Name</label>
                <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars(explode(' ', $member['name'])[2] ?? '') ?>" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Date of Birth</label>
            <input type="date" name="dob" class="form-control" value="<?= htmlspecialchars($member['dob']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Gender</label>
            <select name="gender" class="form-select" required>
                <option value="Male" <?= $member['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                <option value="Female" <?= $member['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                <option value="Other" <?= $member['gender'] === 'Other' ? 'selected' : '' ?>>Other</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($member['phone']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($member['email']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($member['address']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Type of Work</label>
            <select name="work_type" class="form-select" required>
                <option value="Fisherman" <?= $member['work_type'] === 'Fisherman' ? 'selected' : '' ?>>Fisherman</option>
                <option value="Bangkero" <?= $member['work_type'] === 'Bangkero' ? 'selected' : '' ?>>Bangkero</option>
                <option value="Both" <?= $member['work_type'] === 'Both' ? 'selected' : '' ?>>Both</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">License Number</label>
            <input type="text" name="license_number" class="form-control" value="<?= htmlspecialchars($member['license_number']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Boat Name</label>
            <input type="text" name="boat_name" class="form-control" value="<?= htmlspecialchars($member['boat_name']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Fishing Area</label>
            <input type="text" name="fishing_area" class="form-control" value="<?= htmlspecialchars($member['fishing_area']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Emergency Contact Name</label>
            <input type="text" name="emergency_name" class="form-control" value="<?= htmlspecialchars($member['emergency_name']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Emergency Contact Phone</label>
            <input type="text" name="emergency_phone" class="form-control" value="<?= htmlspecialchars($member['emergency_phone']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Upload New Image (optional)</label>
            <input type="file" name="image" class="form-control" accept="image/*">
            <?php if (!empty($member['image'])): ?>
                <div class="mt-2">
                    <small>Current Image:</small><br>
                    <img src="../uploads/<?= htmlspecialchars($member['image']) ?>" width="120" alt="Current Member Image">
                </div>
            <?php endif; ?>
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" name="agreement" class="form-check-input" id="agreement" <?= $member['agreement'] ? 'checked' : '' ?>>
            <label for="agreement" class="form-check-label">I agree to the association's rules</label>
        </div>

        <button type="submit" class="btn btn-success w-100">Update Member</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
