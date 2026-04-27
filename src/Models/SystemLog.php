<?php
namespace App\Models;

use DB\SQL\Mapper;

class SystemLog extends Mapper {
    public function __construct(\DB\SQL $db) {
        parent::__construct($db, 'system_log');
    }

    public function insertLoginLog(array $data): void {
        $this->reset();
        $this->user_id = (int)($data['user_id'] ?? 0);
        $this->office_id = (string)($data['office_id'] ?? '');
        $this->content = (string)($data['content'] ?? '');
        $this->client_ip = (string)($data['client_ip'] ?? '');
        $this->browser_name = (string)($data['browser_name'] ?? '');
        $this->user_name = (string)($data['user_name'] ?? '');
        $this->full_name = (string)($data['full_name'] ?? '');
        $this->save();
    }
}

