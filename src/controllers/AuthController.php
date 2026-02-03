<?php
class AuthController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // === MÉTODOS PARA REGISTRO ===
    public function showRegister() {
        include '../views/auth/register.php';
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $nombre = trim($_POST['nombre'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $pais = $_POST['pais'] ?? '';
        $celular = trim($_POST['celular'] ?? '');
        $usuario = trim($_POST['usuario'] ?? '');
        $contrasena = $_POST['contrasena'] ?? '';

        if (empty($nombre) || empty($correo) || empty($pais) || empty($celular) || empty($usuario) || empty($contrasena)) {
            $error = "Todos los campos son obligatorios.";
            include '../views/auth/register.php';
            return;
        }

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE usuario = ? OR correo = ?");
        $stmt->execute([$usuario, $correo]);
        if ($stmt->fetchColumn() > 0) {
            $error = "Usuario o correo ya registrado.";
            include '../views/auth/register.php';
            return;
        }

        $hashed = password_hash($contrasena, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("
            INSERT INTO usuarios (nombre_completo, correo, pais, celular, usuario, contrasena, rol)
            VALUES (?, ?, ?, ?, ?, ?, 'cliente')
        ");
        if ($stmt->execute([$nombre, $correo, $pais, $celular, $usuario, $hashed])) {
            header("Location: /public/index.php?action=login&msg=registered");
            exit;
        } else {
            $error = "Error al registrar. Intente nuevamente.";
            include '../views/auth/register.php';
            return;
        }
    }

    // === LOGIN Y LOGOUT ===
    public function loginProcess() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        
        $usuario = $_POST['usuario'] ?? '';
        $contrasena = $_POST['contrasena'] ?? '';

        if (empty($usuario) || empty($contrasena)) {
            $error = "Complete todos los campos.";
            include '../views/auth/login.php';
            return;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE usuario = ?");
        $stmt->execute([$usuario]);
        $user = $stmt->fetch();

        if ($user && password_verify($contrasena, $user['contrasena'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['usuario'] = $user['usuario'];
            $_SESSION['rol'] = $user['rol'];

            if ($user['rol'] === 'administrador') {
                header("Location: /public/index.php?action=admin_panel");
            } else {
                header("Location: /public/index.php?action=mis_prestamos");
            }
            exit;
        } else {
            $error = "Credenciales inválidas.";
            include '../views/auth/login.php';
            return;
        }
    }

    public function logout() {
        session_destroy();
        header("Location: /public/index.php?action=login");
        exit;
    }
}
?>