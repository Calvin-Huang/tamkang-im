<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\InstituteModel;
use Tool\Check\FieldCheck;

/**
 * IntituteController
 *
 * @author
 *
 * @version
 *
 */
class InstituteController extends AbstractActionController
{

    /**
     * The default action - show the home page
     */
    public function indexAction ()
    {
        $viewModel = new ViewModel();
        $instituteModel = new InstituteModel();
        $fieldCheck = new FieldCheck();
        
        $typeId = 1;
        $content = "";
        
        try {
            $typeId = $fieldCheck->checkInput($this->params()->fromQuery("type")); 
        } catch (\Exception $exception) {
        }
        
        $introduce = $instituteModel->getIntroduceByTypeId($typeId);
        
        $viewModel->setVariable("typeId", $typeId);
        $viewModel->setVariable("instituteTypes", $instituteModel->listIntroduceType());
        $viewModel->setVariable("content", $introduce["content"]);
        RETURN $viewModel;
    }
}