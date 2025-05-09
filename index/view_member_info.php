<?php
include('../config/db_connect.php');

$member_id = $_GET['id'];
$sql = "SELECT * FROM members WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();
$member = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Member Information Sheet</title>
  <style>
    body {
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f0f0f0;
      margin: 0;
      padding: 30px;
    }
    .sheet {
        background: #eeeeff;
      max-width: 900px;
      margin: auto;
      padding: 40px;
      border: 1px solid #ccc;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      position: relative;
    }
    .header {
      text-align: center;
      margin-bottom: 30px;
    }
    .btn-top {
      display: flex;
      justify-content: space-between;
      margin-bottom: 20px;
    }
    .btn {
      background-color: #007bff;
      color: white;
      border: none;
      padding: 8px 16px;
      font-size: 14px;
      cursor: pointer;
      border-radius: 4px;
      text-decoration: none;
    }
    .photo-box {
      float: right;
      width: 150px;
      height: 150px;
      border: 1px solid #000;
      margin-left: 20px;
      background-color: #eee;
    }
    .photo-box img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .section {
      margin-bottom: 30px;
      clear: both;
    }
    .section-title {
      font-weight: bold;
      border-bottom: 2px solid #000;
      padding-bottom: 5px;
      margin-bottom: 15px;
      font-size: 1.1rem;
    }
    .row {
      margin-bottom: 8px;
    }
    .label {
      display: inline-block;
      width: 220px;
      font-weight: bold;
    }
    .value {
      display: inline-block;
    }
    @media print {
      body {
        background-color: white;
        margin: 0;
      }
      .sheet {
        box-shadow: none;
        border: none;
      }
      .btn-top {
        display: none;
      }
    }
  </style>
</head>
<body>
<?php if ($member): ?>
  <div class="sheet">
    <div class="btn-top">
      <a href="memberlist.php" class="btn">← Back</a>
      <button onclick="window.print()" class="btn">🖨️ Print</button>
    </div>

    <div class="header">
      <h2>Member Information Sheet</h2>
    </div>

    <div class="section">
      <div class="photo-box">
        <?php if ($member['image']): ?>
          <img src="../uploads/<?php echo htmlspecialchars($member['image']); ?>" alt="Member Image">
        <?php else: ?>
          <p style="text-align:center; line-height:150px;">Photo</p>
        <?php endif; ?>
      </div>
      <div class="section-title">Personal Information</div>
      <div class="row"><span class="label">Full Name:</span><span class="value"><?php echo htmlspecialchars($member['name']); ?></span></div>
      <div class="row"><span class="label">Date of Birth:</span><span class="value"><?php echo htmlspecialchars($member['dob']); ?></span></div>
      <div class="row"><span class="label">Gender:</span><span class="value"><?php echo htmlspecialchars($member['gender']); ?></span></div>
      <div class="row"><span class="label">Phone:</span><span class="value"><?php echo htmlspecialchars($member['phone']); ?></span></div>
      <div class="row"><span class="label">Email:</span><span class="value"><?php echo htmlspecialchars($member['email']); ?></span></div>
      <div class="row"><span class="label">Address:</span><span class="value"><?php echo htmlspecialchars($member['address']); ?></span></div>
    </div>

    <div class="section">
      <div class="section-title">Work & License Information</div>
      <div class="row"><span class="label">Work Type:</span><span class="value"><?php echo htmlspecialchars($member['work_type']); ?></span></div>
      <div class="row"><span class="label">License Number:</span><span class="value"><?php echo htmlspecialchars($member['license_number']); ?></span></div>
      <div class="row"><span class="label">Boat Name:</span><span class="value"><?php echo htmlspecialchars($member['boat_name']); ?></span></div>
      <div class="row"><span class="label">Fishing Area:</span><span class="value"><?php echo htmlspecialchars($member['fishing_area']); ?></span></div>
    </div>

    <div class="section">
      <div class="section-title">Emergency Contact</div>
      <div class="row"><span class="label">Contact Name:</span><span class="value"><?php echo htmlspecialchars($member['emergency_name']); ?></span></div>
      <div class="row"><span class="label">Contact Phone:</span><span class="value"><?php echo htmlspecialchars($member['emergency_phone']); ?></span></div>
    </div>

    <div class="section">
      <div class="section-title">Other</div>
      <div class="row"><span class="label">Agreement Status:</span><span class="value"><?php echo $member['agreement'] ? 'Agreed' : 'Not Agreed'; ?></span></div>
    </div>
  </div>
<?php else: ?>
  <div class="sheet">
    <div class="btn-top">
      <a href="members_list.php" class="btn">← Back</a>
    </div>
    <p style="text-align:center;">Member not found.</p>
  </div>
<?php endif; ?>
</body>
</html>
