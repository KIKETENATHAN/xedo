<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Futuristic Sign Up</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background: linear-gradient(135deg, #0f2027, #2c5364, #1a2980);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Orbitron', Arial, sans-serif;
            color: #fff;
            margin: 0;
        }
        .signup-container {
            background: rgba(44, 83, 100, 0.85);
            padding: 40px 30px;
            border-radius: 16px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255,255,255,0.18);
            width: 350px;
        }
        .signup-container h2 {
            text-align: center;
            margin-bottom: 24px;
            letter-spacing: 2px;
        }
        .signup-container input[type="text"],
        .signup-container input[type="email"],
        .signup-container input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0 20px 0;
            border: none;
            border-radius: 8px;
            background: rgba(255,255,255,0.1);
            color: #fff;
            font-size: 16px;
            outline: none;
            transition: background 0.3s;
        }
        .signup-container input[type="text"]:focus,
        .signup-container input[type="email"]:focus,
        .signup-container input[type="password"]:focus {
            background: rgba(255,255,255,0.2);
        }
        .signup-container button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(90deg, #1a2980, #26d0ce);
            color: #fff;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            letter-spacing: 1px;
            transition: background 0.3s;
        }
        .signup-container button:hover {
            background: linear-gradient(90deg, #26d0ce, #1a2980);
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h2>Sign Up</h2>
        <form action="signup.php" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Create Account</button>
        </form>
    </div>
</body>
</html>