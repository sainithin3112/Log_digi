<?php
    session_start();
require('includes/functions.php');

    unset($_SESSION['LOGI_EMP_ID']);
    unset($_SESSION['LOGI_EMP_NAME']);
    unset($_SESSION['LOGI_EMP_EMAIL']);
    unset($_SESSION['LOGI_USER_ROLE_ID']);
    unset($_SESSION['LOGI_USER_ROLE_NAME']);
    echo "<script>window.location.href='index.php'</script>";
    die();
?>