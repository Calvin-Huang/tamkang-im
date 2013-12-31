<?php
namespace ZhTW\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\CollectModel;
use Tool\Check\FieldCheck;
use Application\Model\Language;

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
        $languageModel = new Language();
        
        $types = $collectModel->listIntroduceType($languageModel->getLanguageIdByShortCut("zh_TW"));
        $typeId = $types[0]["id"];
        $content = "";
        
        try {
            $typeId = $fieldCheck->checkInput($this->params()->fromQuery("type")); 
        } catch (\Exception $exception) {
        }
        
        $introduce = $collectModel->getIntroduceByTypeId($typeId);
        
        $viewModel->setVariable("typeId", $typeId);
        $viewModel->setVariable("collectTypes", $types);
        $viewModel->setVariable("content", $introduce["content"]);
        RETURN $viewModel;
    }
}