
<?php
session_start();

if($_SESSION['username']==""){

    header('location: login.php');
}

include('../config/db_connect.php');

if (isset($_POST['submit'])) {
    // Sanitize event inputs
    $event_name = htmlspecialchars($_POST['event_name'], ENT_QUOTES, 'UTF-8');
    $event_date = $_POST['event_date'];
    $event_description = htmlspecialchars($_POST['event_description'], ENT_QUOTES, 'UTF-8');
    $event_time = $_POST['event_time'];
    $event_location = htmlspecialchars($_POST['event_location'], ENT_QUOTES, 'UTF-8');

    // File upload settings
    $target_dir = "../uploads/";
    $file_name = basename($_FILES["event_poster"]["name"]);
    $file_name = preg_replace("/[^a-zA-Z0-9\-_\.]/", "_", $file_name);
    $target_file = $target_dir . $file_name;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["event_poster"]["tmp_name"]);
    if ($check === false) {
        $uploadOk = 0;
        echo "File is not an image.";
    }

    if ($_FILES["event_poster"]["size"] > 500000) {
        $uploadOk = 0;
        echo "File is too large.";
    }

    if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
        $uploadOk = 0;
        echo "Only JPG, JPEG, PNG & GIF allowed.";
    }

    if ($uploadOk && move_uploaded_file($_FILES["event_poster"]["tmp_name"], $target_file)) {
        $poster_path = $target_file;

        $stmt = $conn->prepare("INSERT INTO events (event_name, description, date, time, location, event_poster) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("ssssss", $event_name, $event_description, $event_date, $event_time, $event_location, $poster_path);
            if ($stmt->execute()) {
                header('Location: member.php');
                exit();
            } else {
                echo "Execute error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Prepare failed: " . $conn->error;
        }
    } else {
        echo "There was an error uploading the file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Event</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        h2 {
            color: #333;
        }
        form {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }
        label {
            font-weight: bold;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <h2>Add Event</h2>

    <!-- Form to submit event details -->
    <form action="upload_event.php" method="POST" enctype="multipart/form-data">
        <label for="event_name">Event Name:</label>
        <input type="text" name="event_name" id="event_name" required><br>
        <label for="event_time">Event Time:</label>
<input type="time" name="event_time" id="event_time" required><br>

<label for="event_location">Event Location:</label>
<input type="text" name="event_location" id="event_location" required><br>


        <label for="event_date">Event Date:</label>
        <input type="date" name="event_date" id="event_date" required><br>

        <label for="event_description">Event Description:</label>
        <textarea name="event_description" id="event_description" rows="4" required></textarea><br>

        <label for="event_poster">Event Poster:</label>
        <input type="file" name="event_poster" id="event_poster" accept="image/*" required><br>

        <button type="submit" name="submit">Add Event</button>
    </form>

</body>
</html>
