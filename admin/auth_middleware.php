<?php
function checkAdminAuth() {
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        header("Location: ../auth/login.php");
        exit();
    }
}
?>