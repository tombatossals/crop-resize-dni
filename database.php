<?php
class Database {
    private $dsn = 'mysql:host=localhost;dbname=recogidadnis';
    private $username = 'recogidadnis';
    private $password = 'recogidadnisLALALA';

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

    public function updateDni($idu, $idni, $img, $ndni, $cara) {
	$img = str_replace("data:image/jpeg;base64,", "", $img);
        $img = base64_decode($img, true);
        list($width, $height) = getimagesizefromstring($img);
        if ($width < 1024) {
	    $i2 = imagecreatetruecolor(1024, $height*1024/$width);
	    $i = imagecreatefromstring($img);
	    imagecopyresampled($i2, $i, 0, 0, 0, 0, 1024, $height*1024/$width, $width, $height);
            ob_start();
            imagejpeg($i2);
            $img = ob_get_contents();
	    ob_end_clean(); 
        }

        if ($img) {
        	$this->stmt = $this->db->prepare("UPDATE dnis SET img=:img, cara=:cara, ndni=:ndni WHERE idni=:idni and frm=:frm");
                $this->bind(":img", $img);
        } else {
        	$this->stmt = $this->db->prepare("UPDATE dnis SET ndni=:ndni, cara=:cara WHERE idni=:idni and frm=:frm");
        }
        $this->bind(":frm", $idu);
        $this->bind(":idni", $idni);
        $this->bind(":ndni", $ndni);
        $this->bind(":cara", $cara);
        $this->execute();
    }

    public function auth($username, $password) {
        $this->stmt = $this->db->prepare("SELECT * FROM usuarios WHERE emailtelf=:username and codigo=:codigo");
        $this->bind(":username", $username);
        $this->bind(":codigo", $password);
        $this->execute();
        return $this->single();
    }

    public function setPage($idu, $idni, $pagina) {
        $this->stmt = $this->db->prepare("UPDATE dnis SET hoja=:pagina WHERE idni=:idni and frm=:frm");
        $this->bind(":frm", $idu);
        $this->bind(":idni", $idni);
        $this->bind(":pagina", intval($pagina));
        $this->execute();
    }


    public function getPages($idu) {
	$this->stmt = $this->db->prepare('SELECT hoja, count(*) as count FROM dnis where frm=:idu GROUP BY hoja');
        $this->bind(":idu", $idu);
	$this->execute();
	$res = $this->resultset();
	$pages = array();
	foreach ($res as $p) {
		$pages[$p['hoja']] = $p['count'];
	}

	return $pages;
    }

    public function getPage($pagina, $idu) {
	$this->stmt = $this->db->prepare('SELECT * FROM dnis where frm=:idu and hoja=:pagina');
        $this->bind(":idu", $idu);
	$this->bind(':pagina', $pagina);
	$this->execute();
	return $this->resultset();
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

