<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\AdmissionModel;
use Tool\Check\FieldCheck;

/**
 * AdmissionController
 *
 * @author
 *
 * @version
 *
 */
class AdmissionController extends AbstractActionController
{

    /**
     * The default action - show the home page
     */
    public function indexAction ()
    {
        $viewModel = new ViewModel();
        $admissionModel = new AdmissionModel();
        $fieldCheck = new FieldCheck();
        
        $typeId = 1;
        $content = "";
        
        try {
            $typeId = $fieldCheck->checkInput($this->params()->fromQuery("type")); 
        } catch (\Exception $exception) {
        }
        
        $introduce = $admissionModel->getIntroduceByTypeId($typeId);
        
        $viewModel->setVariable("typeId", $typeId);
        $viewModel->setVariable("admissionTypes", $admissionModel->listIntroduceType());
        $viewModel->setVariable("content", $introduce["content"]);
        RETURN $viewModel;
    }
}