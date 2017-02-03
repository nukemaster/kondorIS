<?php

namespace App\Presenters;

use Nette,
    App,
    Nette\Application\UI,
    Nette\Application\UI\Multiplier;

class OddilovkyPresenter extends \BasePresenter
{
    protected $oddilovkyAPI;
    
    public function __construct(Nette\Database\Context $database)
    {
        $this->oddilovkyAPI = new App\model\OddilovkyAPI($database);
    }

    public function handleDownload($id)
    {
        $oddilovka = $this->oddilovkyAPI->getOddilovkaByID($id);
        $bodyOddilovky = $this->oddilovkyAPI->getBodyOddilovky($id);
        $filename = "oddilovka-$oddilovka->aktivni_do";
        $filecontent = "";

        $filecontent .= "Výpis z informačního systému KondorIS \n";
        $filecontent .= "Vytvořeno: " . date("Y-n-j G:i") . "\n\n";
        $filecontent .= "----------------------------------------------------------------------\n";
        $filecontent .= "------------------------------ ODDÍLOVKA -----------------------------\n";
        $filecontent .= "----------------------------------------------------------------------\n";

        $filecontent .= "\n$oddilovka->popis_short\n";
        $filecontent .= "Od: $oddilovka->aktivni_do Do: $oddilovka->aktivni_do\n";
        $filecontent .= "Autor: $oddilovka->name";
        $filecontent .= "\n$oddilovka->popis_long\n";

        foreach ($bodyOddilovky as $bod1) {
            $bod = $this->oddilovkyAPI->getBodOddilovkyByID($bod1->id); //b.name, b.vytvoreno, b.oddilovka_id, b.popis, b.text, users.name author
            $filecontent .= "----------------------------------------------------------------------\n";
            $filecontent .= "---------------------------- BOD ODDÍLOVKY ---------------------------\n";
            $filecontent .= "----------------------------------------------------------------------\n\n";

            $filecontent .= "$bod->name\n";
            $filecontent .= "Autor: $bod->author $bod->vytvoreno\n";
            $filecontent .= "Popis: $bod->popis\n\n";
            $filecontent .= "$bod->text\n\n";

            $filecontent .= "-------------------------------- HLASY -------------------------------\n";

            foreach ($this->oddilovkyAPI->getKomentareHlasyOnPointID($bod1->id, $this->user->id) as $hlas) { //hlas.name, k.text,
                $filecontent .= "----------------------------------------------------------------------\n\n";
                $filecontent .= "Hlas: $hlas->name\n";
                $filecontent .= "$hlas->text\n\n";
            }
        }
        $filecontent = str_replace("<br>", "\n", $filecontent);
        $filecontent = wordwrap($filecontent);


        $filesize = strlen($filecontent);

        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Length: ". $filesize);
        header("Content-Disposition: attachment; filename=". $filename);
        header("Content-Type: application/octet-stream; ");
        header("Content-Transfer-Encoding: binary");

        echo $filecontent;

    }

    public function beforeRender()
    {
        \BasePresenter::beforeRender();
        if (!$this->user->isAllowed('oddilovka', 'read')) {
            $this->redirect('Homepage:default');
        }
    }

    public function renderDefault()
    {
        $this->template->oddilovky = $this->oddilovkyAPI->getOddilovky();
        $this->template->canCreateNewOddilovka = $this->user->isAllowed('oddilovka', 'create');
    }
    
    public function renderRead($id)
    {
        $oddilovka = $this->oddilovkyAPI->getOddilovkaByID($id);
        $this->template->canCreateBodOddilovky = $this->user->isAllowed('bodOddilovky', 'create');
        $this->template->oddilovka = $oddilovka;
        $this->template->body = $this->oddilovkyAPI->getBodyOddilovky($id);
    }

    public function renderReadPoint($id)
    {
        $this->template->bod = $this->oddilovkyAPI->getBodOddilovkyByID($id);
        
        $this->template->komentareHlasy = $this->oddilovkyAPI->getKomentareHlasyOnPointID($id, $this->user->id);
        $this->template->canCommentHlas = (($this->user->isAllowed('bodOddilovky', 'hlas') AND $this->oddilovkyAPI->getHaveUserHlas($this->user->id)) && (!empty($this->oddilovkyAPI->getUsersHlasyNotUsedInBodOddilovky($this->user->id, $this->getParameter('id')))));
        $usersHlasy = $this->oddilovkyAPI->getUsersHlasy($this->user->id);
        
        $this->template->komentareDiskuze = $this->oddilovkyAPI->getKomentareDiskuzeOnPointID($id);
        $this->template->canCommentDiskuze = $this->user->isAllowed('bodOddilovky', 'coment');
    }

