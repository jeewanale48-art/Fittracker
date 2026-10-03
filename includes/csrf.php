<?php
function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function verifyCsrfToken(): bool {
    $submitted = $_POST['csrf_token'] ?? '';
    $stored = $_SESSION['csrf_token'] ?? '';
    return is_string($submitted) && is_string($stored)
        && $stored !== '' && hash_equals($stored, $submitted);
}
function requireValidCsrf(): void {
    if (!verifyCsrfToken()) {
        http_response_code(403);
        exit('Invalid or expired security token. Go back and try again.');
    }
}
