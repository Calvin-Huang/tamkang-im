<?php
namespace ZhTW\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\FileModel;
use Tool\Check\FieldCheck;
use Application\Model\ArticleModel;
use Tool\Page\Paginator;
use Application\Model\Language;

/**
 * FileController
 *
 * @author
 *
 * @version
 *
 */
class FileController extends AbstractActionController
{    
    public function searchAction()
    {
        $basePath = $this->getServiceLocator()->get("viewhelpermanager")->get("BasePath");
        $this->getServiceLocator()->get("viewhelpermanager")->get("HeadScript")->appendFile($basePath->__invoke() . "/js/search-highlight.js");
        $this->getServiceLocator()->get("navigation/default")->findOneById("news")->setActive(true);
        
        $onePageQuantity = 20;
        $viewModel = new ViewModel();
        $articleModel = new ArticleModel();
        $fieldCheck = new FieldCheck();
        $languageModel = new Language();
        $fileModel = new FileModel($onePageQuantity);
        $languageId = $languageModel->getLanguageIdByShortCut("zh_TW");
        
        $typeId = null;
        $term = null;
        $page = $fieldCheck->checkPage($this->params()->fromQuery("page"));
        
        try {
            $typeId = $fieldCheck->checkInput($this->params()->fromQuery("type"));
        } catch (\Exception $exception) {
        }
        
        try {
            $term = $fieldCheck->checkInput($this->params()->fromQuery("term"));
        } catch (\Exception $exception) {
        }
        
        $downloads = $fileModel->listDownloadByTypeIdAndTerm($page, $typeId, $term);
        
        $similars = array();
        if (isset($term) && $term != "") {
            $similars = $fileModel->listDownloadByTitle($term);
            for ($i = 0; $i < count($similars); $i++) {
                for ($j = 0; $j < count($downloads[1]); $j++) {
                    if ($similars[$i]["id"] == $downloads[1][$j]["id"]) {
                        unset($similars[$i]);
                        break;
                    }
                }
            }
        }
        
        $viewModel->setVariable("articleTypes", $articleModel->listType($languageId));
        $viewModel->setVariable("typeId", $typeId);
        $viewModel->setVariable("term", $term);
        $viewModel->setVariable("downloads", $downloads[1]);
        $viewModel->setVariable("similars", $similars);
        $viewModel->setVariable("pagination", Paginator::factory($page, $onePageQuantity, $downloads[0]));
        return $viewModel;
    }
}