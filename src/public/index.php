<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/LoanController.php';

$action = $_GET['action'] ?? 'home';

if (!isset($_SESSION['user_id']) && !in_array($action, ['login', 'register', 'login_process'])) {
    header("Location: /public/index.php?action=login");
    exit;
}

$auth = new AuthController($pdo);
$loanCtrl = new LoanController($pdo);

switch ($action) {
    case 'login':
        include '../views/auth/login.php';
        break;

    case 'register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth->register();
        } else {
            $auth->showRegister();
        }
        break;

    case 'login_process':
        $auth->loginProcess();
        break;

    case 'logout':
        $auth->logout();
        break;

    case 'admin_panel':
        if ($_SESSION['rol'] !== 'administrador') {
            header("Location: /public/index.php?action=mis_prestamos");
            exit;
        }
        $clientes = $loanCtrl->getAllClients();
        $todos_prestamos = $loanCtrl->getAllLoansWithClientName();
        include '../views/admin/panel.php';
        break;

    case 'create_loan':
        $loanCtrl->createLoanAsAdmin();
        break;

    case 'edit_loan_form':
        if ($_SESSION['rol'] !== 'administrador') exit('Acceso denegado');
        $prestamo = $loanCtrl->getLoanById($_GET['id']);
        if (!$prestamo) die("Préstamo no encontrado.");
        $clientes = $loanCtrl->getAllClients();
        include '../views/admin/edit_loan.php';
        break;

    case 'update_loan':
        $loanCtrl->updateLoan($_POST);
        break;

    case 'delete_loan':
        $loanCtrl->deleteLoan($_GET['id']);
        break;

    case 'mis_prestamos':
        $prestamos = $loanCtrl->getLoansByUser($_SESSION['user_id']);
        include '../views/client/mis_prestamos.php';
        break;

    default:
        if ($_SESSION['rol'] === 'administrador') {
            header("Location: /public/index.php?action=admin_panel");
        } else {
            header("Location: /public/index.php?action=mis_prestamos");
        }
}
?>