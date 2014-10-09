<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Tool\Check\FieldCheck;
use Admin\Model\LinkModel;


class LinksController extends AbstractActionController
{
    public function indexAction()
    {
        $basePath = $this->getServiceLocator()->get("viewhelpermanager")->get("BasePath");
        $headScript = $this->getServiceLocator()->get("viewhelpermanager")->get("HeadScript");
        $headScript->appendFile($basePath->__invoke() . "/js/jquery-ui-1.10.3.custom.min.js");
        $headScript->appendFile($basePath->__invoke() . "/js/append-type-field.js");
        $headScript->appendFile($basePath->__invoke() . "/js/type-form.js");
        $headLink = $this->getServiceLocator()->get("viewhelpermanager")->get("HeadLink");
        $headLink->appendStylesheet("//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css");
        $this->getServiceLocator()->get("navigation/admin")->findOneById("home-page-manage")->setActive(true);

        $viewModel = new ViewModel();
        $linksModel = new LinkModel();
        $fieldCheck = new FieldCheck();
        $linkTypeId = $fieldCheck->checkInput($this->params()->fromQuery("link_type_id"));

        $viewModel->setVariable("links", $linksModel->findByLinkTypeId($linkTypeId));
        $viewModel->setVariable("linkTypeId", $linkTypeId);

        return $viewModel;
    }

    public function newAction()
    {
        $viewModel = new ViewModel();
        $fieldCheck = new FieldCheck();
        $link = array();
        $link["id"] = "";
        $link["link_type_id"] = $fieldCheck->checkInput($this->params()->fromQuery("link_type_id"));
        $link["name"] = "";
        $link["url"] = "";

        $viewModel->setVariable("link", $link);
        
        return $viewModel;
    }

    public function editAction()
    {
        $viewModel = new ViewModel();
        $fieldCheck = new FieldCheck();
        $linkModel = new LinkModel();

        $viewModel->setTemplate("admin/links/new.phtml");
        $viewModel->setVariable("link", $linkModel->find($fieldCheck->checkInput($this->params()->fromQuery("id"))));
        return $viewModel;
    }

    public function createAction()
    {
        $linkModel = new LinkModel();

        $link = $linkModel->find($linkModel->add($this->params()->fromPost()));

        return $this->redirect()->toRoute("admin/default", array(
            "controller" => "links",
            "action" => "index"
        ), array(
            "query" => array(
                "link_type_id" => $link["link_type_id"]
            )
        ));
    }

    public function updateAction()
    {
        $linkModel = new LinkModel();
        
        $link = $linkModel->find($linkModel->update($this->params()->fromPost()));

        return $this->redirect()->toRoute("admin/default", array(
            "controller" => "links",
            "action" => "index"
        ), array(
            "query" => array(
                "link_type_id" => $link["link_type_id"]
            )
        ));

    }

    public function destroyAction()
    {
        $linkModel = new LinkModel();
        $fieldCheck = new FieldCheck();
        $link = $linkModel->find($fieldCheck->checkInput($this->params()->fromQuery("id")));

        $linkModel->destroy($link["id"]);

        return $this->redirect()->toRoute("admin/default", array(
            "controller" => "links",
            "action" => "index"
        ), array(
            "query" => array(
                "link_type_id" => $link["link_type_id"]
            )
        ));
    }
}

?>