<?php
require_once 'api/db.php';

// Administradores
$sql = "INSERT INTO USUARIOS VALUES (NULL, ?, ?, ?, ?, ?, ?, NOW(), NOW(), 'SI', NULL);";
$data = array('BRAYAM VALERO', '20427843', 'BVALEROP@GMAIL.COM', '3114916358', password_hash('123456', PASSWORD_DEFAULT), 'ADMINISTRADOR');

db_query($sql, $data);

// $sql = "INSERT INTO USUARIOS VALUES (NULL, ?, ?, ?, ?, ?, ?, NOW(), NOW(), 'SI', NULL);";
// $data = array('JAIME ROMERO', '123456', 'JAIMEROMERO@GMAIL.COM', '123456', password_hash('123456', PASSWORD_DEFAULT), 'ADMINISTRADOR');

// db_query($sql, $data);

// // Operarios
// $sql = "INSERT INTO USUARIOS VALUES (NULL, ?, ?, ?, ?, ?, ?, NOW(), NOW(), 'SI', NULL);";
// $data = array('OPERARIO 1', '11223344', 'OPERARIO1@GMAIL.COM', '123456', password_hash('123456', PASSWORD_DEFAULT), 'OPERARIO');

// db_query($sql, $data);

// $sql = "INSERT INTO USUARIOS VALUES (NULL, ?, ?, ?, ?, ?, ?, NOW(), NOW(), 'SI', NULL);";
// $data = array('OPERARIO 2', '11223345', 'OPERARIO2@GMAIL.COM', '123456', password_hash('123456', PASSWORD_DEFAULT), 'OPERARIO');

// db_query($sql, $data);

// $sql = "INSERT INTO USUARIOS VALUES (NULL, ?, ?, ?, ?, ?, ?, NOW(), NOW(), 'SI', NULL);";
// $data = array('OPERARIO 3', '11223346', 'OPERARIO3@GMAIL.COM', '123456', password_hash('123456', PASSWORD_DEFAULT), 'OPERARIO');

// db_query($sql, $data);

// $sql = "INSERT INTO USUARIOS VALUES (NULL, ?, ?, ?, ?, ?, ?, NOW(), NOW(), 'SI', NULL);";
// $data = array('OPERARIO 4', '66699955', 'OPERARIO4@GMAIL.COM', '123456', password_hash('123456', PASSWORD_DEFAULT), 'OPERARIO');

// db_query($sql, $data);

// // Ventas
// $sql = "INSERT INTO USUARIOS VALUES (NULL, ?, ?, ?, ?, ?, ?, NOW(), NOW(), 'SI', NULL);";
// $data = array('VENTAS', '45645456', 'VENTAS@GMAIL.COM', '123456', password_hash('123456', PASSWORD_DEFAULT), 'VENTAS');

// db_query($sql, $data);

// // Molinero
// $sql = "INSERT INTO USUARIOS VALUES (NULL, ?, ?, ?, ?, ?, ?, NOW(), NOW(), 'SI', NULL);";
// $data = array('MOLINERO', '34234423', 'MOLINERO@GMAIL.COM', '123456', password_hash('123456', PASSWORD_DEFAULT), 'MOLINERO');


// db_query($sql, $data);

// // Control de Calidad
// $sql = "INSERT INTO USUARIOS VALUES (NULL, ?, ?, ?, ?, ?, ?, NOW(), NOW(), 'SI', NULL);";
// $data = array('CONTROL DE CALIDAD', '66697', 'CONTROL@GMAIL.COM', '123456', password_hash('123456', PASSWORD_DEFAULT), 'CONTROL');

// db_query($sql, $data);

// // Produccion
// $sql = "INSERT INTO USUARIOS VALUES (NULL, ?, ?, ?, ?, ?, ?, NOW(), NOW(), 'SI', NULL);";
// $data = array('PRODUCCION', '213193123', 'PRODUCCION@GMAIL.COM', '123456', password_hash('123456', PASSWORD_DEFAULT), 'PRODUCCION');

