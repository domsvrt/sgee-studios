<?php

declare(strict_types=1);

namespace App\Controllers;

abstract class BaseController
{
    protected function render(string $viewPath, array $data = []): void
    {
        $e = [$this, 'escape'];
        extract($data, EXTR_SKIP);
        require $this->viewPath($viewPath);
    }

    protected function renderWithLayout(string $layoutPath, string $viewPath, array $data = []): void
    {
        $flash = $this->pullFlash();
        $content = $this->capture($viewPath, $data);
        $e = [$this, 'escape'];
        extract($data, EXTR_SKIP);
        require $this->viewPath($layoutPath);
    }

    protected function redirect(string $path): never
    {
        header('Location: ' . $path);
        exit;
    }

    protected function pullFlash(): ?array
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        return is_array($flash) ? $flash : null;
    }

    protected function requireRole(array $roles, string $redirectPath = '/sign-in'): void
    {
        $role = $_SESSION['user_role'] ?? null;
        if (!is_string($role) || !in_array($role, $roles, true)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Please sign in with an authorized account.'];
            $this->redirect($redirectPath);
        }
    }

    private function capture(string $viewPath, array $data): string
    {
        ob_start();
        $this->render($viewPath, $data);
        return (string) ob_get_clean();
    }

    private function viewPath(string $viewPath): string
    {
        return __DIR__ . '/../views/' . ltrim($viewPath, '/');
    }

    private function escape($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}
