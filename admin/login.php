<?php
session_start();


$ADMIN_USER = 'admin';

$ADMIN_PASS_HASH = '$2y$10$P5z0hZc6C1H1JxY/cVdLGe6Sohk/1k1dLtHjHkZzM/ZoOv8Av2h/e';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $_POST['user'] ?? '';
    $p = $_POST['pass'] ?? '';

    if ($u === $ADMIN_USER && password_verify($p, $ADMIN_PASS_HASH)) {
        $_SESSION['admin'] = true;
        header('Location: dashboard.php'); 
        exit;
    } else {
        $error = 'Usuario o contraseña incorrectos';
    }
}
$ADMIN_USER = 'admin';
$ADMIN_PASS = 'password';

if(isset($_POST['user'])){
    $u = $_POST['user'];
    $p = $_POST['pass'];
    if($u === $ADMIN_USER && $p === $ADMIN_PASS){
        $_SESSION['admin'] = true;
        header('Location: dashboard.php'); exit;
    } else {
        $error = 'Credenciales inválidas';
    }
}
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Admin - Login</title>
<link rel="stylesheet" href="../assets/style.css">
<style>
body {
    font-family: Arial, sans-serif;
    background-color: #fff8f6;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}
.container {
    background: #fff;
    padding: 30px 40px;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.15);
    width: 320px;
}
.login-form h2 {
    margin-top: 0;
    margin-bottom: 20px;
    text-align: center;
    color: #ff7f66;
}
.login-form label {
    display: block;
    margin-bottom: 12px;
    font-weight: bold;
    color: #555;
}
.login-form input {
    width: 100%;
    padding: 8px 10px;
    margin-top: 4px;
    border: 1px solid #ddd;
    border-radius: 6px;
    box-sizing: border-box;
}
.login-form button {
    width: 100%;
    padding: 10px;
    background-color: #ff7f66;
    border: none;
    color: white;
    font-weight: bold;
    border-radius: 6px;
    cursor: pointer;
    margin-top: 15px;
    transition: background 0.2s;
}
.login-form button:hover {
    background-color: #ff4f33;
}
.error {
    background-color: #ffe1da;
    color: #e74c3c;
    padding: 8px;
    border-radius: 6px;
    text-align: center;
    margin-bottom: 12px;
}
</style>
</head>
<body>
<main class="container">
    <form method="post" class="login-form">
        <h2>Admin - Ingresar</h2>
        <?php if(!empty($error)) echo '<div class="error">'.htmlspecialchars($error).'</div>'; ?>
        <label>Usuario
            <input type="text" name="user" required autofocus>
        </label>
        <label>Contraseña
            <input type="password" name="pass" required>
        </label>
        <button type="submit">Ingresar</button>
    </form>
</main>
</body>
</html>
