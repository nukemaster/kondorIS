<?php
//todo: doplit about
namespace App\Presenters;

use Nette,
    components;

class HomepagePresenter extends \BasePresenter
{
    /** @var  components\IRegistrationFactory @inject */
    public $registrationFactory;

    /** @var  components\ILoginFactory @inject */
    public $loginFactory;

    public function renderDefault()
    {
        if ($this->user->loggedIn) {
            $this->redirect('Homepage:logged');
        }
    }

    public function renderLogOff()
    {
        $this->user->logout(TRUE);
        $this->flashMessage('Uživatel odhlášen');
        $this->redirect('Homepage:default');
    }

    public function renderLogged() {
        if (! $this->user->loggedIn) {
            $this->redirect('Homepage:default');
        }
        $this->template->isAllowedAdministrace = $this->user->isAllowed("administrace", "enter");
    }

    public function createComponentLogInForm()
    {
        return $this->loginFactory->create();
    }

    public function createComponentRegistration()
    {
        return $this->registrationFactory->create();
    }
}
