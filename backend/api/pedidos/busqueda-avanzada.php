<?php
session_start();
require_once "../db.php";
// echo '<pre>'; print_r($_POST); echo '</pre>';

// Si se ejecuta un Request, ya sea GET o POST se ejecuta el código.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $cliente_id = $_POST['cliente'];

    if(isset($cliente_id)){

        $sql = "SELECT ID FROM PEDIDOS WHERE CLIENTE_ID = ?";
        $pedidos = db_query($sql, array($cliente_id));

        foreach ($pedidos as $pedido) {

            $sql = "SELECT PRO.PEDIDO_ID, SUE.MARCA, SUE.TALLA, COL.COLOR, PRO.RESTANTE, PRO.ESTADO FROM PRODUCCION PRO
                        JOIN SUELAS SUE ON SUE.ID = PRO.SUELA_ID
                        JOIN COLOR COL ON COL.ID = PRO.COLOR_ID
                            WHERE PRO.PEDIDO_ID = ?";

            $result = db_query($sql, array($pedido['ID']));

            foreach ($result as $row) {

                $total_ped[] = array(
                    "PEDIDO_ID"     =>  $row['PEDIDO_ID'],
                    "MARCA"         =>  $row['MARCA'],
                    "TALLA"         =>  $row['TALLA'],
                    "COLOR"         =>  $row['COLOR'],
                    "RESTANTE"      =>  $row['RESTANTE'],
                    "ESTADO"        =>  $row['ESTADO']
                );

            }

        }
        
        echo json_encode($total_ped);

    } else {
        
        echo "FAIL";

    }

}