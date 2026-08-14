<?php
require_once 'config.php';

// Hapus semua session
$_SESSION = [];
session_destroy();

redirect('index.php');
