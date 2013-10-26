<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Http\Response\Stream;
use Zend\View\Model\ViewModel;
use Application\Model\FileModel;
use Tool\Check\FieldCheck;
use Admin\Model\ArticleModel;
use Tool\Page\Paginator;

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

    /**
     * The default action - show the home page
     */
    public function indexAction ()
    {
        $fileName = $_SERVER["QUERY_STRING"];
        
        $files = glob(realpath(__DIR__ . "/../../../../../data/upload") . "/" . $fileName . ".{doc,docx,xls,xlsx,pdf,txt,zip,7z}", GLOB_BRACE);
        
        // 找不到檔案就不進行下載
        if (count($files) <= 0) {
            $this->getResponse()->setStatusCode(404);
            exit();
        }
        
        $fileModel = new FileModel();
        
        $response = new Stream();
        $response->getHeaders()->addHeaders(array(
            "Content-Disposition" => "attachment; filename=" . $fileModel->getDownloadNameByFileName($fileName),
            "Content-Type" => "application/force-downlaod",
            "Content-Type" => "applicationo/octet-stream",
            "Content-Description" => "File Transfer",
            "Content-Length" => filesize($files[0]),
        ));
        
        $response->setStream(fopen($files[0], "r"));
        return $response;
    }
    
    public function packageAction()
    {
        $fieldCheck = new FieldCheck();
        try {
            $articleId = $fieldCheck->checkInput($this->params()->fromQuery("article_id"));
            
            $articleModel = new ArticleModel();
            $articleDownloads = $articleModel->listArticleDownloadByArticleId($articleId);
            
            foreach ($articleDownloads as $i => $articleDownload) {
                $files = glob(realpath(__DIR__ . "/../../../../../data/upload") . "/" . $articleDownload["file_name"] . ".{doc,docx,xls,xlsx,pdf,txt,zip,7z}", GLOB_BRACE);
                
                if ($files > 0) {
                    $articleDownloads[$i]["file_name"] = $files[0];
                }
            }
            
        } catch (\Exception $exception) {
            $this->getResponse()->setStatusCode(404);
        }
    }
    
    public function searchAction()
    {
        $basePath = $this->getServiceLocator()->get("viewhelpermanager")->get("BasePath");
        $this->getServiceLocator()->get("viewhelpermanager")->get("HeadScript")->appendFile($basePath->__invoke() . "/js/search-highlight.js");
        $this->getServiceLocator()->get("navigation/default")->findOneById("news")->setActive(true);
        
        $onePageQuantity = 20;
        $viewModel = new ViewModel();
        $articleModel = new ArticleModel();
        $fieldCheck = new FieldCheck();
        $fileModel = new FileModel($onePageQuantity);
        
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
                for ($j = 0; $j < count($downloads); $j++) {
                    if ($similars[$i]["id"] == $downloads[$j]["id"]) {
                        unset($similars[$i]);
                        break;
                    }
                }
            }
        }
        
        $viewModel->setVariable("articleTypes", $articleModel->listType());
        $viewModel->setVariable("typeId", $typeId);
        $viewModel->setVariable("term", $term);
        $viewModel->setVariable("downloads", $downloads[1]);
        $viewModel->setVariable("similars", $similars);
        $viewModel->setVariable("pagination", Paginator::factory($page, $onePageQuantity, $downloads[0]));
        return $viewModel;
    }
}