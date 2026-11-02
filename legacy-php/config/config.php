<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
const APP_NAME = 'e-Linx';
const APP_TAGLINE = 'Digital Engineering & Technology';
const BASE_URL = '/e-linx'; // change only if your folder name differs
const DB_HOST = '127.0.0.1';
const DB_NAME = 'elinx_db';
const DB_USER = 'root';
const DB_PASS = '';

date_default_timezone_set('Asia/Karachi');

function db(): mysqli {
    static $db = null;
    if ($db instanceof mysqli) return $db;
    $db = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($db->connect_errno) {
        die('<h2 style="font-family:Arial">Database connection failed</h2><p>Create <b>elinx_db</b> in phpMyAdmin and import <b>database/elinx_db.sql</b>.</p>');
    }
    $db->set_charset('utf8mb4');
    return $db;
}

function e(?string $value): string { return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8'); }
function url(string $path = ''): string { return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/'); }
function redirect(string $path): never { header('Location: ' . url($path)); exit; }
function is_post(): bool { return $_SERVER['REQUEST_METHOD'] === 'POST'; }
function csrf_token(): string { if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function verify_csrf(): void { if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(419); exit('Invalid CSRF token.'); } }
function flash(string $key, ?string $value = null): ?string { if ($value !== null) { $_SESSION['flash'][$key] = $value; return null; } $v = $_SESSION['flash'][$key] ?? null; unset($_SESSION['flash'][$key]); return $v; }
function admin(): bool { return !empty($_SESSION['admin_id']); }
function require_admin(): void { if (!admin()) redirect('admin/login.php'); }
function old(string $key): string { return e($_POST[$key] ?? ''); }
function rows(string $sql): array { $r = db()->query($sql); return $r ? $r->fetch_all(MYSQLI_ASSOC) : []; }
function row(string $sql): ?array { $r = db()->query($sql); return $r ? ($r->fetch_assoc() ?: null) : null; }
function clean_slug(string $s): string { $s = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $s), '-')); return $s ?: 'item-' . time(); }
