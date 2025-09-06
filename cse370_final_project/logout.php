<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


header("Location: index.php");
exit();
?>
```eof
```php:Updated Signup Page:signup.php
<?php
require 'connection.php';

$message = ''; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $password = $_POST['password'];
    $isAdmin = isset($_POST['isAdmin']) ? 1 : 0;

    $checkStmt = $connection->prepare("SELECT user_id FROM user WHERE user_id = ? OR email = ?");
    $checkStmt->bind_param("is", $user_id, $email);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        $message = "<p style='color: red; text-align: center;'>Error: A user with this ID or Email already exists.</p>";
    } else {

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

 
        $stmt = $connection->prepare("INSERT INTO user (user_id, name, email, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $name, $email, $hashed_password);

        if ($stmt->execute()) {
            if ($isAdmin) {
                $adminStmt = $connection->prepare("INSERT INTO admin (user_id) VALUES (?)");
                $adminStmt->bind_param("i", $user_id);
                $adminStmt->execute();
                $adminStmt->close();
            }
            $message = "<p style='color: green; text-align: center;'>Signup successful! You can now <a href='login.php'>Login here</a>.</p>";
        } else {
            $message = "<p style='color: red; text-align: center;'>Error: Could not register user.</p>";
        }
        $stmt->close();
    }
    $checkStmt->close();
}

include 'header.php';
?>

<form method="POST">
    <h2>Create an Account</h2>
    <?php echo $message; ?>

    <label for="user_id">User ID:</label>
    <input type="number" id="user_id" name="user_id" required>

    <label for="name">Full Name:</label>
    <input type="text" id="name" name="name" required>

    <label for="email">Email Address:</label>
    <input type="email" id="email" name="email" required>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>
    
    <div style="margin-top: 1rem; text-align: center;">
      <input type="checkbox" id="isAdmin" name="isAdmin" value="1" style="width: auto;">
      <label for="isAdmin" style="display: inline; font-weight: normal;">Register as an Administrator</label>
    </div>
    <br>

    <button type="submit">Sign Up</button>
</form>

<?php include 'footer.php'; ?>
