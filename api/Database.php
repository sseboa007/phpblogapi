<?php

    class Database {
        public function __construct(private string $host,
                                    private string $name,
                                    private string $user,
                                    private string $password) {

        }

        public function getConnection(): PDO {
            return new PDO("mysql:host=$this->host;dbname=$this->name;charset=utf8", 
                            $this->user, 
                            $this->password,
                            [
                                PDO::ATTR_EMULATE_PREPARES => false,
                                PDO::ATTR_STRINGIFY_FETCHES => false
                            ]
                        );
        }
    }

?>
