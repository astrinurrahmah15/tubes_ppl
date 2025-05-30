<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GaweYuk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: #00b300;
        }
        .navbar-brand span {
            color: #000;
        }
        .background-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            opacity: 0.5;
        }
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .login-container h1 {
            font-family: 'Comic Sans MS', cursive, sans-serif;
            color: #00b300;
            text-align: center;
            margin-bottom: 20px;
        }
        .btn-login {
            background-color: #004d00;
            color: #ffffff;
            width: 100%;
        }
        .text-center a {
            color: #00b300;
        }
    </style>
    <script>
        function validateForm(event) {
            event.preventDefault(); // Prevent the form from submitting

            // Get form values
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();

            // Clear previous error messages
            document.getElementById('error-message').innerText = '';

            // Validation checks
            if (!email) {
                showError('Email harus diisi.');
                document.getElementById('email').focus();
                return;
            }

            if (!isValidEmail(email)) {
                showError('Format email tidak valid.');
                document.getElementById('email').focus();
                return;
            }

            if (!password) {
                showError('Kata sandi harus diisi.');
                document.getElementById('password').focus();
                return;
            }

            if (password.length < 6) {
                showError('Kata sandi harus memiliki minimal 6 karakter.');
                document.getElementById('password').focus();
                return;
            }

            // If validation passes, you can proceed with form submission
            alert('Form berhasil divalidasi dan siap dikirim!');
            // document.getElementById('loginForm').submit();
        }

        function showError(message) {
            document.getElementById('error-message').innerText = message;
        }

        function isValidEmail(email) {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailPattern.test(email);
        }

        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggle-password');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-bars"></i>
                <span>Gawe</span>Yuk
            </a>
            <button aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation" class="navbar-toggler" data-bs-target="#navbarNav" data-bs-toggle="collapse" type="button">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Tentang Kami</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Lowongan Kerja</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">FAQ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Posting Pekerjaan</a>
                    </li>
                </ul>
            <div class="d-flex">
                    <a class="btn btn-outline-success me-2" href="daftar.php" role="button">Daftar</a>
                </div>
            </div>
        </div>
    </nav>
    <div class="bg">
        <div class="overlay">
            <div class="login-container">
                <h1>GaweYuk</h1>
                <form id="loginForm" action="login.php" method="POST">
    <div id="error-message" class="text-danger mb-3"></div>
    <div class="input-group mb-3">
        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
        <input type="email" id="email" name="email" class="form-control" placeholder="example@gmail.com">
    </div>
    <div class="input-group mb-3">
        <span class="input-group-text"><i class="fas fa-lock"></i></span>
        <input type="password" id="password" name="password" class="form-control" placeholder="Kata sandi">
        <span class="input-group-text" onclick="togglePasswordVisibility()">
            <i class="fas fa-eye" id="toggle-password"></i>
        </span>
    </div>
        <button type="submit" class="btn btn-login">Login</button>
                    </form>
                <div class="text-center mt-3">
                    Belum punya akun? <a href="daftar.php">DAFTAR</a>
                </div>
            </div>
        </div>
    </div>
    
    <img alt="Background image of a group of people working on laptops and documents on a wooden table" class="background-image" height="1080" src="https://storage.googleapis.com/a1aa/image/fdfUtEMTjtv9VUqKZbU8ISxIdJcxURbkIN7dFxVciwvcwe2nA.jpg" width="1920"/>
</body>
</html>
