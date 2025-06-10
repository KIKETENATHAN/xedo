<?php
session_start();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Dummy credentials for demonstration
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    if ($username === 'admin' && $password === 'password123') {
        $_SESSION['user'] = $username;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Futuristic Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background: linear-gradient(135deg, #0f2027, #2c5364, #1a2980, #26d0ce);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
        }
        .login-card {
            background: rgba(30, 30, 60, 0.95);
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.18);
            padding: 40px 30px;
            width: 340px;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        .login-card::before {
            content: '';
            position: absolute;
            top: -60px;
            left: -60px;
            width: 180px;
            height: 180px;
            background: linear-gradient(135deg, #00f2fe 0%, #4facfe 100%);
            opacity: 0.15;
            border-radius: 50%;
            z-index: 0;
        }
        .login-card h2 {
            color: #fff;
            margin-bottom: 24px;
            letter-spacing: 2px;
            font-weight: 600;
            z-index: 1;
        }
        .login-card form {
            width: 100%;
            z-index: 1;
        }
        .input-group {
            margin-bottom: 20px;
            position: relative;
        }
        .input-group input {
            width: 100%;
            padding: 12px 16px;
            border: none;
            border-radius: 8px;
            background: rgba(255,255,255,0.08);
            color: #fff;
            font-size: 16px;
            outline: none;
            transition: background 0.2s;
        }
        .input-group input:focus {
            background: rgba(76, 175, 255, 0.18);
        }
        .input-group label {
            position: absolute;
            left: 16px;
            top: 12px;
            color: #b0c4de;
            font-size: 14px;
            pointer-events: none;
            transition: 0.2s;
        }
        .input-group input:focus + label,
        .input-group input:not(:placeholder-shown) + label {
            top: -10px;
            left: 8px;
            font-size: 12px;
            color: #4facfe;
            background: #1e1e3c;
            padding: 0 4px;
            border-radius: 4px;
        }
        .login-btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(90deg, #00f2fe 0%, #4facfe 100%);
            color: #1e1e3c;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(76, 175, 255, 0.2);
            transition: background 0.2s, color 0.2s;
        }
        .login-btn:hover {
            background: linear-gradient(90deg, #4facfe 0%, #00f2fe 100%);
            color: #fff;
        }
        .error {
            color: #ff6b6b;
            margin-bottom: 16px;
            text-align: center;
            font-size: 15px;
            z-index: 1;
        }
        @media (max-width: 400px) {
            .login-card {
                width: 95vw;
                padding: 30px 10px;
            }
        }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>Login</h2>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post" autocomplete="off">
            <div class="input-group">
                <input type="text" name="username" id="username" required placeholder=" " autocomplete="username">
                <label for="username">Username</label>
            </div>
            <div class="input-group">
                <input type="password" name="password" id="password" required placeholder=" " autocomplete="current-password">
                <label for="password">Password</label>
            </div>
            <button class="login-btn" type="submit">Sign In</button>
        </form>
    </div>
</body>
</html>