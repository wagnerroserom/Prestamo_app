DROP TABLE IF EXISTS amortizacion_detalle;
DROP TABLE IF EXISTS prestamos;
DROP TABLE IF EXISTS usuarios;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    pais VARCHAR(50),
    celular VARCHAR(20),
    rol ENUM('cliente', 'administrador') DEFAULT 'cliente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO usuarios (nombre_completo, correo, usuario, contrasena, rol)
VALUES (
    'Administrador Principal',
    'admin@prestamistapp.local',
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'administrador'
);

CREATE TABLE prestamos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    creado_por INT NOT NULL,
    monto DECIMAL(12,2) NOT NULL,
    cuotas INT NOT NULL,
    tasa_mensual DECIMAL(5,2) NOT NULL,
    modalidad ENUM('cuota_fija', 'cuota_disminuyente', 'interes_fijo', 'capital_al_final') NOT NULL,
    fecha_primer_pago DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (creado_por) REFERENCES usuarios(id)
);

CREATE TABLE amortizacion_detalle (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prestamo_id INT NOT NULL,
    numero_cuota INT NOT NULL,
    fecha DATE NOT NULL,
    capital DECIMAL(12,2) NOT NULL,
    interes DECIMAL(12,2) NOT NULL,
    balance DECIMAL(12,2) NOT NULL,
    total_pagar DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (prestamo_id) REFERENCES prestamos(id) ON DELETE CASCADE
);