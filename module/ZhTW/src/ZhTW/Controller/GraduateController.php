<?php
namespace ZhTW\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\InstituteModel;
use Tool\Check\FieldCheck;
use Application\Model\Language;

/**
 * IntituteController
 *
 * @author
 *
 * @version
 *
 */
class GraduateController extends AbstractActionController
{

    /**
     * The default action - show the home page
     */
    public function indexAction ()
    {
        $viewModel = new ViewModel();
        $instituteModel = new InstituteModel();
        $fieldCheck = new FieldCheck();
        $languageModel = new Language();
        
        $types = $instituteModel->listIntroduceType($languageModel->getLanguageIdByShortCut("zh_TW"));
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