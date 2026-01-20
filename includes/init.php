<?php
// Basic init + safe defaults
if (!isset($page_title)) { $page_title = 'P&J Tenarte Dental Clinic'; }
if (!isset($active)) { $active = ''; }

// ------------------------------------------------------------
// Base URL (IMPORTANT)
// ------------------------------------------------------------
// Expected setup:
//   htdocs/ProjectSAAD/
// So the website root is:
//   http://localhost/ProjectSAAD/
// If you rename the folder, update BASE_URL below.
if (!defined('BASE_URL')) {
  define('BASE_URL', '/ProjectSAAD');
}

function url_path(string $path = ''): string {
  $path = ltrim($path, '/');
  return rtrim(BASE_URL, '/') . '/' . $path;
}

function asset_path(string $path): string {
  return url_path('assets/' . ltrim($path, '/'));
}

// Helpers
function is_active(string $key, string $active): string {
  return $key === $active
    ? 'text-brand-blue font-semibold'
    : 'text-gray-600 hover:text-brand-blue font-medium transition';
}

