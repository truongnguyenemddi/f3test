<?php
namespace App\Services\Auth;

use App\Models\Office;
use App\Models\RoleGroup;
use DB\SQL;

class AuthzService {
    public static function normalizePipeList(string $s): string {
        $s = str_replace('||', '|', $s);
        if ($s === '') return '|';
        if ($s[0] !== '|') $s = '|' . $s;
        if (substr($s, -1) !== '|') $s .= '|';
        return preg_replace('/\\|+/', '|', $s) ?: '|';
    }

    public static function hasInPipeList(string $id, string $pipeList): bool {
        $id = trim($id);
        if ($id === '') return false;
        return strpos($pipeList, '|' . $id . '|') !== false;
    }

    public static function computePermission(SQL $db, string $userId, string $unitId): string {
        $office = new Office($db);
        $universe = self::normalizePipeList($office->getRoleUniverse($unitId));

        $roleGroup = new RoleGroup($db);
        $rows = $roleGroup->findRoleRowsForUserInOffice($userId, $unitId);

        $out = '|';
        foreach ($rows as $row) {
            $listRole = (string)($row['list_role'] ?? '');
            foreach (explode('|', $listRole) as $roleId) {
                $roleId = trim($roleId);
                if ($roleId === '') continue;
                if (self::hasInPipeList($roleId, $universe)) $out .= $roleId . '|';
            }
        }

        return self::normalizePipeList($out);
    }

    public static function computeModules(SQL $db, string $userId, string $unitId): string {
        $roleGroup = new RoleGroup($db);
        $rows = $roleGroup->findModuleRowsForUserInOffice($userId, $unitId);

        $out = '|';
        foreach ($rows as $row) {
            $out .= (string)($row['list_module'] ?? '');
        }
        return self::normalizePipeList($out);
    }

    public static function officeType1Info(SQL $db, string $unitId): array {
        $office = new Office($db);
        return $office->getType1Info($unitId);
    }
}

