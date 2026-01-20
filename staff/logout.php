<?php
require_once __DIR__ . '/../includes/auth.php';
logout_user();
header('Location: ' . url_path('index.php'));
exit;
