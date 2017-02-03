<?php
namespace App\model;

use Nette;

class AdministraceAPI
{
    /** @var Nette\Database\Context */
    protected $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function getUsers()
    {
        $sql = "SELECT u.id, u.name, u.email FROM users u";
        return $this->database->query($sql)->fetchAll();
    }

    public function getRoles()
    {
        $sql = "SELECT * FROM acl_roles";
        return $this->database->query($sql)->fetchAll();
    }

    public function getRoleOfUser($id)
    {
        $sql = "SELECT users.role FROM users WHERE users.id = ?";
        return $this->database->query($sql, $id)->fetch();
    }

    public function getHlasy()
    {
        $sql = "SELECT id, name FROM hlas ORDER BY active DESC, priority DESC, name ASC ";
        return $this->database->query($sql)->fetchAll();
    }
        
    public function getUserHlasy()
    {
        $sql = "SELECT t.active, t.id
                  FROM user_hlas t
                  ORDER BY t.active DESC";
        return $this->database->query($sql)->fetchAll();
    }

    public function getHlasDetail($id)
    {
        $sql = "SELECT * FROM hlas WHERE id = ?";
        return $this->database->query($sql, $id)->fetch();
    }

    public function getUserHlasDetail($id)
    {
        $sql = "SELECT t.active, hlas.name, t.user_id, t.hlas_id
                  FROM user_hlas t
                  INNER JOIN hlas
                    ON t.hlas_id = hlas.id
                  WHERE t.id = ?";
        return $this->database->query($sql, $id)->fetch();
    }

    public function updateUser($userId, $role)
    {
        $sql = "UPDATE users SET role = ? WHERE id = ?";
        return $this->database->query($sql, $role, $userId);
    }

    public function updateHlas($active, $name, $hlasId)
    {
        $sql = "UPDATE hlas SET active = ?, name = ? WHERE id =?";
        $this->database->query($sql, $active, $name, $hlasId);
    }

    public function updateUserHlas($active, $userId, $hlasId, $id)
    {
        $sql = "UPDATE user_hlas SET active = ?, user_id = ?, hlas_id = ? WHERE id = ?";
        $this->database->query($sql, $active, $userId, $hlasId, $id);
    }

    public function newUserHlas($hlasId, $userId)
    {
        $sql = "INSERT INTO user_hlas(hlas_id, user_id, active) VALUES (?, ?, TRUE)";
        $this->database->query($sql, $hlasId, $userId);
    }

    public function newHlas($name)
    {
        $sql = "INSERT INTO hlas(name) VALUES (?)";
        $this->database->query($sql, $name);
    }
};