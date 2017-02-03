<?php

namespace components;

use Nette\Application\UI,
    Nette;

class Registration extends UI\Control
{

    public $userManager;

    public function __construct(\App\model\UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public function render()
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/registration.latte');
        $template->render();

    }

    public function createComponentRegistrationForm()
    {
        $form = new UI\Form;
        $form->addText('name', 'Login')
            ->setRequired('Zvolte si přihlašovací jméno');
        $form->addPassword('password', 'Heslo')
            ->setRequired('Zvolte si heslo');
        $form->addPassword('password2', 'Heslo znovu')
            ->setRequired('Zadejte prosím heslo ještě jednou pro kontrolu')
            ->addRule(UI\Form::EQUAL, 'hesla se neshoduji', $form['password']);
        $form->addEmail('email', 'Email')
            ->setRequired('Zadejte váš email')
            ->getLabel()->setAttribute('data-error','wrong');
        $form->addText('realName', 'Jméno a příjmení')
            ->setRequired('Zadejte své jméno a příjmení');
        //$form->addSubmit('submit', 'registrovat');
        //$form->addButton('submit', 'registrovat');
        $form->onSuccess[] = array($this, 'registrationSubmit');
        return $form;
    }

    public function registrationSubmit(UI\Form $form)
    {
        $values = $form->getValues();
        try {
            $this->userManager->add($values->name, $values->email, $values->password, $values->realName);
            $this->template->success = 1;
        }
        catch (\App\Exeptions\DuplicateNameException $e) {
            $this->template->success = 0;
            $this->template->exeptionMessage = $e->getMessage();
        }
    }
}

interface IRegistrationFactory
{
    /** @return \components\Registration */
    function create();
}

?>