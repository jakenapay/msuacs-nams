
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
        /* Full background image styling */
        body, html {
            height: 100%;
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        .bg {
            background-image: url('<?= base_url('assets/images/MSU_GenSan.jpeg'); ?>');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Overlay form container styling */
        .login-container {
            background: rgba(255, 255, 255, 0.1); /* Semi-transparent */
            padding: 40px 30px;
            border-radius: 10px;
            text-align: center;
            max-width: 400px;
            width: 100%;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.2);
        }

        .login-container h1 {
            color: #FFFFFF;
            font-size: 2rem;
            margin-bottom: 30px;
        }

        .form-control {
            background-color: transparent;
            border: none;
            border-bottom: 2px solid #FFFFFF;
            color: #FFFFFF;
            margin-bottom: 20px;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #FFFFFF;
        }

        .btn-login {
            background-color: #d9534f; /* Red color for the button */
            color: #FFFFFF;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
        }

        .btn-login:hover {
            background-color: #c9302c;
        }
    </style>
</head>
<body>
    <div class="bg">
        <div class="login-container">
            <img src="<?= base_url('assets/images/MSU_GenSan.png'); ?>" alt="Logo" style="width: 80px; margin-bottom: 20px;">
            <h1>WELCOME</h1>
            <form action="login.php" method="post">
                <input type="text" class="form-control" name="username" placeholder="Username" required>
                <input type="password" class="form-control" name="password" placeholder="Password" required>
                <button type="submit" class="btn btn-login">LOGIN</button>
            </form>
        </div>
    </div>
</body>
</html>
