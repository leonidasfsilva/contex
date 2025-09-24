<?php

$acaoForm    = empty($_GET["acaoForm"]) ?: $_GET["acaoForm"];
$action2     = !empty($_POST["action2"]) ? $_POST["action2"] : null;
$pk_cectb003 = empty($_GET["pk_cectb003"]) ?: $_GET["pk_cectb003"];

echo '<pre>';
var_dump($acaoForm, $action2, $pk_cectb003);
echo '</pre>';
exit();
