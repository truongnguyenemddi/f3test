<?php
namespace App\Models;

use DB\SQL\Mapper;

class User extends Mapper {
    public function __construct(\DB\SQL $db) {
        parent::__construct($db, 'user');
    }

    public function findByIdentity(string $identity): ?self {
        $identity = trim($identity);
        if ($identity === '')
            return null;

        $this->load(['user_name = ? OR email = ?', $identity, $identity]);
        if ($this->dry())
            return null;

        return $this;
    }

    public function verifyPassword(string $plainPassword): bool {
        $hashOrPlain = (string) ($this->password ?? '');
        if ($hashOrPlain === '')
            return false;

        // Prefer modern hashes (bcrypt/argon2/etc.)
        if (password_verify($plainPassword, $hashOrPlain))
            return true;

        // Fallback for legacy hashes (e.g., MD5, SHA1)
        if (hash_equals($hashOrPlain, md5($plainPassword)))
            return true;
        if (hash_equals($hashOrPlain, sha1($plainPassword)))
            return true;

        // Fallback for legacy plaintext passwords (can be removed once migrated)
        return hash_equals($hashOrPlain, $plainPassword);
    }
}