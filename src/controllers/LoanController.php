<?php
class LoanController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function calcularPrestamo($input) {
        $monto = (float)($input['monto'] ?? 0);
        $cuotas = (int)($input['cuotas'] ?? 0);
        $tasa = (float)($input['tasa_mensual'] ?? 0);
        $modalidad = $input['modalidad'] ?? 'cuota_fija';
        $fecha_inicio = $input['fecha_primer_pago'] ?? date('Y-m-d');

        if ($monto <= 0 || $cuotas <= 0 || $tasa < 0) {
            die("Datos de préstamo inválidos.");
        }

        $r = $tasa / 100;
        $amortizacion = [];
        $balance = $monto;
        $fecha = new DateTime($fecha_inicio);

        for ($i = 1; $i <= $cuotas; $i++) {
            if ($i == 1) {
                // Primera fecha: tal como se ingresa
            } else {
                // Ir al primer día del mes siguiente, luego al último
                $fecha->modify('first day of next month');
                $fecha->modify('last day of this month');
            }

            switch ($modalidad) {
                case 'cuota_fija':
                    if ($i == 1) {
                        if ($r == 0) {
                            $cuota = $monto / $cuotas;
                        } else {
                            $cuota = $monto * ($r * pow(1 + $r, $cuotas)) / (pow(1 + $r, $cuotas) - 1);
                        }
                    }
                    $interes = $balance * $r;
                    $capital = $cuota - $interes;
                    if ($i == $cuotas) {
                        $capital = $balance;
                        $cuota = $capital + $interes;
                    }
                    $balance -= $capital;
                    break;

                case 'cuota_disminuyente':
                    $capital = $monto / $cuotas;
                    $interes = $balance * $r;
                    $cuota = $capital + $interes;
                    $balance -= $capital;
                    break;

                case 'interes_fijo':
                    $capital = $monto / $cuotas;
                    $interes = $monto * $r;
                    $cuota = $capital + $interes;
                    $balance = $monto - ($capital * $i);
                    break;

                case 'capital_al_final':
                    $capital = ($i == $cuotas) ? $monto : 0;
                    $interes = $monto * $r;
                    $cuota = $capital + $interes;
                    $balance = ($i == $cuotas) ? 0 : $monto;
                    break;

                default:
                    die("Modalidad no soportada.");
            }

            $amortizacion[] = [
                'numero_cuota' => $i,
                'fecha' => $fecha->format('Y-m-d'),
                'capital' => round($capital, 2),
                'interes' => round($interes, 2),
                'balance' => round(max(0, $balance), 2),
                'total_pagar' => round($cuota, 2)
            ];
        }

        return $amortizacion;
    }

    public function getAllClients() {
        $stmt = $this->pdo->query("SELECT id, nombre_completo, usuario FROM usuarios WHERE rol = 'cliente'");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllLoansWithClientName() {
        $sql = "
            SELECT p.*, u.nombre_completo AS cliente_nombre
            FROM prestamos p
            JOIN usuarios u ON p.cliente_id = u.id
            ORDER BY p.created_at DESC
        ";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLoansByUser($usuario_id) {
        $sql = "
            SELECT p.*, u.nombre_completo AS cliente_nombre
            FROM prestamos p
            JOIN usuarios u ON p.cliente_id = u.id
            WHERE p.cliente_id = ?
            ORDER BY p.created_at DESC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$usuario_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLoanById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM prestamos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ✅ CORREGIDO: sin session_start()
    public function createLoanAsAdmin() {
        // La sesión ya está activa (iniciada en index.php)
        if (!isset($_SESSION['user_id']) || ($_SESSION['rol'] ?? '') !== 'administrador') {
            die("Acceso denegado.");
        }

        $input = $_POST;
        $amortizacion = $this->calcularPrestamo($input);

        $stmt = $this->pdo->prepare("
            INSERT INTO prestamos 
            (cliente_id, creado_por, monto, cuotas, tasa_mensual, modalidad, fecha_primer_pago)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $input['cliente_id'],
            $_SESSION['user_id'],
            $input['monto'],
            $input['cuotas'],
            $input['tasa_mensual'],
            $input['modalidad'],
            $input['fecha_primer_pago']
        ]);
        $prestamo_id = $this->pdo->lastInsertId();

        $stmtDet = $this->pdo->prepare("
            INSERT INTO amortizacion_detalle 
            (prestamo_id, numero_cuota, fecha, capital, interes, balance, total_pagar)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        foreach ($amortizacion as $fila) {
            $stmtDet->execute([
                $prestamo_id,
                $fila['numero_cuota'],
                $fila['fecha'],
                $fila['capital'],
                $fila['interes'],
                $fila['balance'],
                $fila['total_pagar']
            ]);
        }

        header("Location: /public/index.php?action=admin_panel&msg=created");
        exit;
    }

    public function updateLoan($data) {
        $stmt = $this->pdo->prepare("
            UPDATE prestamos 
            SET cliente_id = ?, monto = ?, cuotas = ?, tasa_mensual = ?, 
                modalidad = ?, fecha_primer_pago = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $data['cliente_id'],
            $data['monto'],
            $data['cuotas'],
            $data['tasa_mensual'],
            $data['modalidad'],
            $data['fecha_primer_pago'],
            $data['id']
        ]);

        $input = [
            'monto' => (float)$data['monto'],
            'cuotas' => (int)$data['cuotas'],
            'tasa_mensual' => (float)$data['tasa_mensual'],
            'modalidad' => $data['modalidad'],
            'fecha_primer_pago' => $data['fecha_primer_pago']
        ];
        $amortizacion = $this->calcularPrestamo($input);

        $this->pdo->prepare("DELETE FROM amortizacion_detalle WHERE prestamo_id = ?")
                  ->execute([$data['id']]);

        $stmtDet = $this->pdo->prepare("
            INSERT INTO amortizacion_detalle 
            (prestamo_id, numero_cuota, fecha, capital, interes, balance, total_pagar)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        foreach ($amortizacion as $fila) {
            $stmtDet->execute([
                $data['id'],
                $fila['numero_cuota'],
                $fila['fecha'],
                $fila['capital'],
                $fila['interes'],
                $fila['balance'],
                $fila['total_pagar']
            ]);
        }

        header("Location: /public/index.php?action=admin_panel&msg=updated");
        exit;
    }

    public function deleteLoan($id) {
        $this->pdo->prepare("DELETE FROM prestamos WHERE id = ?")->execute([$id]);
        header("Location: /public/index.php?action=admin_panel&msg=deleted");
        exit;
    }
}
?>