<?php
namespace ZhTW\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\AdvanceModel;
use Tool\Check\FieldCheck;
use Application\Model\Language;

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
        $languageModel = new Language();
        
        $types = $advanceModel->listIntroduceType($languageModel->getLanguageIdByShortCut("zh_TW"));
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