<?php
namespace App\Presenters;
//todo: omezit pristup
use Nette,
    App,
    Nette\Application\UI,
    Nette\Application\UI\Multiplier,
    components;

class AdministracePresenter extends \BasePresenter
{
    protected $administraceAPI;

    public function __construct(Nette\Database\Context $database)
    {
        $this->administraceAPI = new App\model\AdministraceAPI($database);
    }

    public function beforeRender()
    {
        \BasePresenter::beforeRender();
        if (! $this->user->isAllowed("administrace", "enter")) {
            $this->redirect('Homepage:default');
        }
    }

    public function renderDefault()
    {
        $this->template->users = $this->administraceAPI->getUsers();
        $this->template->userHlasy = $this->administraceAPI->getUserHlasy();
        $this->template->hlasy = $this->administraceAPI->getHlasy();
    }

    public function updateUserSuccess($form)
    {
        $values = $form->getValues();
        $this->administraceAPI->updateUser($form->name, $values->role);
    }

    public function updateHlasSuccess($form)
    {
        $values = $form->getValues();
        $this->administraceAPI->updateHlas($values->active, $values->hlasName, $form->name);
    }

    public function updateUserHlasSuccess($form)
    {
        $values = $form->getValues();
        $this->administraceAPI->updateUserHlas($values->active, $values->user, $values->hlas, $form->name);
    }

    public function newUserHlasSuccess($form)
    {
        $values = $form->getValues();
        $this->administraceAPI->newUserHlas($values->hlas, $values->user);
    }
    
    public function newHlasSuccess($form)
    {
        $values = $form->getValues();
        $this->administraceAPI->newHlas($values->name);
    }

    public function createComponentUpdateUserForm()
    {
        return new Multiplier(function ($userId) {
            $form = new UI\Form;
            $roles = $this->administraceAPI->getRoles();
            $arrOptions = array();
            foreach ($roles as $role) {
                $arrOptions[$role->name] = $role->text;
            }
            $form->addSelect('role', NULL, $arrOptions)
                ->setDefaultValue($this->administraceAPI->getRoleOfUser($userId)->role);
            $form->onSuccess[] = [$this, 'updateUserSuccess'];
            return $form;
        });
    }

    public function createComponentUpdateHlasForm()
    {
        return new Multiplier(function ($id) {
            $detail = $this->administraceAPI->getHlasDetail($id);
            $form = new UI\Form;
            $form->addText('hlasName')
                ->setDefaultValue($detail->name)
                ->setMaxLength(50)
                ->setAttribute('length', '50');
            $form->addCheckbox('active')
                ->setDefaultValue($detail->active);
            $form->onSuccess[] = [$this, 'updateHlasSuccess'];
            return $form;
        });
    }

    public function createComponentUpdateUserHlasForm()
    {
        return new Multiplier(function ($id) {
            $users = array();
            $hlasy = array();
            foreach ($this->administraceAPI->getUsers() as $user) {
                $users[$user->id] = $user->name;
            }
            foreach ($this->administraceAPI->getHlasy() as $hlas) {
                $hlasy[$hlas->id] = $hlas->name;
            }

            $detail = $this->administraceAPI->getUserHlasDetail($id);
            $form = new UI\Form;
            $form->addSelect('hlas', 'Hlas', $hlasy)
                ->setDefaultValue($detail->hlas_id);
            $form->addSelect('user', 'Uživatel', $users)
                ->setDefaultValue($detail->user_id);
            $form->addCheckbox('active')
                ->setDefaultValue($detail->active);
            $form->onSuccess[] = [$this, 'updateUserHlasSuccess'];

            return $form;
        });
    }
    
    public function createComponentNewHlasUserForm()
    {
        $users = array();
        $hlasy = array();
        foreach ($this->administraceAPI->getUsers() as $user) {
            $users[$user->id] = $user->name;
        }
        foreach ($this->administraceAPI->getHlasy() as $hlas) {
            $hlasy[$hlas->id] = $hlas->name;
        }

        $form = new UI\Form;
        $form->addSelect('user', "Uživatel", $users)
            ->setPrompt('Vyber uživatele')
            ->setRequired('Je potřeba vybrat uživatele');
        $form->addSelect('hlas', "Hlas", $hlasy)
            ->setPrompt('Vyber hlas')
            ->setRequired('Je potřeba vybrat hlas');
        $form->onSuccess[] = [$this, 'newUserHlasSuccess'];

        return $form;
    }

    public function createComponentNewHlasForm()
    {
        $form = new UI\Form;
        $form->addText('name', 'Jméno hlasu' )
            ->setMaxLength(50)
            ->setAttribute('length', '50');;
        $form->onSuccess[] = [$this, 'newHlasSuccess'];
        return $form;
    }

};

