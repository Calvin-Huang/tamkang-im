<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\AdvanceModel;
use Tool\Check\FieldCheck;

/**
 * AdvanceController
 *
 * @author
 *
 * @version
 *
 */
class AdvanceController extends AbstractActionController
{

    /**
     * The default action - show the home page
     */
    public function indexAction ()
    {
        $viewModel = new ViewModel();
        $advanceModel = new AdvanceModel();
        $fieldCheck = new FieldCheck();
        
        $typeId = 1;
        $content = "";
        
        try {
            $typeId = $fieldCheck->checkInput($this->params()->fromQuery("type")); 
        } catch (\Exception $exception) {
        }
        
        $introduce = $advanceModel->getIntroduceByTypeId($typeId);
        
        $viewModel->setVariable("typeId", $typeId);
        $viewModel->setVariable("advanceTypes", $advanceModel->listIntroduceType());
        $viewModel->setVariable("content", $introduce["content"]);
        RETURN $viewModel;
    }
}