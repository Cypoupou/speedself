<?php

class FooterController extends Zend_Controller_Action
{

    public function init()
    {
        $auth = Zend_Auth::getInstance();

        //If user is not connected, redirect to logout
        if (!$auth->hasIdentity()) {
            $this->_helper->redirector('logout', 'auth', null, array('reason' => '1')); // back to login page
        }

        ini_set("memory_limit",'2048M');
        ini_set('max_execution_time', 300);
    }

    public function aideAction(){
        
    }
    
    public function getdocumentAction(){
        $pathFile = "../docs/Aide-memoireSCO.pdf";
        header("Content-Type: application/pdf");
        header("Content-disposition: attachment; filename=Aide-memoireSCO.pdf");
        readfile($pathFile);
        die();
        
    }
    
    public function contactAction(){
        
    }
}