// db_query($sql, $data);

// // Norsaplast
// $sql = "INSERT INTO USUARIOS VALUES (NULL, ?, ?, ?, ?, ?, ?, NOW(), NOW(), 'SI', NULL);";
// $data = array('NORSAPLAST', '68621517', 'NORSAPLAST@GMAIL.COM', '123456', password_hash('123456', PASSWORD_DEFAULT), 'NORSAPLAST');

// db_query($sql, $data);

// // Despachos
// $sql = "INSERT INTO USUARIOS VALUES (NULL, ?, ?, ?, ?, ?, ?, NOW(), NOW(), 'SI', NULL);";
// $data = array('DESPACHO', '682169512', 'DESPACHO@GMAIL.COM', '123456', password_hash('123456', PASSWORD_DEFAULT), 'DESPACHO');

// db_query($sql, $data);

// // Cliente
// $sql = "INSERT INTO USUARIOS VALUES (NULL, ?, ?, ?, ?, ?, ?, NOW(), NOW(), 'SI', NULL);";
// $data = array('CLIENTE', '6862115', 'CLIENTE@GMAIL.COM', '123456', password_hash('123456', PASSWORD_DEFAULT), 'CLIENTE');

// db_query($sql, $data);

// Se crea materia prima

// $sql = "INSERT INTO MATERIA_PRIMA VALUES (NULL, 'MEXICHEM', NULL, NULL, NULL, 1000);";
// db_query($sql);

// $sql = "INSERT INTO MATERIA_PRIMA VALUES (NULL, 'RECUPERADO EXPANSO', NULL, NULL, NULL, 1000);";
// db_query($sql);

// $sql = "INSERT INTO MATERIA_PRIMA VALUES (NULL, 'RECUPERADO PVC', NULL, NULL, NULL, 1000);";
// db_query($sql);

// $sql = "INSERT INTO MATERIA_PRIMA VALUES (NULL, 'NORSAPLAST', NULL, NULL, NULL, 1000);";
// db_query($sql);

// $sql = "INSERT INTO MATERIA_PRIMA VALUES (NULL, 'EXPANSEL', NULL, NULL, NULL, 1000);";
// db_query($sql);

// $sql = "INSERT INTO MATERIA_PRIMA VALUES (NULL, 'CHUPO', NULL, NULL, NULL, 1000);";
// db_query($sql);

// $sql = "INSERT INTO MATERIA_PRIMA VALUES (NULL, 'ESTAÑO', NULL, NULL, NULL, 1000);";
// db_query($sql);

// $sql = "INSERT INTO MATERIA_PRIMA VALUES (NULL, 'DIOXIDO', NULL, NULL, NULL, 1000);";
// db_query($sql);

// $sql = "INSERT INTO MATERIA_PRIMA VALUES (NULL, 'AMARILLO PURO', NULL, NULL, NULL, 1000);";
// db_query($sql);

// $sql = "INSERT INTO MATERIA_PRIMA VALUES (NULL, 'AZUL PURO', NULL, NULL, NULL, 1000);";
// db_query($sql);

// $sql = "INSERT INTO MATERIA_PRIMA VALUES (NULL, 'ROJO PURO', NULL, NULL, NULL, 1000);";
// db_query($sql);

// $sql = "INSERT INTO MATERIA_PRIMA VALUES (NULL, 'NEGRO PURO', NULL, NULL, NULL, 1000);";
// db_query($sql);


// Fin crear materia prima

// Se crean formula
// $sql = "INSERT INTO FORMULAS VALUES (NULL, 'FORMULA PARA BLANCO', 'EXPANSO', 'APROBADO');";
// db_query($sql);

// $sql = "INSERT INTO RECETAS VALUES (NULL, 1, 1);";
// db_query($sql);

// $sql = "INSERT INTO RECETAS VALUES (NULL, 1, 2);";
// db_query($sql);

// $sql = "INSERT INTO RECETAS VALUES (NULL, 1, 4);";
// db_query($sql);
// Fin crear formulas

header("Location: ../login.php");