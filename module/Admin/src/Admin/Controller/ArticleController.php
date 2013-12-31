<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\ArticleModel;
use Tool\Check\FieldCheck;
use Tool\Check\Exception\FieldEmptyException;
use Tool\Page\Paginator;
use Admin\Model\FileHandler;
use Admin\Model\Language;
use Tool\File\Exception\InvalidFileFormatException;
use Tool\File\Exception\UploadFailedException;

class ArticleController extends AbstractActionController
{
    public function __construct()
    {
        
    }
    
    public function addAction()
    {
        $articleModel = new ArticleModel();
        $viewModel = new ViewModel();
        $fieldCheck = new FieldCheck();
        
        if ($this->getRequest()->isPost()) {
            $fileHandler = new FileHandler();
            
            try {
                $fieldCheck->checkToken($this->params()->fromPost("t"));
                
                $title = $fieldCheck->checkInput($this->params()->fromPost("title"));
                $content = $fieldCheck->checkEditor($this->params()->fromPost("content"));
                $typeId = $fieldCheck->checkInput($this->params()->fromPost("type"));
                
                $articleId = $articleModel->addArticle($title, $content, $typeId);

                // 圖檔上傳
                try {
                    $images = $fieldCheck->checkArray($this->params()->fromFiles("image"));
                    foreach ($images as $i => $image) {
                    
                        $image = $fieldCheck->checkFile($image);
                        $imageName = $fileHandler->addImage($image["name"], $image["tmp_name"]);
                    
                        $articleModel->addArticleImage($articleId, $imageName);
                    }
                } catch (FieldEmptyException $exception) {
                }

                // 文件上傳
                try {
                    $documents = $fieldCheck->checkArray($this->params()->fromFiles("document"));
                    foreach ($documents as $i => $document) {
                    
                        $document = $fieldCheck->checkFile($document);
                        $names = $fileHandler->addFile($document["name"], $document["tmp_name"]);
                        
                        $articleModel->addArticleDownload($articleId, $names["fileName"], $names["downloadName"]);
                    }
                } catch (FieldEmptyException $exception) {
                }
            } catch (UploadFailedException $exception) {
                echo $exception->getMessage();
            } catch (InvalidFileFormatException $exception) {
                echo $exception->getMessage();
            } catch (\Exception $exception) {
                $this->getResponse()->setStatusCode(403);
            }
            exit();
        } else {
            $viewModel->setVariable("typeList", $articleModel->listType());
            $viewModel->setVariable("token", $fieldCheck->createToken("tamkang-im"));
        }
        
        $viewModel->setTerminal(true);
        return $viewModel;
    }
    
    public function articletypeAction()
    {
        $basePath = $this->getServiceLocator()->get("viewhelpermanager")->get("BasePath");
        $headScript = $this->getServiceLocator()->get("viewhelpermanager")->get("HeadScript");
        $headScript->appendFile($basePath->__invoke() . "/js/append-type-field.js");
        
        $articleModel = new ArticleModel();
        $language = new Language();
        $viewModel = new ViewModel();
        $isSuccess = false;
        
        if ($this->getRequest()->isPost()) {
            $fieldCheck = new FieldCheck();
            $types = $fieldCheck->checkArray($this->params()->fromPost("type"));
            $languages = $fieldCheck->checkArray($this->params()->fromPost("language"));
            
            if ($articleModel->setArticletype($types, $languages)) {
                $isSuccess = true;
            }
        }
        
        $viewModel->setVariable("articletypeList", $articleModel->listType());
        $viewModel->setVariable("languageList", $language->listLanguage());
        $viewModel->setVariable("saveSuccess", $isSuccess);
        return $viewModel;
    }
    
    public function deleteAction()
    {
        $fieldCheck = new FieldCheck();
        $id = null;
        
        try {
            $id = $fieldCheck->checkInput($this->params()->fromQuery("id"));
            $articleModel = new ArticleModel();
        
            // 進行完動作之後不render view
            if ($this->getRequest()->isPost()) {
                $fieldCheck->checkToken($this->params()->fromPost("t"));
                
                $images = $articleModel->listArticleImageByArticleId($id);
                $downloads = $articleModel->listArticleDownloadByArticleId($id);
                
                $articleModel->deleteArticleImageByArticleId($id);
                $articleModel->deleteArticleDownloadByArticleId($id);
                $articleModel->deleteArticleById($id);
                
                try {
                    $images = $fieldCheck->checkArray($images);
                    $fileHandler = new FileHandler();
                    foreach ($images as $i => $value) {
                        $fileHandler->deleteImage($value["image_name"]);
                    }
                } catch (\Exception $exception) {
                }
                
                try {
                    $downloads = $fieldCheck->checkArray($downloads);
                    $fileHandler = new FileHandler();
                    foreach ($downloads as $i => $value) {
                        $fileHandler->deleteFile($value["file_name"]);
                    }
                } catch (\Exception $exception) {
                }
                exit();
            }
        } catch (\Exception $exception) {
            $this->getResponse()->setStatusCode(403);
        }
        
        $viewModel = new ViewModel(array(
                "token" => $fieldCheck->createToken("tamkang-im"),
                "id" => $id,
        ));
        $viewModel->setTerminal(true);
        
        return $viewModel;
    }
    
