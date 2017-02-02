<?php

namespace App\model;

use Nette\Security\Permission;
use Nette;

class Acl extends Permission
{
    const ACL_TABLE = 'acl_table';
    const PRIVILEGES_TABLE = 'acl_privileges';
    const RESOURCES_TABLE = 'acl_resources';
    const ROLES_TABLE = 'acl_roles';

    /** @var Nette\Database\Context */
    private $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;

        foreach ($this->modelGetRoles() as $role) {
            $this->addRole($role->name, $role->parent_name);
        }

        foreach ($this->modelGetResources() as $resource) {
            $this->addResource($resource->name);
        }

        foreach ($this->modelGetRules() as $rule) {
            if ($rule->allow == true)
                $this->allow($rule->role, $rule->resource, $rule->privilage);
            else
                $this->deny($rule->role, $rule->resource, $rule->privilage);
        }
    }

    public function modelGetRoles()
    {
        $sql = "SELECT * FROM " . self::ROLES_TABLE;
        return $this->database->query($sql)->fetchAll();
    }

    public  function modelGetResources()
    {
        $sql = "SELECT * FROM " . self::RESOURCES_TABLE;
        return $this->database->query($sql)->fetchAll();
    }

    public function modelGetRules()
    {
        $sql = "SELECT * FROM " . self::ACL_TABLE;
        return $this->database->query($sql)->fetchAll();
    }
}