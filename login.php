<?php

if (!isset($_SESSION)) {
    session_start();
}
include("db.php");
$id = $_POST["id"];
$passwd = $_POST["passwd"];
is_passwd_correct($id, $passwd);

?>
