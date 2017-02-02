<?php
use Nette\Application\UI\Presenter;

abstract class BasePresenter extends Presenter
{
    public function beforeRender()
    {
        //$this->template->user = $this->user;
        $this->template->userLoggedIn = $this->user->loggedIn;
        $this->template->userIdentity = $this->user->getIdentity();
    }
}