DROP DATABASE IF EXISTS UO298184_DB;
CREATE DATABASE UO298184_DB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE UO298184_DB;

CREATE TABLE PericiaInformatica (
    idPericia INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nivelPericia VARCHAR(50) NOT NULL UNIQUE
);

INSERT INTO PericiaInformatica (nivelPericia) VALUES
('Principiante'),
('Intermedio'),
('Avanzado'),
('Experto');

CREATE TABLE Usuario (
    idUsuario VARCHAR(50) NOT NULL PRIMARY KEY,
    profesion VARCHAR(100) NOT NULL,
    edad INT NOT NULL CHECK (edad >= 0),
    genero ENUM('Masculino', 'Femenino', 'Otro', 'Prefiero no decirlo') NOT NULL,
    idPericia INT NOT NULL, 
    FOREIGN KEY (idPericia) REFERENCES PericiaInformatica(idPericia)
);

CREATE TABLE Prueba (
    idPrueba INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    idUsuario VARCHAR(50) NOT NULL,
    FOREIGN KEY (idUsuario) REFERENCES Usuario(idUsuario),
    dispositivo ENUM('Ordenador', 'Tableta', 'Teléfono') NOT NULL,
    tiempoTotal FLOAT NOT NULL CHECK (tiempoTotal >= 0),
    tareaCompletada BOOLEAN NOT NULL,
    comentariosUsuario TEXT,
    propuestasMejora TEXT, 
    valoracion INT NOT NULL CHECK (valoracion BETWEEN 0 AND 10)
);

CREATE TABLE Observacion (
    idObservacion INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    idPrueba INT NOT NULL,
    FOREIGN KEY (idPrueba) REFERENCES Prueba(idPrueba),
    comentariosFacilitador TEXT NOT NULL
);