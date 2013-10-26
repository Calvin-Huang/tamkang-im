<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\CollectModel;
use Tool\Check\FieldCheck;

/**
 * CollectController
 *
 * @author
 *
 * @version
 *
 */
class CollectController extends AbstractActionController
{

    /**
     * The default action - show the home page
     */
    public function indexAction ()
    {
        $viewModel = new ViewModel();
        $collectModel = new CollectModel();
        $fieldCheck = new FieldCheck();
        
        $typeId = 1;
        $content = "";
        
        try {
            $typeId = $fieldCheck->checkInput($this->params()->fromQuery("type")); 
        } catch (\Exception $exception) {
        }
        
        $introduce = $collectModel->getIntroduceByTypeId($typeId);
        
        $viewModel->setVariable("typeId", $typeId);
        $viewModel->setVariable("collectTypes", $collectModel->listIntroduceType());
        $viewModel->setVariable("content", $introduce["content"]);
        RETURN $viewModel;
    }
}