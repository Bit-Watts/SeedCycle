<?php

require_once __DIR__ . '/../../config/DB.php';

class User {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function findByEmail(string $email): ?array {
        return DB::fetch($this->conn,
            'SELECT id, first_name, last_name, username, email, password_hash, role, is_active, profile_image
             FROM users WHERE email = ? LIMIT 1',
            's', [$email]
        );
    }

    public function findById(int $id): ?array {
        return DB::fetch($this->conn,
            'SELECT id, first_name, last_name, username, email, role,
                    phone_number, address, profile_image, created_at
             FROM users WHERE id = ? LIMIT 1',
            'i', [$id]
        );
    }

    public function emailOrUsernameExists(string $email, string $username, int $excludeId = 0): bool {
        return DB::exists($this->conn,
            'SELECT id FROM users WHERE (email = ? OR username = ?) AND id != ? LIMIT 1',
            'ssi', [$email, $username, $excludeId]
        );
    }

    public function create(string $firstName, string $lastName, string $email, string $username, string $password): bool {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        return DB::execute($this->conn,
            'INSERT INTO users (first_name, last_name, email, username, password_hash) VALUES (?, ?, ?, ?, ?)',
            'sssss', [$firstName, $lastName, $email, $username, $hash]
        );
    }

    public function createFull(string $firstName, string $lastName, string $email, string $username,
                               string $password, string $phone = '', string $address = '', string $profileImage = ''): bool {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        return DB::execute($this->conn,
            'INSERT INTO users (first_name, last_name, email, username, password_hash, phone_number, address, profile_image)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            'ssssssss', [$firstName, $lastName, $email, $username, $hash, $phone, $address, $profileImage]
        );
    }

    public function updateProfile(int $id, string $firstName, string $lastName, string $username,
                                  string $email, string $phone = '', string $address = ''): bool {
        return DB::execute($this->conn,
            'UPDATE users SET first_name=?, last_name=?, username=?, email=?, phone_number=?, address=? WHERE id=?',
            'ssssssi', [$firstName, $lastName, $username, $email, $phone, $address, $id]
        );
    }

    public function updatePassword(int $id, string $newPassword): bool {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        return DB::execute($this->conn,
            'UPDATE users SET password_hash=? WHERE id=?',
            'si', [$hash, $id]
        );
    }

    public function getPasswordHash(int $id): ?string {
        $row = DB::fetch($this->conn, 'SELECT password_hash FROM users WHERE id = ? LIMIT 1', 'i', [$id]);
        return $row['password_hash'] ?? null;
    }

    public function updateProfileImage(int $id, string $imagePath): bool {
        return DB::execute($this->conn,
            'UPDATE users SET profile_image = ? WHERE id = ?',
            'si', [$imagePath, $id]
        );
    }
}
