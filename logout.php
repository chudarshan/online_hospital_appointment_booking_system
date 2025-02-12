<?php
session_start();
session_destroy();
header("Location: adlogin.html");
exit();
?>
