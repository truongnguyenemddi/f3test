<?php
namespace App\Models;

use DB\SQL\Mapper;

class RoleGroup extends Mapper {
    public function __construct(\DB\SQL $db) {
        parent::__construct($db, 'role_group');
    }

    /**
     * Returns rows with list_role for a user in an office.
     */
    public function findRoleRowsForUserInOffice(string $userId, string $officeId): array {
        $needle = '%|' . $userId . '|%';
        return $this->find(['list_user LIKE ? AND office_id = ?', $needle, $officeId]);
    }

    /**
     * Returns rows with list_module for a user in an office.
     */
    public function findModuleRowsForUserInOffice(string $userId, string $officeId): array {
        $needle = '%|' . $userId . '|%';
        return $this->find(['list_user LIKE ? AND office_id = ?', $needle, $officeId]);
    }
}

