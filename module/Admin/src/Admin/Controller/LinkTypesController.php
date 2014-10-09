<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Tool\Check\FieldCheck;
use Admin\Model\LinkTypeModel;
use Admin\Model\Language;
use Admin\Model\IconModel;

class LinkTypesController extends AbstractActionController
{
    public function indexAction()
    {
        $basePath = $this->getServiceLocator()->get("viewhelpermanager")->get("BasePath");
        $headScript = $this->getServiceLocator()->get("viewhelpermanager")->get("HeadScript");
        $headScript->appendFile($basePath->__invoke() . "/js/jquery-ui-1.10.3.custom.min.js");
        $headScript->appendFile("//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.2/js/bootstrap-select.min.js");
        $headScript->appendFile($basePath->__invoke() . "/js/append-type-field.js");
        $headScript->appendFile($basePath->__invoke() . "/js/type-form.js");
        $headLink = $this->getServiceLocator()->get("viewhelpermanager")->get("HeadLink");
        $headLink->appendStylesheet("//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css");
        $headLink->appendStylesheet("//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.2/css/bootstrap-select.min.css");
        $this->getServiceLocator()->get("navigation/admin")->findOneById("home-page-manage")->setActive(true);

        $viewModel = new ViewModel();
        $language = new Language();
        $linkTypeModel = new LinkTypeModel();
        $iconModel = new IconModel();
        $fieldCheck = new FieldCheck();

        $status = true;

        try {
            $fieldCheck->checkBoolean($this->params()->fromQuery("status"));
        } catch (\Exception $exception) {
            $status = false;
        }

        $viewModel->setVariable("types", $linkTypeModel->all());
        $viewModel->setVariable("languageList", $language->listLanguage());
        $viewModel->setVariable("icons", $iconModel->all());
        $viewModel->setVariable("status", $status);
        return $viewModel;
    }

    public function updateAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->getResponse()->setStatausCode(404);
        }

        $linkTypeModel = new LinkTypeModel();
        $linkTypeModel->updateAll($this->params()->fromPost());

        // redirect to list view
        // damn, why zf2's redirect is so complex. Whyyyyyyyyyyyy!?
        return $this->redirect()->toUrl(
            $this->getServiceLocator()->get("viewhelpermanager")->get("Url")->__invoke("admin/default", array(
                "controller" => "link-types", "action" => "index"), array(
                    "query" => array(
                        "status" => true
                    )
                )
            )
        );
    }
}
?>