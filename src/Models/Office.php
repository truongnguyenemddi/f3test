<?php
namespace App\Models;

use DB\SQL\Mapper;

class Office extends Mapper {
    public function __construct(\DB\SQL $db) {
        parent::__construct($db, 'office');
    }

    public function getRoleUniverse(string $officeId): string {
        $this->load(['office_id = ?', $officeId]);
        return (string)($this->list_role ?? '');
    }

    public function getType1Info(string $officeId): array {
        $this->load(['office_id = ? AND type = 1', $officeId]);
        if ($this->dry()) {
            return ['listRole' => '', 'officeName' => ''];
        }

        return [
            'listRole' => (string)($this->list_role ?? ''),
            'officeName' => (string)($this->name ?? ''),
        ];
    }
}

