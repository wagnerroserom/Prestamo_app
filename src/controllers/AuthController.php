<?php
class AuthController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

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
            echo "<p style='color:red'>Todos los campos son obligatorios.</p>";
            $this->showRegister();
            return;
        }

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE usuario = ? OR correo = ?");
        $stmt->execute([$usuario, $correo]);
        if ($stmt->fetchColumn() > 0) {
            echo "<p style='color:red'>Usuario o correo ya registrado.</p>";
            $this->showRegister();
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
            echo "<p style='color:red'>Error al registrar. Intente nuevamente.</p>";
            $this->showRegister();
        }
    }

    public function loginProcess() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        
        $usuario = $_POST['usuario'] ?? '';
        $contrasena = $_POST['contrasena'] ?? '';

        if (empty($usuario) || empty($contrasena)) {
            echo "<p style='color:red'>Complete todos los campos.</p>";
            include '../views/auth/login.php';
            return;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE usuario = ?");
        $stmt->execute([$usuario]);
        $user = $stmt->fetch();

        if ($user && password_verify($contrasena, $user['contrasena'])) {
            session_start();
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
            echo "<p style='color:red'>Credenciales inválidas.</p>";
            include '../views/auth/login.php';
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        header("Location: /public/index.php?action=login");
        exit;
    }
}
?>