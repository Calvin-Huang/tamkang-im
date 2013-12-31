<?php
namespace EnUS\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\InstituteModel;
use Application\Model\Language;
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
        $languageModel = new Language();
        $fieldCheck = new FieldCheck();
        $types = $instituteModel->listIntroduceType($languageModel->getLanguageIdByShortCut("en_US"));
        $typeId = $types[0]["id"];
        $content = "";
        
        try {
            $typeId = $fieldCheck->checkInput($this->params()->fromQuery("type")); 
        } catch (\Exception $exception) {
        }
        
        $introduce = $instituteModel->getIntroduceByTypeId($typeId);
        
        $viewModel->setVariable("typeId", $typeId);
        $viewModel->setVariable("instituteTypes", $types);
        $viewModel->setVariable("content", $introduce["content"]);
        RETURN $viewModel;
    }
}