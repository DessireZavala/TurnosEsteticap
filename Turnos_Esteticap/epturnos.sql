-- Crear la base de datos
CREATE DATABASE epturnos;
USE epturnos;

-- Crear la tabla "roles"
CREATE TABLE roles (
    id_rol INT(1) PRIMARY KEY AUTO_INCREMENT,
    tipo VARCHAR(15) NOT NULL
);

-- Crear la tabla "usuarios"
CREATE TABLE usuarios (
    id_usuario INT(4) PRIMARY KEY AUTO_INCREMENT,
    nombrep VARCHAR(15) NOT NULL,
    usuario VARCHAR(20) UNIQUE NOT NULL,
    password VARCHAR(64) NOT NULL,
    id_rol INT(1) NOT NULL,
    FOREIGN KEY (id_rol) REFERENCES roles(id_rol)
);

-- Crear la tabla "clientes"
CREATE TABLE clientes (
    id_cliente INT(4) PRIMARY KEY AUTO_INCREMENT,
    nombre_clientes VARCHAR(25) NOT NULL,
    ap_clientes VARCHAR(15) NOT NULL,
    am_clientes VARCHAR(15) NOT NULL
);

-- Crear la tabla "servicios"
CREATE TABLE servicios (
    id_servicio INT(4) PRIMARY KEY AUTO_INCREMENT,
    nombre_serv VARCHAR(25) NOT NULL UNIQUE,
    costo DECIMAL(6,2) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1 -- 1 para activo, 2 para inactivo, 0 para eliminado (se elimina desde php)

);

-- Crear la tabla "turno"
CREATE TABLE turno (
    id_turno INT PRIMARY KEY AUTO_INCREMENT,
    num_turno INT NOT NULL,
    id_rep INT NOT NULL, -- ID del recepcionista
    fecha_turno DATE NOT NULL,
    estado INT(1) NOT NULL DEFAULT 1, -- Estado: 1 = en espera, 2 = no atendido, 3 = atendido
    FOREIGN KEY (id_rep) REFERENCES usuarios(id_usuario)
);

-- Crear la tabla "ventas" (tabla intermedia entre servicios y clientes)
CREATE TABLE ventas (
    id_turno INT(4) NOT NULL,
    id_cliente INT(4) NOT NULL,
    id_servicio INT(4) NOT NULL,
    id_usuario INT(4) NOT NULL,
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_cliente, id_servicio, id_turno),
    FOREIGN KEY (id_turno) REFERENCES turno(id_turno),
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente),
    FOREIGN KEY (id_servicio) REFERENCES servicios(id_servicio),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

-- Crear la tabla "estado_turnos" (para registrar el último turno asignado cada día)
CREATE TABLE estado_turnos (
    id INT PRIMARY KEY,
    fecha_ultimo_turno DATE NOT NULL
);

-- Insertar el estado inicial en estado_turnos
INSERT INTO estado_turnos (id, fecha_ultimo_turno) VALUES (1, CURDATE());

-- Cambiar el delimitador a // para poder crear el trigger
DELIMITER //

CREATE TRIGGER reiniciar_turnos
BEFORE INSERT ON turno
FOR EACH ROW
BEGIN
    DECLARE ultimo_turno INT;

    -- Verifica si el día actual es diferente al registrado en estado_turnos
    IF (SELECT fecha_ultimo_turno FROM estado_turnos WHERE id = 1) != CURDATE() THEN
        -- Si es un nuevo día, reinicia el turno a 1
        SET NEW.num_turno = 1;

        -- Actualiza la tabla estado_turnos con la fecha actual
        UPDATE estado_turnos SET fecha_ultimo_turno = CURDATE() WHERE id = 1;
    ELSE
        -- Si es el mismo día, incrementa el turno según el máximo turno registrado
        SET NEW.num_turno = (SELECT COALESCE(MAX(num_turno), 0) FROM turno WHERE fecha_turno = CURDATE()) + 1;
    END IF;
END;
//

DELIMITER ;

-- Insertar roles en la tabla "roles"
INSERT INTO roles (tipo) 
VALUES ('superusuario'), ('recepcionista');

-- Insertar usuarios en la tabla "usuarios"
INSERT INTO usuarios (nombrep, usuario, password, id_rol)
VALUES ('Fernanda', 'superuser', SHA2('superu123', 256), 1),
       ('Amanda', 'recep1', SHA2('recep123', 256), 2);

-- Insertar servicios en la tabla "servicios"
INSERT INTO servicios (nombre_serv, costo)
VALUES ('Peinado', 300.00),
       ('Maquillaje', 600.00),
       ('Corte de Dama', 150.00),
       ('Corte de Caballero', 120.00),
       ('Depilacion', 100.00),
       ('Tinte', 500.00),
       ('Efecto de color', 1000.00);
