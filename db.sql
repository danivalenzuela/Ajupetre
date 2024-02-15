CREATE DATABASE IF NOT EXISTS personas_db;

USE personas_db;

CREATE TABLE IF NOT EXISTS personas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_apellido VARCHAR(255) NOT NULL,
    dni VARCHAR(20) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    direccion VARCHAR(255) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL,
    fecha_afiliacion DATE NOT NULL
);

CREATE TABLE IF NOT EXISTS pagos_pendientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    persona_id INT NOT NULL,
    mes VARCHAR(20) NOT NULL,
    FOREIGN KEY (persona_id) REFERENCES personas(id)
);