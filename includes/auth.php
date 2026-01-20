<?php
require_once __DIR__ . '/init.php';
// Simple session-based auth for demo (no database)
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

function auth_user(): ?array {
  return $_SESSION['auth'] ?? null;
}

function login_user(string $role, string $name, string $username): void {
  $_SESSION['auth'] = [
    'role' => $role,
    'name' => $name,
    'username' => $username,
  ];
}

function logout_user(): void {
  $_SESSION = [];
  if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
      $params['path'], $params['domain'], $params['secure'], $params['httponly']
    );
  }
  session_destroy();
}

function require_role(string $role): void {
  $u = auth_user();
  if (!$u || ($u['role'] ?? '') !== $role) {
    header('Location: ' . url_path('staff/index.php'));
    exit;
  }
}
