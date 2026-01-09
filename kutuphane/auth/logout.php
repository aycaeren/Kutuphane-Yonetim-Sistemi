<?php
session_start();
session_unset();
session_destroy();

/* ÇIKIŞ → INDEX */
header("Location: ../index.php");
exit;
