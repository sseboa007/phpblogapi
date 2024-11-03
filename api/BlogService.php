<?php
    class BlogService {
        private PDO $conn;
        private string $table = 'blogs';

        public function __construct(PDO $connection) {
            $this->conn = $connection;
        }

        public function getBlog(string $id) {
            $sql = "
                SELECT
                    BlogId,
                    Title,
                    Content,
                    Image,
                    DateCreated,
                    DateUpdated
                FROM
                    $this->table
                WHERE
                    BlogId = :id
            ";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":id", $id, PDO::PARAM_INT);

            $stmt->execute();

            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            return $data;
        }

        public function getBlogs(?object $decodedToken = null) {
            $sql = "";
            $stmt = null;

            if (empty($decodedToken) || $decodedToken->role == 1) {
                $sql = "
                    SELECT
                        BlogId,
                        Title,
                        Content,
                        Image,
                        DateCreated,
                        DateUpdated,
                        UserId
                    FROM
                        $this->table
                ";

                $stmt = $this->conn->query($sql);
            }
            else {
                $sql = "
                    SELECT
                        BlogId,
                        Title,
                        Content,
                        Image,
                        DateCreated,
                        DateUpdated,
                        UserId
                    FROM
                        $this->table
                    WHERE
                        UserId = :userId
                ";

                $stmt = $this->conn->prepare($sql);

                $stmt->bindValue(":userId", $decodedToken->sub, PDO::PARAM_INT);

                $stmt->execute();
            }

            $data = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }

            return $data;
        }

        public function create($data, $userId) {
            $sql = "
                INSERT INTO $this->table (Title, Content, DateCreated, Image, UserId)
                VALUES (:title, :content, :dateCreated, :image, :userId)
            ";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":title", $data["title"], PDO::PARAM_STR);
            $stmt->bindValue(":content", $data["content"], PDO::PARAM_STR);
            $stmt->bindValue(":image", $data["feature_image"], PDO::PARAM_STR);
            $stmt->bindValue(":userId", $userId, PDO::PARAM_INT);
            $stmt->bindValue(":dateCreated", date('Y-m-d H:i:s'), PDO::PARAM_STR);

            $stmt->execute();

            return $this->conn->lastInsertId();
        }

        public function update($data, $blog, $userId) {
            $sql = "
                UPDATE
                    $this->table
                SET
                    Title = :title,
                    Content = :content,
                    Image = :image,
                    DateUpdated = :dateUpdated
                WHERE
                    BlogId = :blogId
            ";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":title", $data["title"], PDO::PARAM_STR);
            $stmt->bindValue(":content", $data["content"], PDO::PARAM_STR);
            $stmt->bindValue(":image", $data["feature_image"] ?? $blog["Image"], PDO::PARAM_STR);
            $stmt->bindValue(":dateUpdated", date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $stmt->bindValue(":blogId", $blog["BlogId"], PDO::PARAM_INT);

            $stmt->execute();
        }

        public function delete($blogId) {
            $sql = "
                DELETE FROM
                    $this->table
                WHERE
                    BlogId = :blogId
            ";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":blogId", $blogId, PDO::PARAM_INT);

            $stmt->execute();
        }
    }
?>