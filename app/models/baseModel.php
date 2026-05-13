<?php

declare(strict_types=1);

namespace App\Models;

use App\Database\DatabaseConnection;
use PDO;

abstract class BaseModel
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = DatabaseConnection::get();
    }
}
