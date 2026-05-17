<?php

declare(strict_types=1);

namespace App\Controllers\Shared;

use Throwable;

trait AdminGuardTrait
{
    private function canEditProtectedEntries(): bool
    {
        return true;
    }

    private function assertCanEditProtectedEntries(): void
    {
        if (!$this->canEditProtectedEntries()) {
            throw new \RuntimeException('Only admin accounts can edit existing user and booking entries.');
        }
    }

    private function renderAdmin(string $view, array $data): void
    {
        $this->ensureAdminAccess();
        $data['canEditProtectedEntries'] = $this->canEditProtectedEntries();
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
        $this->requireRole(['admin']);
    }
}
