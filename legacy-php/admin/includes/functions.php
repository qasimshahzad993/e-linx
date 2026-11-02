<?php
// Shared helpers for every admin page
if (session_status() === PHP_SESSION_NONE) session_start();

function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function redirect($url) { header("Location: $url"); exit; }

// Toast messages (shown after redirect)
function flash($type, $msg) { $_SESSION['flash'][] = ['type' => $type, 'msg' => $msg]; }
function get_flashes() { $f = $_SESSION['flash'] ?? []; unset($_SESSION['flash']); return $f; }

// CSRF protection
function csrf_token() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_field() { return '<input type="hidden" name="csrf" value="' . csrf_token() . '">'; }
function csrf_check() {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        flash('error', 'Your session expired. Please try again.');
        redirect($_SERVER['REQUEST_URI']);
    }
}

// Login guard. Change 'admin_id' if your login uses another session key.
function require_admin() {
    if (empty($_SESSION['admin_id'])) redirect('login.php');
}
function initials($name) {
    $p = preg_split('/\s+/', trim($name));
    return strtoupper(substr($p[0] ?? 'A', 0, 1) . substr($p[1] ?? '', 0, 1));
}
