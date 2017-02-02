<?php

namespace components;

use Nette\Application\UI,
    Nette;

class Login extends UI\Control
{
    public $user;

    public function __construct(Nette\Security\User $user)
    {
        $this->user = $user;
    }

    public function render()
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/login.latte');
        $template->isLogedIn = $this->user->isLoggedIn();
        $template->render();
    }

    public function handleLogOff()
    {
        $this->user->logout(TRUE);
    }

    public function createComponentLoginForm()
    {
        $form = new UI\Form;
        $form->addText('name', 'Login')
            ->setRequired('Zadejte přihlašovací jméno');;
        $form->addPassword('password', 'Heslo')
            ->setRequired('Zadejte heslo');
        $form->onSuccess[] = array($this, 'loginSubmit');
        return $form;
    }

    public function createComponentLogoutForm()
    {
        $form = new UI\Form;
        $form->addSubmit('login', 'Odhlásit');
        $form->onSuccess[] = array($this, 'logoutSubmit');
        return $form;
    }

    public function loginSubmit($form)
    {
        $values = $form->getValues();
        try {
            $this->user->login($values->name, $values->password);
        }
        catch (Nette\Security\AuthenticationException $e) {
            $this->template->exeptionMessage = $e->getMessage();
        }
    }

    public function logoutSubmit()
    {
        $this->user->logout(TRUE);
    }
}

interface ILoginFactory
{
    /** @return \components\Login */
    function create();
}

?>