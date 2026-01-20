<?php
// JSON storage helpers (simple local "database" for demo)

function data_path(string $filename): string {
  return __DIR__ . '/../data/' . $filename;
}

function read_json(string $filename, array $default = []): array {
  $path = data_path($filename);
  if (!file_exists($path)) return $default;
  $raw = file_get_contents($path);
  $data = json_decode($raw, true);
  return is_array($data) ? $data : $default;
}

function write_json(string $filename, array $data): void {
  $path = data_path($filename);
  if (!is_dir(dirname($path))) {
    mkdir(dirname($path), 0775, true);
  }
  file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

function uuid(): string {
  try {
    return bin2hex(random_bytes(8));
  } catch (Throwable $e) {
    return uniqid('id_', true);
  }
}

function now_iso(): string {
  return date('c');
}
