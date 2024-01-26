<?php

class BDD{
    private string $dsn = 'mysql:host=localhost;dbname=ludine_games;charsetutf8';
    private string $username = 'root';
    private string $password = '';
    private PDO $bdd;

    public function __construct(){
        $this->bdd = new PDO($this->dsn, $this->username, $this->password);
    }

    public function get_bdd(){
        return $this->bdd;
    }
}