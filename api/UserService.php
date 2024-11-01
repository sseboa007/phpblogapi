<?php
    class UserService {
        private PDO $conn;
        private string $table = 'users';

        public function __construct(PDO $connection) {
            $this->conn = $connection;
        }

        public function getUser(string $id): array | false {
            $sql = "
                SELECT
                    UserId,
                    FirstName,
                    LastName,
                    Email,
                    Password,
                    RoleId,
                    DateCreated
                FROM
                    $this->table
                WHERE
                    UserId = :id
            ";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":id", $id, PDO::PARAM_INT);

            $stmt->execute();

            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            return $data;
        }

        public function getUserForLogin(string $email, string $password) {
            $sql = "
                SELECT
                    UserId,
                    FirstName,
                    LastName,
                    Email,
                    RoleId,
                    DateCreated
                FROM
                    $this->table
                WHERE
                    Email = :email
                    AND Password = :password
            ";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":email", $email, PDO::PARAM_STR);
            $stmt->bindValue(":password", $password, PDO::PARAM_STR);

            $stmt->execute();

            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            return $data;
        }

        public function getUsers(): array {
            //create query
            $sql = "
                SELECT
                    UserId,
                    FirstName,
                    LastName,
                    Email,
                    Password,
                    RoleId,
                    DateCreated
                FROM
                    $this->table
            ";

            //prepare statement
            $stmt = $this->conn->query($sql);

            $data = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }

            return $data;
        }

        public function create($data) {
            $sql = "
                INSERT INTO $this->table (FirstName, LastName, Email, Password, RoleId, DateCreated)
                VALUES (:firstName, :lastName, :email, :password, :roleId, :dateCreated)
            ";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":firstName", $data["firstName"], PDO::PARAM_STR);
            $stmt->bindValue(":lastName", $data["lastName"], PDO::PARAM_STR);
            $stmt->bindValue(":email", $data["email"], PDO::PARAM_STR);
            $stmt->bindValue(":password", password_hash($data["password"], PASSWORD_DEFAULT), PDO::PARAM_STR);
            $stmt->bindValue(":roleId", $data["roleId"], PDO::PARAM_INT);
            $stmt->bindValue(":dateCreated", date('Y-m-d H:i:s'), PDO::PARAM_STR);

            $stmt->execute();

            return $this->conn->lastInsertId();
        }
    }

?>