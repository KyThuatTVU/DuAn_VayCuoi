<?php
// Admin Header Component
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit();
}
?>
