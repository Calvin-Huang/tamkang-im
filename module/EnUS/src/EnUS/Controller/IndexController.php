<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonZhTW for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace EnUS\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\ArticleModel;
use Application\Model\IndexSlideModel;
use Application\Model\Language;
use Tool\Check\FieldCheck;

class IndexController extends AbstractActionController
{
public function indexAction()
    {
        $viewModel = new ViewModel();
        $articleModel = new ArticleModel();
        $indexSlideModel = new IndexSlideModel();
        $languageModel = new Language();
        $fieldCheck = new FieldCheck();
        
        $currentPage = $fieldCheck->checkPage($this->params()->fromPost("page"));
        
        $articleArray = $articleModel->listArticle($currentPage, $languageModel->getLanguageIdByShortCut("en_US"), null, null, true);
        $articles = array();
        $downloads = array();
        $slides = array();
        $slides = $indexSlideModel->listIndexSlide();
        
        foreach ($articleArray[1] as $i => $article) {
            $articleImages = $articleModel->listArticleImageByArticleId($article["id"]);
            $articleContent = strip_tags($article["content"]);
            
            if (mb_strlen($articleContent, "UTF-8") > 170) {
                $articleContent = mb_substr($articleContent, 0, 167, "UTF-8") . "...";
            }
            
            $articles[$i] = array(
                "id" => $article["id"],
                "title" => $article["title"],
                "content" => $articleContent,
                "time" => $article["create_time"],
                "title_image" => $articleImages[0]["image_name"],
                "top" => $article["top"]
            );
        }
        
        for ($i = 0; $i < count($slides); $i++) {
            $slides[$i]["content"] = str_replace("\n", "<br/>", $slides[$i]["content"]);
        }
        
        // if infinity scroll get value, return json array and stop application
        if ($this->getRequest()->isPost()) {
            $viewModel->setTemplate("en-us/index/index.template");
            $viewModel->setTerminal(true);
        } else {
            $basePath = $this->getServiceLocator()->get("viewhelpermanager")->get("BasePath");
            $headScript = $this->getServiceLocator()->get("viewhelpermanager")->get("HeadScript");
            $headScript->appendFile($basePath->__invoke() . "/js/infinity-scroll.js");
        }
        
        $viewModel->setVariable("articles", $articles);
        $viewModel->setVariable("slides", $slides);
        
        return $viewModel;
    }
}
