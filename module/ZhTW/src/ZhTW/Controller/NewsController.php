<?php

namespace ZhTW\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Tool\Page\Paginator;
use Tool\Check\FieldCheck;
use Application\Model\ArticleModel;
use Application\Model\Language;

class NewsController extends AbstractActionController
{
    public function detailAction()
    {
        $basePath = $this->getServiceLocator()->get("viewhelpermanager")->get("BasePath");
        $this->getServiceLocator()->get("viewhelpermanager")->get("HeadScript")->appendFile($basePath->__invoke() . "/js/stretch-image.js");
        $viewModel = new ViewModel();
        $fieldCheck = new FieldCheck();
        $articleModel = new ArticleModel();
        $languageModel = new Language();
        
        $id = null;
        $time = date("Y / m / d");
        $title = "";
        $content = "";
        $typeId = null;
        $downloads = array();
        $images = array();
        
        try {
            $id = $fieldCheck->checkInput($this->params()->fromQuery("id"));

            $article = $articleModel->getArticleByIdAndIsVisible($id, true);
            
            $typeId = $article["type_id"];
            $time = date("Y / m / d", strtotime($article["create_time"]));
            $title = $article["title"];
            $content = $article["content"];
            
            $downloads = $articleModel->listArticleDownloadByArticleId($id);
            $images = $articleModel->listArticleImageByArticleId($id);
        } catch (\Exception $exception) {
            $this->getResponse()->setStatusCode(404);
        }
        
        $viewModel->setVariable("articleId", $id);
        $viewModel->setVariable("articleTypes", $articleModel->listType($languageModel->getLanguageIdByShortCut("zh_TW")));
        $viewModel->setVariable("typeId", $typeId);
        $viewModel->setVariable("time", $time);
        $viewModel->setVariable("title", $title);
        $viewModel->setVariable("content", $content);
        $viewModel->setVariable("downloads", $downloads);
        $viewModel->setVariable("images", $images);
        
        return $viewModel;
    }
    
    public function indexAction()
    {
        $basePath = $this->getServiceLocator()->get("viewhelpermanager")->get("BasePath");
        $this->getServiceLocator()->get("viewhelpermanager")->get("HeadScript")->appendFile($basePath->__invoke() . "/js/search-highlight.js");
        
        $onePageDisplay = 10;
        $viewModel = new ViewModel();
        $articleModel = new ArticleModel($onePageDisplay);
        $languageModel = new Language();
        $fieldCheck = new FieldCheck();
        
        $typeId = null;
        $term = null;
        $page = $fieldCheck->checkPage($this->params()->fromQuery("page"));
        $languageId = $languageModel->getLanguageIdByShortCut("zh_TW");
        
        try {
            $typeId = $fieldCheck->checkInput($this->params()->fromQuery("type"));
        } catch (\Exception $exception) {
        }
        
        try {
            $term = $fieldCheck->checkInput($this->params()->fromQuery("term"));
        } catch (\Exception $exception) {
        }
        
        $articleArray = $articleModel->listArticle($page, $languageId, $term, $typeId, true);
        $articles = array();
        
        foreach ($articleArray[1] as $i => $article) {
            $articleImages = $articleModel->listArticleImageByArticleId($article["id"]);
            $articleContent = strip_tags($article["content"]);
            
            if (mb_strlen($articleContent, "UTF-8") > 140) {
                $articleContent = mb_substr($articleContent, 0, 137, "UTF-8") . "...";
            }
            $articles[$i] = array(
                "id" => $article["id"],
                "title" => $article["title"],
                "content" => $articleContent,
                "time" => date("Y / m / d", strtotime($article["create_time"])),
                "title_image" => $articleImages[0]["image_name"],
            );
        }
        
        $viewModel->setVariable("pagination", Paginator::factory($page, $onePageDisplay, $articleArray[0]));
        $viewModel->setVariable("term", $term);
        $viewModel->setVariable("articles", $articles);
        $viewModel->setVariable("articleTypes", $articleModel->listType($languageId));
        $viewModel->setVariable("typeId", $typeId);
        
        return $viewModel;
    }
}