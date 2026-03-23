<?php

class User extends Model
{
    public function findByEmail(string $email): array|false
    {
        $statement = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $statement->execute(['email' => $email]);

        return $statement->fetch();
    }

    public function create(array $data): bool
    {
        $statement = $this->db->prepare(
            'INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)'
        );

        return $statement->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'] ?? 'customer',
        ]);
    }

    public function all(): array
    {
        $statement = $this->db->query('SELECT id, name, email, role, created_at FROM users ORDER BY id DESC');
        return $statement->fetchAll();
    }
}

