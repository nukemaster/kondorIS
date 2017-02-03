<?php
namespace App\model;

use Nette;

class OddilovkyAPI
{
    const T_ODDILOVKY = 'oddilovky';


    /** @var Nette\Database\Context  */
    protected $database;

    public function replace($text)
    {
        $text = str_replace("\n", "<br>", $text);
        //$text = str_replace("script", "")
        return $text;
    }

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function getOddilovky()
    {
        $sql = "SELECT * FROM " . self::T_ODDILOVKY;
        return $this->database->query($sql)->fetchAll();
    }

    public function getOddilovkaByID($id)
    {
        $sql = "SELECT o.aktivni_od, o.aktivni_do, o.popis_short, o.popis_long, users.name 
                  FROM oddilovky o  
                  INNER JOIN users
                  ON o.autor = users.id
                  WHERE o.id = ?";
        return $this->database->query($sql, $id)->fetch();
    }

    public function getBodyOddilovky($id)
    {
        $sql = "SELECT b.id, b.name, b.autor, b.popis, b.vytvoreno, users.name author
                  FROM bod_oddilovky b
                  INNER JOIN users
                  ON b.autor = users.id
                  WHERE b.oddilovka_id = ?";
        return $this->database->query($sql, $id)->fetchAll();
    }

    public function getBodOddilovkyByID($id)
    {
        $sql = "SELECT b.name, b.vytvoreno, b.oddilovka_id, b.popis, b.text, users.name author
                  FROM bod_oddilovky b 
                  INNER JOIN users
                  ON b.autor = users.id
                  WHERE b.id = ?";
        return $this->database->query($sql, $id)->fetch();
    }

    public function getKomentareHlasyOnPointID($id, $userId)
    {
        $sql = 'SELECT hlas.name, k.text, k.create, k.hlas_id, k.id
                  FROM komentare_hlasy k 
                  INNER JOIN hlas
                    ON k.hlas_id = hlas.id
                  WHERE k.bod_oddilovky_id = ?
                  ORDER BY hlas.priority DESC ';
        $hlasy = $this->database->query($sql, $id)->fetchAll();

        $sql = "SELECT user_id FROM user_hlas WHERE hlas_id = ? AND user_id = ?";
        foreach ($hlasy as $key => $hlas) {
            $tmp = $this->database->query($sql, $hlas->hlas_id, $userId)->fetchAll();
            $hlasy[$key]->canUpdate = !empty($tmp);
        }
        return $hlasy;
    }

    public function getHlas($hlasId)
    {
        $sql = "SELECT hlas.name, REPLACE(k.text, '<br>', '\n') text, k.create 
                  FROM komentare_hlasy k 
                  INNER JOIN hlas
                    ON k.hlas_id = hlas.id
                  WHERE k.id = ?";
        return $this->database->query($sql, $hlasId)->fetch();
    }

    public function getKomentareDiskuzeOnPointID($id)
    {
        $sql = "SELECT users.name, k.time, k.text
                  FROM komentare_diskuze k
                  INNER JOIN users
                  ON k.user_id = users.id
                  WHERE k.bodOddilovky_id = ?";
        return $this->database->query($sql, $id)->fetchAll();
    }

    public function addKomentarDiskuzeOnPointID($user_id, $text, $bodOddilovky )
    {
        $text = $this->replace($text);
        $sql = "INSERT INTO komentare_diskuze(user_id, text, bodOddilovky_id ) 
                  VALUES (?, ?, ?)";
        return $this->database->query($sql, $user_id, $text, $bodOddilovky);
    }

    public function addKomentareHlasyOnPointID($hlas_id, $text, $bod_oddilovky_id )
    {
        $text = $this->replace($text);
        $sql = "INSERT INTO komentare_hlasy(hlas_id, text, bod_oddilovky_id)
                  VALUES (?, ?, ?)";
        return $this->database->query($sql, $hlas_id, $text, $bod_oddilovky_id);
    }

    public function addBodOddilovky($oddilovka_id, $autor, $name, $popis, $text)
    {
        $text = $this->replace($text);
        $text = $this->replace($popis);
        $sql = "INSERT INTO bod_oddilovky(oddilovka_id, autor, name, popis, text) 
                  VALUES (?, ?, ?, ?, ?)";
        return $this->database->query($sql, $oddilovka_id, $autor, $name, $popis, $text);
    }

    public function getUsersHlasy($id)
    {
        $sql = "SELECT hlas.name, hlas.id FROM user_hlas u 
                  INNER JOIN hlas
                  ON u.hlas_id = hlas.id
                  WHERE u.user_id = ? AND hlas.active = TRUE AND u.active = TRUE 
                  ORDER BY hlas.priority DESC";
        return $this->database->query($sql, $id)->fetchAll();
    }

    public function getUsersHlasyNotUsedInBodOddilovky($userId, $bodOddilovkyId)
    {
        $sql = "SELECT hlas.id, hlas.name
                  FROM hlas
                  INNER JOIN user_hlas
                    ON hlas.id = user_hlas.hlas_id
                  WHERE NOT EXISTS (SELECT *
                                      FROM komentare_hlasy
                                          WHERE komentare_hlasy.bod_oddilovky_id = ?
                                              AND komentare_hlasy.hlas_id = hlas.id
                                    )
                  AND user_hlas.user_id = ?
                ";
        return $this->database->query($sql, $bodOddilovkyId, $userId)->fetchAll();
    }

    public function getHaveUserHlas($id)
    {
        return !empty($this->getUsersHlasy($id));
    }

    public function updateHlas($text, $hlaId)
    {
        $text = $this->replace($text);
        $sql = "UPDATE komentare_hlasy SET text = ? WHERE komentare_hlasy.id = ?";
        $this->database->query($sql, $text, $hlaId);
    }

    public function newHlas($text, $hlas, $id)
    {
        $text = $this->replace($text);
        $sql = "INSERT INTO komentare_hlasy(text, hlas_id, bod_oddilovky_id) VALUES (?, ?, ?)";
        $this->database->query($sql, $text, $hlas, $id);
    }

    public function addOddilovka($userId, $date, $popis, $popisLong)
    {
        $popis = $this->replace($popis);
        $popisLong = $this->replace($popisLong);
        $sql = "INSERT INTO oddilovky(autor, aktivni_do, popis_short, popis_long) VALUES (?, ?, ?, ?)";
        $this->database->query($sql, $userId, $date, $popis, $popisLong);
    }
};