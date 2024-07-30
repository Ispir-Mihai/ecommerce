<?php

require_once "init.php";
require_once "db.php";

class Permission
{
    public int $id;
    public string $mod;
    public string $description;

    public function __construct(int $id, string $mod, string $description)
    {
        $this->id = $id;
        $this->mod = $mod;
        $this->description = $description;
    }

    public static function getById(Database $db, int $id): Permission
    {
        $row = $db->select("permissions", ["id" => $id])[0];

        return new Permission($row["id"], $row["mod"], $row["description"]);
    }
}

class Role
{
    private Database $db;
    public int $id;
    public string $name;
    public array $permissions;

    public function __construct(Database $db, int $id, string $name, array $permissions)
    {
        $this->db = $db;
        $this->id = $id;
        $this->name = $name;
        $this->permissions = $permissions;
    }

    public static function getById(Database $db, int $id): Role
    {
        $row = $db->select("roles", ["id" => $id])[0];
        $role_permissions = $db->select("role_permissions", ["role_id" => $id]);
        $permissions = [];

        foreach ($role_permissions as $role_permission) {
            $permissions[] = Permission::getById($db, $role_permission["permission_id"]);
        }

        return new Role($db, $row["id"], $row["name"], $permissions);
    }
    public static function getAll(Database $db): array
    {
        $roles = [];

        foreach ($db->select("roles") as $row) {
            $role_permissions = $db->select("role_permissions", ["role_id" => $row["id"]]);
            $permissions = [];

            foreach ($role_permissions as $role_permission) {
                $permissions[] = Permission::getById($db, $role_permission["permission_id"]);
            }

            $roles[] = new Role($db, $row["id"], $row["name"], $permissions);
        }

        return $roles;
    }

    public function __sleep()
    {
        return ["id", "name", "permissions"];
    }
}

class User
{
    private Database $db;
    public int $id;
    public string $first_name;
    public string $last_name;
    public string $email;
    public string $password_hash;
    public int $role_id;
    public Role $role;

    public function __construct(Database $db, int $id, string $first_name, string $last_name, string $email, string $password_hash, int $role_id)
    {
        $this->db = $db;
        $this->id = $id;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->email = $email;
        $this->password_hash = $password_hash;
        $this->role_id = $role_id;
        $this->role = Role::getById($this->db, $role_id);
    }

    public function update(string $first_name, string $last_name, string $email, string $password_hash, int $role_id): array
    {
        $user = User::getByEmail($this->db, $email);
        if ($user && $email != $user->email) {
            return [
                "success" => false,
                "message" => "Users with email duplicates are not allowed."
            ];
        }

        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->email = $email;
        $this->password_hash = $password_hash;
        $this->role_id = $role_id;

        $this->db->update(
            "users",
            [
                "first_name" => $this->first_name,
                "last_name" => $this->last_name,
                "email" => $this->email,
                "password_hash" => $this->password_hash,
                "role_id" => $this->role_id
            ],
            ["id" => $this->id]
        );

        return [
            "success" => true,
            "message" => "User updated."
        ];
    }
    public static function create(Database $db, string $first_name, string $last_name, string $email, int $role_id): array
    {
        $user = User::getByEmail($db, $email);

        if ($user) {
            return [
                "success" => false,
                "message" => "Users with email duplicates are not allowed."
            ];
        }

        $db->insert(
            "users",
            [
                "first_name" => $first_name,
                "last_name" => $last_name,
                "email" => $email,
                "password_hash" => "",
                "role_id" => 7
            ]
        );

        return [
            "success" => true,
            "message" => "User created."
        ];
    }
    public static function delete(Database $db, int $id): array
    {
        $db->delete("users", ["id" => $id]);
        return [
            "success" => true,
            "message" => "User deleted."
        ];
    }

    public static function login(Database $db, string $email, string $password): array
    {
        $user = User::getByEmail($db, $email);

        if (!$user) {
            return [
                "success" => false,
                "message" => "Account does not exist."
            ];
        }

        if (hash("sha256", $password) != $user->password_hash) {
            return [
                "success" => false,
                "message" => "Wrong password."
            ];
        }

        $_SESSION["user"] = serialize($user);

        return [
            "success" => $user,
            "message" => "Logged in."
        ];
    }
    public static function register(Database $db, string $first_name, string $last_name, string $email, string $password, string $confirm_password): array
    {
        $user = User::getByEmail($db, $email);

        if ($user) {
            return [
                "success" => false,
                "message" => "Email already in use."
            ];
        }

        if ($password != $confirm_password) {
            return [
                "success" => false,
                "message" => "Passwords do not match."
            ];
        }

        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';
        if (!preg_match($pattern, $password)) {
            return [
                "success" => false,
                "message" => "
                    Password must be 8 characters or longer.<br>
                    Contain at least one lowercase letter.<br>
                    Contain at least one uppercase letter.<br>
                    Contain at least one digit.<br>
                    Contain at least one special character.
                "
            ];
        }

        $password_hash = hash("sha256", $password);
        $db->insert(
            "users",
            [
                "first_name" => $first_name,
                "last_name" => $last_name,
                "email" => $email,
                "password_hash" => $password_hash,
                "role_id" => 7
            ]
        );

        return [
            "success" => $user,
            "message" => "Account created successfully."
        ];
    }
    public static function logout(): void
    {
        $_SESSION = array();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();
    }

    public static function getById(Database $db, int $id): ?User
    {
        $rows = $db->select("users", ["id" => $id]);

        if (empty($rows))
            return null;

        $row = $rows[0];
        return new User($db, $row["id"], $row["first_name"], $row["last_name"], $row["email"], $row["password_hash"], $row["role_id"]);
    }
    public static function getByEmail(Database $db, string $email): ?User
    {
        $rows = $db->select("users", ["email" => $email]);

        if (empty($rows))
            return null;

        $row = $rows[0];
        return new User($db, $row["id"], $row["first_name"], $row["last_name"], $row["email"], $row["password_hash"], $row["role_id"]);
    }

    public static function getSessionUser(): ?User
    {
        return isset($_SESSION["user"]) ? unserialize($_SESSION["user"]) : null;
    }
    public static function isUserLoggedIn(): bool
    {
        return isset($_SESSION["user"]);
    }
    public static function getAll(Database $db): array
    {
        $users = [];
        foreach ($db->select("users") as $row) {
            $users[] = new User($db, $row["id"], $row["first_name"], $row["last_name"], $row["email"], $row["password_hash"], $row["role_id"]);
        }

        return $users;
    }

    public function __sleep()
    {
        return ["id", "first_name", "last_name", "email", "password_hash", "role_id", "role"];
    }
}

// User::login($db, "htchd2ariciu@gmail.com", "Timisoara1!");
User::login($db, "mihaimechanic@gmail.com", "Timisoara1!");