    public function editAction()
    {
        $fieldCheck = new FieldCheck();
        $articleModel = null;
        $id = null;
        $resetCookie = true;
        
        try {
            $id = $fieldCheck->checkInput($this->params()->fromQuery("id"));
            $articleModel = new ArticleModel();
            
            $article = $articleModel->getArticleById($id);
            if ($this->getRequest()->isPost()) {
                $fileHandler = new FileHandler();
                
                $fieldCheck->checkToken($this->params()->fromPost("t"));
                
                $title = $fieldCheck->checkInput($this->params()->fromPost("title"));
                $content = $fieldCheck->checkEditor($this->params()->fromPost("content"));
                $typeId = $fieldCheck->checkInput($this->params()->fromPost("type"));
                $visible = $fieldCheck->checkBoolean($this->params()->fromPost("visible"));

                $articleModel->updateArticleById($id, $title, $content, $typeId, $visible);
                
                // 圖檔上傳
                try {
                    $images = $fieldCheck->checkArray($this->params()->fromFiles("image"));
                    foreach ($images as $i => $image) {
                    
                        $image = $fieldCheck->checkFile($image);
                        $imageName = $fileHandler->addImage($image["name"], $image["tmp_name"]);
                    
                        $articleModel->addArticleImage($id, $imageName);
                    }
                } catch (FieldEmptyException $exception) {
                }

                // 文件上傳
                try {
                    $documents = $fieldCheck->checkArray($this->params()->fromFiles("document"));
                    foreach ($documents as $i => $document) {
                    
                        $document = $fieldCheck->checkFile($document);
                        $names = $fileHandler->addFile($document["name"], $document["tmp_name"]);
                    
                        $articleModel->addArticleDownload($id, $names["fileName"], $names["downloadName"]);
                    }
                } catch (FieldEmptyException $exception) {
                }
                
                // 刪除圖片
                try {
                    $deleteImages = json_decode($fieldCheck->checkInput($this->params()->fromPost("delete_images")));
                    foreach ($deleteImages as $i => $value) {
                        $imageName = $articleModel->getArticleImageById($value);
                        $fileHandler->deleteImage($imageName);
                        $articleModel->deleteArticleImageById($value);
                    }
                } catch (\Exception $exception) {
                }
                
                // 刪除附件
                try {
                    $deleteDownloads = json_decode($fieldCheck->checkInput($this->params()->fromPost("delete_downloads")));
                    foreach ($deleteDownloads as $i => $value) {
                        $fileName = $articleModel->getArticleDownloadById($value);
                        $fileHandler->deleteFile($fileName);
                        $articleModel->deleteArticleDownloadById($value);
                    }
                } catch (\Exception $exception) {
                }
                exit();
            }
        } catch (UploadFailedException $exception) {
            echo $exception->getMessage();
            exit();
        } catch (InvalidFileFormatException $exception) {
            echo $exception->getMessage();
            exit();
        } catch (\Exception $exception) {
            $this->getResponse()->setStatusCode(403);
        }
        
        $viewModel = new ViewModel(array(
            "typeList" => $articleModel->listType(),
            "imageList" => $articleModel->listArticleImageByArticleId($id),
            "downloadList" => $articleModel->listArticleDownloadByArticleId($id),
            "title" => $article["title"],
            "content" => $article["content"],
            "typeId" => $article["type_id"],
            "id" => $id,
            "visible" => $article["visible"],
            "token" => $fieldCheck->createToken("tamkang-im"),
            "resetCookie" => $resetCookie,
        ));
        
        $viewModel->setTerminal(true);
        return $viewModel;
    }
    
    public function indexAction()
    {
        $basePath = $this->getServiceLocator()->get("viewhelpermanager")->get("BasePath");
        $headScript = $this->getServiceLocator()->get("viewhelpermanager")->get("HeadScript");
        $headScript->appendFile($basePath->__invoke() . "/ckeditor/ckeditor.js");
        
        $fieldCheck = new FieldCheck();
        $onePageDisplay = 20;
        $currentPage = $fieldCheck->checkPage($this->params()->fromQuery("page"));
        $term = null;
        $visible = null;
        
        try {
            $term = $fieldCheck->checkInput($this->params()->fromQuery("term"));
        } catch (\Exception $exception) {
        }
        
        try {
            $visible = $fieldCheck->checkInput($this->params()->fromQuery("visible"));
        } catch (\Exception $exception) {
        }
        
        $articleModel = new ArticleModel($onePageDisplay);
        $articleList = $articleModel->listArticle($currentPage, $term, null, $visible);
        
        // 簡化處理輸出
        for ($i = 0; $i < count($articleList[1]); $i++) {
            $title = strip_tags($articleList[1][$i]["title"]);
            $content = strip_tags($articleList[1][$i]["content"]);
            if (mb_strlen($content, "UTF-8") > 57) {
                $content = mb_substr($content, 0, 54, "UTF-8") . "...";
            }
            
            $articleList[1][$i]["title"] = $title;
            $articleList[1][$i]["content"] = $content;
        }
        
        return new ViewModel(array(
            "articleList" => $articleList[1],
            "paginator" => Paginator::factory($currentPage, $onePageDisplay, $articleList[0]),
            "term" => $term,
            "visible" => $visible,
        ));
    }
    
    public function setVisibleAction()
    {
        return new ViewModel();
    }
}