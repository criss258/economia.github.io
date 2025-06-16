CREATE DATABASE IF NOT EXISTS economia;

USE economia;

CREATE TABLE registros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('ingreso', 'gasto') NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    monto DECIMAL(10, 2) NOT NULL,
    fecha DATE NOT NULL
);

CREATE TABLE estadisticas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('ingreso', 'gasto') NOT NULL,
    periodo ENUM('dia', 'mes', 'anio') NOT NULL,
    monto_total DECIMAL(10, 2) NOT NULL,
    fecha DATE NOT NULL
);

CREATE TABLE planes_ahorro (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    monto_total DECIMAL(10, 2) NOT NULL,
    progreso DECIMAL(10, 2) DEFAULT 0,
    estado ENUM('activo', 'completado') DEFAULT 'activo',
    frecuencia ENUM('diario', 'semanal', 'mensual') NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL
);

CREATE TABLE eventos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descripcion TEXT,
    fecha DATE NOT NULL,
    color VARCHAR(7) DEFAULT '#007bff'
);

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) UNIQUE NOT NULL,
    contrasena VARCHAR(255) NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS configuraciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pagina VARCHAR(100) UNIQUE NOT NULL,
    fondo TEXT NOT NULL
);
