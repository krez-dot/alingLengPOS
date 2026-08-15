<?php

declare(strict_types=1);

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function redirect(string $path): void
{
    header("Location: {$path}");
    exit;
}

function old(array $data, string $key, string $default = ''): string
{
    return htmlspecialchars((string) ($data[$key] ?? $default));
}
