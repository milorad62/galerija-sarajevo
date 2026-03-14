<?php
// Sigurnosne helper funkcije: CSRF, rate limit, sanitizacija
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

function csrf_token(): string {
  if (empty($_SESSION['_csrf'])) {
    $_SESSION['_csrf'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['_csrf'];
}

function csrf_verify(?string $token): bool {
  return is_string($token) && !empty($_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'], $token);
}

// Jednostavan rate limit po formi (npr. kontakt)
function rate_limit(string $key, int $seconds): bool {
  $now = time();
  $k = '_rl_' . $key;
  if (!empty($_SESSION[$k]) && ($now - (int)$_SESSION[$k]) < $seconds) {
    return false;
  }
  $_SESSION[$k] = $now;
  return true;
}

function clean_text(string $s, int $maxLen = 2000): string {
  $s = trim($s);
  $s = preg_replace('/\s+/', ' ', $s);
  $s = strip_tags($s);
  if (mb_strlen($s) > $maxLen) $s = mb_substr($s, 0, $maxLen);
  return $s;
}
