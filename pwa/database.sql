CREATE DATABASE IF NOT EXISTS pwa CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE pwa;

CREATE TABLE IF NOT EXISTS vijesti (
    id INT NOT NULL AUTO_INCREMENT,
    datum DATE NOT NULL,
    naslov VARCHAR(255) NOT NULL,
    sazetak TEXT NOT NULL,
    tekst TEXT NOT NULL,
    slika VARCHAR(255) NOT NULL,
    kategorija VARCHAR(50) NOT NULL,
    arhiva TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS korisnik (
    id INT NOT NULL AUTO_INCREMENT,
    ime VARCHAR(100) NOT NULL,
    prezime VARCHAR(100) NOT NULL,
    korisnicko_ime VARCHAR(100) NOT NULL,
    lozinka VARCHAR(255) NOT NULL,
    razina INT NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY korisnicko_ime (korisnicko_ime)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
