<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Tool\Check\FieldCheck;
use Admin\Model\IconModel;

class IconsController extends AbstractActionController
{
    public function indexAction()
    {
        $this->getServiceLocator()->get("viewhelpermanager")->get("HeadLink")->appendStylesheet("//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css");

        $viewModel = new ViewModel();
        $iconModel = new IconModel();

        $viewModel->setVariable("icons", $iconModel->all());

        return $viewModel;
    }

    public function newAction()
    {
        return new ViewModel();
    }

    public function editAction()
    {
        $viewModel = new ViewModel();
        $iconModel = new IconModel();
        $fieldCheck = new FieldCheck();

        $viewModel->setTemplate("admin/icons/new.phtml");
        $viewModel->setVariable("icon", $iconModel->find($fieldCheck->checkInput($this->params()->fromQuery("id"))));

        return $viewModel;
    }

    public function createAction()
    {
        $iconModel = new IconModel();

        $iconModel->add($this->params()->fromPost());

        return $this->redirect()->toRoute("admin/default", array(
            "controller" => "icons",
            "action" => "index"
        ));
    }

    public function updateAction()
    {
        $iconModel = new IconModel();

        $iconModel->update($this->params()->fromPost());

        return $this->redirect()->toRoute("admin/default", array(
            "controller" => "icons",
            "action" => "index"
        ));
    }

    public function deleteAction()
    {
        $fieldCheck = new FieldCheck();
        $iconModel = new IconModel();

        $iconModel->destroy($fieldCheck->checkInput($this->params()->fromQuery("id")));

        return $this->redirect()->toRoute("admin/default", array(
            "controller" => "icons",
            "action" => "index"
        ));
    }
}
?>