<?php
require_once 'api/db.php';

// Administradores
$sql = "INSERT INTO USUARIOS VALUES (NULL, ?, ?, ?, ?, ?, ?, NOW(), NOW(), 'SI', NULL);";
$data = array('BRAYAM VALERO', '20427843', 'BVALEROP@GMAIL.COM', '3114916358', password_hash('123456', PASSWORD_DEFAULT), 'ADMINISTRADOR');

db_query($sql, $data);

header("Location: ../login.php");