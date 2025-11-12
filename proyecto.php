<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();


$servername = "p:sql.freedb.tech"; // conexión persistente
$usernameDB = "freedb_Sayun";
$passwordDB = "v*K%M4sBNp8PKj3";
$dbname = "freedb_Prueba12";


$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);
if ($conn->connect_errno) {
    die("Error de conexión: " . $conn->connect_error);
}


$conn->query("
CREATE TABLE IF NOT EXISTS login (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

$error = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = trim($_POST['password'] ?? '');

    if ($user === '' || $pass === '') {
        $error = "Por favor, completa todos los campos.";
    } else {
       
        $stmt = $conn->prepare("SELECT username, password FROM login WHERE username = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param("s", $user);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($row = $res->fetch_assoc()) {
               
                if ($row['password'] === $pass) {
                    $_SESSION['username'] = $row['username'];
                    header('Location: menu.php');
                    exit;
                } else {
                    $error = "Contraseña incorrecta.";
                }
            } else {
                $error = "Usuario incorrecto.";
            }
            $stmt->close();
        } else {
            $error = "Error interno en la base de datos.";
        }
    }
}
$conn->close();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Login</title>
  <style>
    body { font-family: Arial, sans-serif; background:#f3f3f3; display:flex; height:100vh; align-items:center; justify-content:center; margin:0; }
    form { background:#fff; padding:30px; border-radius:10px; box-shadow:0 6px 18px rgba(0,0,0,0.1); width:320px; }
    h2 { text-align:center; margin:0 0 20px 0; }
    label { display:block; margin-top:10px; font-weight:bold; }
    input { width:100%; padding:8px; margin-top:5px; border-radius:6px; border:1px solid #ccc; box-sizing:border-box; }
    button { width:100%; padding:10px; background:#007bff; color:#fff; border:none; border-radius:6px; margin-top:20px; cursor:pointer; font-weight:bold; }
    button:hover { background:#0056b3; }
    .error { color:red; text-align:center; margin-top:10px; }
  </style>
</head>
<body>
  <form method="post">
    <h2>Iniciar sesión</h2>
    <?php if ($error): ?>
      <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <label>Usuario:</label>
    <input type="text" name="username" required autofocus>
    <label>Contraseña:</label>
    <input type="password" name="password" required>
    <button type="submit">Entrar</button>
  </form>
</body>
</html>
