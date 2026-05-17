<?php

declare(strict_types=1);

namespace App\Controllers\Shared;

use Throwable;

trait AdminGuardTrait
{
    private function currentAdminRole(): string
    {
        $role = $_SESSION['user_role'] ?? '';
        return is_string($role) ? $role : '';
    }

    private function canManageProtectedEntries(): bool
    {
        return $this->currentAdminRole() === 'manager';
    }

    private function assertCanManageProtectedEntries(): void
    {
        if (!$this->canManageProtectedEntries()) {
            throw new \RuntimeException('Only manager accounts can modify user and booking entries.');
        }
    }

    private function renderAdmin(string $view, array $data): void
    {
        $this->ensureAdminAccess();
        $data['canManageProtectedEntries'] = $this->canManageProtectedEntries();
        $this->renderWithLayout('admin/layout.php', "admin/{$view}.php", $data);
    }

    private function handle(callable $action, string $redirect, string $success): void
    {
        $this->ensureAdminAccess();

        try {
            $action();
            $_SESSION['flash'] = ['type' => 'success', 'message' => $success];
        } catch (Throwable $exception) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => $exception->getMessage()];
        }

        $this->redirect($redirect);
    }

    private function ensureAdminAccess(): void
    {
        $this->requireRole(['admin', 'manager']);
    }
}
