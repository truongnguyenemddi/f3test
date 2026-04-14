<?php
namespace App\Models\Users;

use DB\SQL\Mapper;

class User extends Mapper {
    public function __construct(\DB\SQL $db) {
        // Giả sử bạn có bảng 'users'
        // parent::__construct($db, 'users');
    }

    public function getAll() {
        // return $this->find();
        return ["Sample User 1", "Sample User 2"];
    }
}