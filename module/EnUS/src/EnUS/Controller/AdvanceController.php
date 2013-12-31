<?php
namespace EnUS\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\AdvanceModel;
use Application\Model\Language;
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
        $languageModel = new Language();
        $fieldCheck = new FieldCheck();
        
        $types = $advanceModel->listIntroduceType($languageModel->getLanguageIdByShortCut("en_US"));
        $typeId = $types[0]["id"];
        $content = "";
        
        try {
            $typeId = $fieldCheck->checkInput($this->params()->fromQuery("type")); 
        } catch (\Exception $exception) {
        }
        
        $introduce = $advanceModel->getIntroduceByTypeId($typeId);
        
        $viewModel->setVariable("typeId", $typeId);
        $viewModel->setVariable("advanceTypes", $types);
        $viewModel->setVariable("content", $introduce["content"]);
        RETURN $viewModel;
    }
}