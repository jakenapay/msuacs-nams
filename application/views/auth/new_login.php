
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
        }

        /* Optional container styling if form alignment is needed */
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
    </style>
</head>
<body class="bg">
    <div class="login-container">
        <!-- Original content from the login.php file will go here -->
<div class="auth-container col-sm-12">   
    <?= $this->session->flashdata('message'); ?>
    <div class="auth-card card col-sm-6 col-md-6 col-lg-3 shadow">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="card-body">
                    <h4 class="mb-3 f-w-400 text-center">Sign-in</h4>
                    <form nams-authenticate>
                        <div class="input-mb-3">
                            <input type="text" name="username" class="form-control" placeholder="Username">
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" name="password" class="form-control" placeholder="Password">
                        </div>
                        <button type="submit" id="submit-btn" class="btn btn-block mb-4 rounded-pill">Authenticate</button>
                    </form>
                    <p class="mb-0 text-muted text-center">Powered by <a href="javascript:void(0)" id="company_name" class="f-w-400">NTEK Systems</a></p>
                </div>
            </div>
        </div>
    </div>
</div>


    <div class="wave-container">
        <svg height="250px" version="1.1" xmlns="http://www.w3.org/2000/svg" class="wave bg-wave">
            <title>Wave</title>
            <defs></defs>
            <path id="feel-the-wave"/>
        </svg>
        <svg height="250px" version="1.1" xmlns="http://www.w3.org/2000/svg" class="wave bg-wave">
            <title>Wave</title>
            <defs></defs>
            <path id="feel-the-wave-two"/>
        </svg>
        <svg height="250px" version="1.1" xmlns="http://www.w3.org/2000/svg" class="wave bg-wave"> 
            <title>Wave</title>
            <defs></defs>
            <path id="feel-the-wave-three"/>
        </svg>
    </div>
<img src="<?= base_url('assets/images/MSU_GenSan.png'); ?>" class="img-fluid" alt="">
    <script src="<?= base_url("assets/js/libs/waves.min.js"); ?>"></script>
    <script src="<?= base_url("assets/js/libs/TweenMax.min.js"); ?>"></script>
    <script src="<?= base_url("assets/js/libs/jquery.wavify.js"); ?>"></script>
    <script src="<?= base_url("assets/js/functions.js"); ?>"></script>
    <script src="<?= base_url("assets/js/login.js"); ?>"></script></div>
</body>
</html>