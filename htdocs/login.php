<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RFID Log Viewer - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">
    <div class="card shadow p-4" style="min-width: 350px;">
        <h4 class="mb-3 text-center">🔐 Enter Admin Password</h4>
        <form method="POST">
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required autofocus>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
        </form>

        <hr>
        <h6 class="text-center mt-3">🔧 Generate Hashed Password</h6>
        <form method="POST">
            <div class="mb-3">
                <label for="newpass" class="form-label">New Password</label>
                <input type="text" class="form-control" id="newpass" name="newpass" placeholder="Type new password">
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-secondary">Generate Hash</button>
            </div>
        </form>
        <?php
        session_start();

        // Predefined hashed password for '********'
        $storedHash = '$2y$12$bxIzNrafal.oorpM33rl5uD1sN37uKEWtOnJICFFvGGLtnIVbQKt.';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['password'])) {
                $inputPassword = $_POST['password'];
                if (password_verify($inputPassword, $storedHash)) {
                    $_SESSION['authenticated'] = true;
                    header('Location: dashboard.php');
                    exit();
                } else {
                    echo '<div class="mt-3 alert alert-danger">Incorrect password</div>';
                }
            }

            if (isset($_POST['newpass']) && !empty($_POST['newpass'])) {
                $newPassword = $_POST['newpass'];
                $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
                echo '<div class="mt-3 alert alert-success">Generated Hash:<br><code>' . htmlspecialchars($newHash) . '</code></div>';
            }
        }
        ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