    public function createComponentNewOddilovkaForm()
    {
        $form = new UI\Form;

        $form->addText('date', 'datum')
            ->setAttribute('type', 'date')
            ->setAttribute('class', 'datepicker')
            ->setRequired(true)
            ->addRule(function ($item) {
                $value = Nette\Utils\DateTime::createFromFormat("d M, Y", $item->getValue());
                return ($value == false)? false : true;
            }, "datumNeodpovida", 8);
        $form->addTextArea('popisLong', 'PopisLong');
        $form->addText('popis', "Popis")
            ->setMaxLength(75)
            ->setAttribute('length', '75');
        $form->onSuccess[] = [$this, 'newOddilovkaSuccess'];
        return $form;
    }

    public function createComponentDiskuzeForm()
    {
        $form = new UI\Form;
        $form->addTextArea('text', 'Text Příspěvku');
        $form->onSuccess[] = [$this, 'prispevekDiskuzeSuccess'];
        return $form;
    }

    public function createComponentHlasyForm()
    {
        $form = new UI\Form;

        $arrOptions = array();
        $hlasy =$this->oddilovkyAPI->getUsersHlasyNotUsedInBodOddilovky($this->user->id, $this->getParameter('id'));
        foreach ($hlasy as $hlas) {
            $arrOptions[$hlas->id] = $hlas->name;
        }

        $form->addTextArea('text', 'Text Hlasu');
        $form->addSelect('hlas', 'Hlas', $arrOptions );
        $form->onSuccess[] = [$this, 'prispevekHlasyDiskuzeSussess'];
        return $form;
    }

    public function createComponentNovyBodOddilovky()
    {
        $form = new UI\Form;
        $form->addText('name', 'Jmeno')
            ->setMaxLength(50)
            ->setAttribute('length', '50');
        $form->addText('popis', 'Popis')
            ->setMaxLength(100)
            ->setAttribute('length', '100');
        $form->addTextArea('text', 'Text');
        $form->onSuccess[] = [$this, 'novyBodOddilovkySuccess'];
        
        return $form;
    }

    public function createComponentUpdateHlas()
    {
        return new Multiplier(function ($hlasId) {
            $form = new UI\Form;
            $form->addTextArea('text')
                ->setDefaultValue($this->oddilovkyAPI->getHlas($hlasId)->text);
            $form->onSuccess[] = [$this, 'updateHlasSuccess'];
            return $form;
        });
    }

    public function newOddilovkaSuccess(UI\Form $form) {
        if (! $this->user->isAllowed('oddilovka', 'create')) {
            return false;
        }
        $values = $form->getValues();
        $date = Nette\Utils\DateTime::createFromFormat("d M, Y",$values->date);
        $this->oddilovkyAPI->addOddilovka($this->user->id, $date, $values->popis, $values->popisLong);
    }

    public function prispevekDiskuzeSuccess(UI\Form $form)
    {
        if (! $this->user->isAllowed('bodOddilovky', 'create')) {
            return false;
        }

        $values = $form->getValues();
        $this->oddilovkyAPI->addKomentarDiskuzeOnPointID($this->user->id, $values->text, $this->getParameter('id'));
    }
    
    public function prispevekHlasyDiskuzeSussess(UI\Form $form)
    {
        //todo: kontrola zda ma uzivatel pristup  k hlasu ktery chce pouzit
//        if (!$this->user->isAllowed('bodOddilovky', 'hlas')) {
//            $this->redirect('Homepage:default');
//        }
        $values = $form->getValues();
        $this->oddilovkyAPI->newHlas($values->text, $values->hlas, $this->getParameter('id'));
    }
    
    public function novyBodOddilovkySuccess(UI\Form $form)
    {
        if (!$this->user->isAllowed('bodOddilovky', 'create')) {
            $this->redirect('Homepage:default');
            return false;
        }
        $values = $form->getValues();
        $this->oddilovkyAPI->addBodOddilovky($this->getParameter('id'), $this->user->id, $values->name, $values->popis, $values->text);
    }

    public function updateHlasSuccess($form)
    {
        //todo: overeni zda ma uzivatel opravdu pristup k hlasu ktery chce upravit

        $values = $form->getValues();
        $this->oddilovkyAPI->updateHlas($values->text, $form->name);
    }



}
