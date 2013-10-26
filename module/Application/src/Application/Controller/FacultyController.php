<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\FacultyModel;
use Tool\Page\Paginator;
use Tool\Check\FieldCheck;

/**
 * TeacherController
 *
 * @author
 *
 * @version
 *
 */
class FacultyController extends AbstractActionController
{

    /**
     * The default action - show the home page
     */
    public function indexAction ()
    {
        $basePath = $this->getServiceLocator()->get("viewhelpermanager")->get("BasePath");
        $headScript = $this->getServiceLocator()->get("viewhelpermanager")->get("HeadScript");
        $headScript->appendFile($basePath->__invoke() . "/js/holder.js");
        
        $viewModel = new ViewModel();
        $facultyModel = new FacultyModel();
        $fieldCheck = new FieldCheck();
        $id = null;
        
        $currentPage = $fieldCheck->checkPage($this->params()->fromQuery("page"));
        
        try {
            $id = $fieldCheck->checkInput($this->params()->fromQuery("title"));
        } catch (\Exception $exception) {
            
        }
        
        $result = $facultyModel->listTeacherByTitleIdAndPage($id, $currentPage);
        $faculties = $result[1];
        
        // 放入教師其他職稱
        for ($i = 0; $i < count($faculties); $i++) {
            $othertitle = $facultyModel->listOthertitleByTeacherId($faculties[$i]["id"]);
            $faculties[$i]["other_title"] = $othertitle;
        }
        
        $paginator = Paginator::factory($currentPage, 20, $result[0]);
        $viewModel->setVariable("titleId", $id);
        $viewModel->setVariable("facultyTitles", $facultyModel->listTitle());
        $viewModel->setVariable("faculties", $faculties);
        $viewModel->setVariable("paginator", $paginator);
        return $viewModel;
    }
    
    public function profileAction()
    {
        $basePath = $this->getServiceLocator()->get("viewhelpermanager")->get("BasePath");
        $headScript = $this->getServiceLocator()->get("viewhelpermanager")->get("HeadScript");
        $headScript->appendFile($basePath->__invoke() . "/js/holder.js");
        
        $this->getServiceLocator()->get("navigation/default")->findOneById("faculty")->setActive(true);
        
        $viewModel = new ViewModel();
        $fieldCheck = new FieldCheck();
        
        try {
            $facultyId = $fieldCheck->checkInput($this->params()->fromQuery("id"));
            
            $facultyModel = new FacultyModel();
            $faculty = $facultyModel->getTeacherById($facultyId);
            
            $profileTypes = $facultyModel->listProfileType();
            $profiles = array();
            
            foreach ($profileTypes as $i => $profileType) {
                $profile = $facultyModel->listProfileByTeacherIdAndTypeId($facultyId, $profileType["id"]);
                if (count($profile) > 0) {
                    $profiles[$profileType["id"]] = $profile;
                } else {
                    unset($profileTypes[$i]);
                }
            }
            
            $bookTypeList = $facultyModel->listBookType();
            $bookList = array();
            
            foreach ($bookTypeList as $key => $value) {
                $bookList[$value["id"]] = $facultyModel->listBookByTeacherIdAndTypeId($facultyId, $value["id"]);
            }
            
            $viewModel->setVariable("faculty", $faculty);
            $viewModel->setVariable("othertitles", $facultyModel->listOthertitleByTeacherId($facultyId));
            
            $viewModel->setVariable("profileTypes", $profileTypes);
            $viewModel->setVariable("profiles", $profiles);
            
            $viewModel->setVariable("bookTypeList", $bookTypeList);
            $viewModel->setVariable("bookList", $bookList);
        } catch (\Exception $exception) {
            $this->getResponse()->setStatusCode(404);
        }
        
        return $viewModel;
    }
}