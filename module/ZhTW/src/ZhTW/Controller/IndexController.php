<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonZhTW for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZhTW\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\ArticleModel;
use Application\Model\IndexSlideModel;
use Application\Model\Language;
use Application\Model\IconModel;
use Application\Model\LinkModel;
use Application\Model\LinkTypeModel;
use Zend\View\Model\JsonModel;
use Tool\Check\FieldCheck;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $viewModel = new ViewModel();
        $articleModel = new ArticleModel();
        $indexSlideModel = new IndexSlideModel();
        $linkTypeModel = new LinkTypeModel();
        $linkModel = new LinkModel();
        $iconModel = new IconModel();
        $languageModel = new Language();
        $fieldCheck = new FieldCheck();
        
        $currentPage = $fieldCheck->checkPage($this->params()->fromPost("page"));
        
        $articleArray = $articleModel->listArticle($currentPage, $languageModel->getLanguageIdByShortCut("zh_TW"), null, null, true);
        $articles = array();
        $downloads = array();
        $slides = array();
        $slides = $indexSlideModel->listIndexSlide();

        
        foreach ($articleArray[1] as $i => $article) {
            $articleTitle = strip_tags($article["title"]);
            $articleContent = strip_tags($article["content"]);
            
            if (mb_strlen($articleTitle, "UTF-8") > 37) {
                $articleTitle = mb_substr($articleTitle, 0, 34, "UTF-8") . "...";
            }
            
            if (mb_strlen($articleContent, "UTF-8") > 55) {
                $articleContent = mb_substr($articleContent, 0, 52, "UTF-8") . "...";
            }
            
            $articles[$i] = array(
                "id" => $article["id"],
                "title" => $articleTitle,
                "content" => $articleContent,
                "time" => $article["create_time"],
                "top" => $article["top"]
            );
        }
        
        for ($i = 0; $i < count($slides) && isset($slides[$i]["content"]); $i++) {
            $slides[$i]["content"] = str_replace("\n", "<br/>", $slides[$i]["content"]);
        }
        
        // if infinity scroll get value, return json array and stop application
        if ($this->getRequest()->isPost()) {
            $viewModel->setTemplate("zh-tw/index/index.template");
            $viewModel->setTerminal(true);
        } else {
            $basePath = $this->getServiceLocator()->get("viewhelpermanager")->get("BasePath");
            $headScript = $this->getServiceLocator()->get("viewhelpermanager")->get("HeadScript");
            $headScript->appendFile($basePath->__invoke() . "/js/infinity-scroll.js");
        }
        
        // combine link_type, link, icon to one array
        $linkTypes = $linkTypeModel->all();
        foreach ($linkTypes as $i => $linkType) {

            // step 1. find all links under linkTypes
            $linkTypes[$i]["links"] = $linkModel->findByLinkTypeId($linkType["id"]);

            // step 2. add icon name to linkTypes
            $linkTypes[$i]["icon"] = $iconModel->find($linkType["icon_id"]);;
        }

        $viewModel->setVariable("articles", $articles);
        $viewModel->setVariable("slides", $slides);
        $viewModel->setVariable("linkTypes", $linkTypes);
        
        return $viewModel;
    }
    
    public function testAction()
    {
        return new ViewModel();
    }
}
