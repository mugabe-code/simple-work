<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = "";
$username = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (!empty($username) && !empty($password)) {
        $conn = new mysqli("localhost", "root", "", "smartpark_sims");
        
        if ($conn->connect_error) {
            die("Connection failed");
        }
        
        $sql = "SELECT user_id, names, username FROM users WHERE username = ? AND password = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['names'] = $user['names'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid username or password";
        }
        $stmt->close();
        $conn->close();
    } else {
        $error = "Please enter both username and password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartPark SIMS - Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .login-box {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            overflow: hidden;
        }
        .login-header {
            background: #007bff;
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .login-header h1 {
            font-size: 28px;
            margin: 0 0 5px 0;
        }
        .login-header h2 {
            font-size: 18px;
            margin: 0 0 10px 0;
            opacity: 0.9;
        }
        .login-header p {
            margin: 0;
            font-size: 14px;
            opacity: 0.8;
        }
        .error-box {
            background: #f8d7da;
            color: #721c24;
            padding: 15px 20px;
            border-left: 4px solid #dc3545;
            margin: 0;
            font-size: 14px;
        }
        .login-form {
            padding: 30px 20px;
        }
        .input-group {
            margin-bottom: 20px;
        }
        .input-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
            font-size: 14px;
        }
        .input-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .input-group input:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
        }
        .login-button {
            width: 100%;
            padding: 14px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }
        .login-button:hover {
            background: #0056b3;
        }
        .login-footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #eee;
            font-size: 13px;
            color: #666;
        }
        .login-footer p {
            margin: 5px 0;
        }
        .login-footer strong {
            color: #333;
        }
        @media (max-width: 480px) {
            body {
                padding: 10px;
            }
            .login-header {
                padding: 20px 15px;
            }
            .login-header h1 {
                font-size: 24px;
            }
            .login-form {
                padding: 20px 15px;
            }
            .input-group input {
                padding: 10px 12px;
                font-size: 16px;
            }
            .login-button {
                padding: 12px;
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="login-header">
            <h1>SmartPark</h1>
            <h2>Stock Inventory System</h2>
            <p>Please sign in to continue</p>
        </div>
        
        <?php if ($error != ""): ?>
            <div class="error-box"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" class="login-form">
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" 
                       value="<?php echo htmlspecialchars($username); ?>" 
                       placeholder="Enter your username" required>
            </div>
            
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" 
                       placeholder="Enter your password" required>
            </div>
            
            <button type="submit" class="login-button">Sign In</button>
        </form>
        
    </div>
</body>
</html>