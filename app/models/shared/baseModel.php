<?php

declare(strict_types=1);

namespace App\Models\Shared;

use App\Database\DatabaseConnection;
use PDO;

abstract class BaseModel
{
    protected PDO $db;
    private static ?bool $usersSplitNameColumns = null;

    public function __construct()
    {
        $this->db = DatabaseConnection::get();
    }

    protected function hasUsersSplitNameColumns(): bool
    {
        if (self::$usersSplitNameColumns !== null) {
            return self::$usersSplitNameColumns;
        }

        $stmt = $this->db->query("SHOW COLUMNS FROM users LIKE 'first_name'");
        self::$usersSplitNameColumns = (bool) $stmt->fetch();
        return self::$usersSplitNameColumns;
    }
}
