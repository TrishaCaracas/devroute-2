<?php
include "helpers/session.php";

if (array_key_exists("logout", $_GET)) {
    session_destroy();
    header("Location: signin");
    exit;
}
?>