<?php
class Database {
    private $dsn = 'mysql:host=localhost;dbname=dnis';
    private $username = 'root';
    private $password = 'guifi';

    private $db;
    private $stmt;

    public function __construct() {
        try {
                $this->db = new PDO($this->dsn, $this->username, $this->password);
        } catch (PDOException $e) {
		print_r($e);
                $this->error = $e->getMessage();
        }
    }

    public function bind($param, $value, $type = null){
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    public function execute(){
        return $this->stmt->execute();
    }

    public function single(){
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function query($query){
        $this->stmt = $this->db->prepare($query);
    }

    public function updateDni($idu, $idni, $img) {
        $img = base64_decode($img);
        $this->stmt = $this->db->prepare("UPDATE dnis SET img=':img' WHERE idni=:idni and frm=:frm");
        $this->bind(":frm", $idu);
        $this->bind(":idni", $idni);
        $this->bind(":img", $img);
        $this->execute();
    }

    public function deleteDni($idu, $idni) {
        $this->stmt = $this->db->prepare("DELETE FROM dnis WHERE idni=:idni and frm=:frm");
        $this->bind(":frm", $idu);
        $this->bind(":idni", $idni);
        $this->execute();
    }

    public function insertDni($idu, $file) {
        $this->stmt = $this->db->prepare("INSERT INTO dnis(frm, img) VALUES(:frm, :img)");
        $this->bind(":frm", $idu);
        $this->bind(":img", $file);
        $this->execute();
    }

    public function resultset(){
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

