<?php
namespace EnUS\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\AdmissionModel;
use Tool\Check\FieldCheck;
use Application\Model\Language;

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
        $languageMpodel = new Language();
        $fieldCheck = new FieldCheck();
        
        $types = $admissionModel->listIntroduceType($languageMpodel->getLanguageIdByShortCut("en_US"));
        $typeId = $types[0]["id"];
        $content = "";
        
        try {
            $typeId = $fieldCheck->checkInput($this->params()->fromQuery("type")); 
        } catch (\Exception $exception) {
        }
        
        $introduce = $admissionModel->getIntroduceByTypeId($typeId);
        
        $viewModel->setVariable("typeId", $typeId);
        $viewModel->setVariable("admissionTypes", $types);
        $viewModel->setVariable("content", $introduce["content"]);
        RETURN $viewModel;
    }
}