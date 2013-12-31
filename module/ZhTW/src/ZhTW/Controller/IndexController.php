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

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $basePath = $this->getServiceLocator()->get("viewhelpermanager")->get("BasePath");
        $headScript = $this->getServiceLocator()->get("viewhelpermanager")->get("HeadScript");
        $headScript->appendFile($basePath->__invoke() . "/js/rubber-banding.js");
        
        $viewModel = new ViewModel();
        $articleModel = new ArticleModel();
        $indexSlideModel = new IndexSlideModel();
        $languageModel = new Language();
        
        $articleArray = $articleModel->listArticle(1, $languageModel->getLanguageIdByShortCut("zh_TW"), null, null, true);
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
            );
        }
        
        for ($i = 0; $i < count($slides); $i++) {
            $slides[$i]["content"] = str_replace("\n", "<br/>", $slides[$i]["content"]);
        }
        
        $viewModel->setVariable("articles", $articles);
        $viewModel->setVariable("slides", $slides);
        
        return $viewModel;
    }
    
    public function testAction()
    {
        return new ViewModel();
    }
}
