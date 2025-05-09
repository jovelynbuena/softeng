<?php
session_start();
require '../config/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $remember = isset($_POST['remember']);

    $stmt = $conn->prepare("SELECT id, username, password_hash, role FROM users WHERE username = ?");
    if (!$stmt) {
        die("Query preparation failed: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row["password_hash"]) || $password === $row["password_hash"]) {
            $_SESSION["user_id"] = $row["id"];
            $_SESSION["username"] = $row["username"];
            $_SESSION["role"] = $row["role"];

            if ($remember) {
                setcookie("remembered_user", $username, time() + (86400 * 30), "/");
            } else {
                setcookie("remembered_user", "", time() - 3600, "/");
            }

            $redirectUrl = match ($row["role"]) {
                "admin" => "admin.php",
                "officer" => "officer.php",
                "member" => "member.php",
                default => "index.php?login=failed&message=Unauthorized access"
            };

            header("Location: $redirectUrl?login=success");
            exit();
        } else {
            header("Location: login.php?login=failed&message=Incorrect password");
            exit();
        }
    } else {
        header("Location: login.php?login=failed&message=User not found");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Bangkero System</title>
    <link rel="stylesheet" href="http://localhost/bangkero_system/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .options-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }
        .options-container .left,
        .options-container .right {
            display: flex;
            align-items: center;
        }
        .options-container .right a {
            text-decoration: none;
            color: #6c757d;
            font-size: 14px;
        }
        .options-container .right a:hover {
            color: #000;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="header">
                <h2>Bangkero and Fishermen Association</h2>
                <p>Barangay Barretto, Olongapo City</p>
            </div>

            <p class="sub-text">Sign in to start your session</p>

            <form action="" method="POST">
                <div class="input-group">
                    <i class="fa fa-user"></i>
                    <input type="text" name="username" placeholder="Username" required value="<?php echo isset($_COOKIE['remembered_user']) ? htmlspecialchars($_COOKIE['remembered_user']) : ''; ?>">
                </div>

                <div class="input-group">
                    <i class="fa fa-lock"></i>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                
                <div class="options-container">
                    <div class="left">
                        <input type="checkbox" name="remember" id="remember" <?php echo isset($_COOKIE['remembered_user']) ? 'checked' : ''; ?>>
                        <label for="remember">Remember Me</label>
                    </div>
                    <div class="right">
                        <a href="forgot-password.php" class="forgot-password">Forgot Password?</a>
                    </div>
                </div>
                
                <button type="submit" class="login-btn">Login</button>
            </form>
        </div>
    </div>

    <?php if (isset($_GET['login']) && $_GET['login'] === 'failed' && !empty($_GET['message'])): ?>
    <script>
        Swal.fire({
            toast: true,
            icon: 'error',
            title: "<?php echo htmlspecialchars($_GET['message']); ?>",
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });
    </script>
    <?php endif; ?>

    
</body>
</html>
