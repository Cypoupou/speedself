<?php

/**
 * UserController
 * 
 * @author
 * @version 
 */
require_once 'Zend/Controller/Action.php';

class UserController extends Zend_Controller_Action
{
    /**
     * Initializes the pages
     * if there is no connected user, it redirect to logout
     */
    public function init() {
        $auth = Zend_Auth::getInstance();
        
        //If user is not connected, redirect to logout
        if (!$auth->hasIdentity()) {
            $this->_helper->redirector('logout', 'auth', null, array('reason' => '1')); // back to login page
        }

        //Check if ctb, if yes redirect to logout
        $idUserGroup = $auth->getIdentity()->UserGroupIdF;
        $action = $this->getRequest()->getActionName();
        $group = new Application_Model_DbTable_UserGroup();
        $group = $group->fetchGroupById($idUserGroup);
        if( $auth->getIdentity()->IdAccessF != '1' && ($action != 'informations' && $action != 'modifypassword'))
        {
            $this->_helper->redirector('logout', 'auth', null, array('reason' => '1')); // back to login page
        }
    }
    
    /**
     * Show the users of the application
     */
    public function gestionuserAction($msg = null)
    {
        
        // Form for the creation of a user
        $form = new Application_Form_CreateUserForm();
        $form->createForm();
        $form->setAction($this->_helper->url('createuser', 'user'));
        $this->view->form = $form;
        
        //Form for the edit of a user
        $editForm = new Application_Form_EditUserForm();
        $editForm->createForm();
        $editForm->setAction($this->_helper->url('edituser', 'user'));
        $this->view->editForm = $editForm;
        
        $auth = Zend_Auth::getInstance();
        $idUserGroup = $auth->getIdentity()->UserGroupIdF;
        
        // Form to get all the users
        $user = new Application_Model_DbTable_User();
        $users = $user->fetchUserByGroup($idUserGroup);
        //passage des variables à la vue
        $msg =$this->getRequest()->getParam('msg');
        $this->view->msg = $msg;
        $this->view->users = $users;
        
    }

    /**
     * Edit a user
     */
    public function edituserAction()
    {
        // Check Post
        if (!$this->getRequest()->isPost())
            die();
        
        //var_dump('ici');die();
        // get param
        $params = $this->getRequest()->getParams();
        $idUser = $params['idUserEdit'];
        $firstname = $params['firstnameEdit'];
        $lastname = $params['lastnameEdit'];
        $email = $params['emailEdit'];
        $tel = $params['telEdit'];
        $auth = Zend_Auth::getInstance();
        $group = $auth->getIdentity()->UserGroupIdF;


        // Check if mail is valid
        $validateur = new Zend_Validate_EmailAddress();
        if (! $validateur->isValid($email)) {
            $error = "L'email est invalide";
            $this->_helper->json(array(
                'code' => $error
            ));
        }

        // Check if firstname's and lastname's lenght is between 1 and 50

        if (strlen($firstname)>50 ) {
            $error = "Le prénom doit être compris entre 1 et 50 caractère";
            $this->_helper->json(array(
                'code' => $error
            ));
        }
        if (strlen($lastname)>50 ) {
            $error = "Le nom doit être compris entre 1 et 50 caractère";
            $this->_helper->json(array(
                'code' => $error
            ));
        }


        $user = new Application_Model_DbTable_User();
        // Update the user
        $verif = $user->updateUser($idUser, $firstname, $lastname, $email, $tel, $group);

        //If ok, response is 42
        if ($verif == "ok") {
            $msg = '42';
        } else
            $msg = $verif;

        $this->_helper->json(array(
            'code' => $msg
        ));
    }
        
    
    /**
     * This action is used to delete a user
     */
    public function deleteuserAction()
    {
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getParams();
            $idUser = $params['idUser'];
            $user = new Application_Model_DbTable_User();
            // delete the user
            $verif = $user->deleteUser($idUser);
            if ($verif == "ok") {
                $msg = 'L\'utilisateur a bien été supprimé';
            } else
                $msg = $verif;
            
            // Return to the page informations
            $this->_helper->redirector('gestionuser','user', null);

        }
        
    }
    
    /**
     * This action hydrate the formular of the edit of the user
     * 
     */
    public function hydrateedituserformAction(){
        
        // Check Post
        if (!$this->getRequest()->isPost())
            die();
        $params = $this->getRequest()->getParams();
        $idUser = $params['idUser'];
        
        $user = new Application_Model_DbTable_User();
        // create the new user
        $user = $user->fetchOne($idUser);
        $this->_helper->json($user);
      
    }
    
    /**
     * This action is used to create a user
     */
    public function createuserAction()
    {
        
        // Check Post
        if ($this->getRequest()->isPost()) {
            
            $params = $this->getRequest()->getParams();
            $username = trim(strtoupper($params['username']));
            $firstname = trim($params['firstname']);
            $lastname = trim($params['lastname']);
            $email = trim($params['email']);
            $password = $params['password'];
            $passwordbis = $params['passwordbis'];
            $tel = $params['tel'];
            $auth = Zend_Auth::getInstance();
            $group = $auth->getIdentity()->UserGroupIdF;
            $access = $params['access'];
            
            $error = "";
            
            // Check if password and passwordbis are the same
            if ($password != $passwordbis)
                $error = "Le mot de passe n'a pas été saisi correctement deux fois d'affilé. ";
            
            // Check if mail is valid
            $validateur = new Zend_Validate_EmailAddress();
            if (! $validateur->isValid($email))
                $error .= "L'email est invalide. ";
            

            if (strlen($username)>50 ) 
                $error .= "Le code alliance est invalide. ";
            
            // Check if firstname's and lastname's lenght is between 1 and 50
            if (strlen($firstname)>50 ) 
                $error .= "Le prénom doit être compris entre 1 et 50 caractères. ";

            if (strlen($lastname)>50 ) 
                $error .= "Le nom doit être compris entre 1 et 50 caractères. ";
            
            //check the length of the password
           if (strlen($password)>50 || strlen($password)<4) 
                $error .= "La longueur du mot de passe doit être entre 4 et 50 caractères. ";
                
            if(!empty($error)){
                $this->_helper->json(array(
                        'code' => $error
                ));
            }
            
            $salt = null;
            // Generation of the salt
            for ($i = 0; $i < 50; $i ++) {
                $salt .= chr(rand(33, 126));
            }
            
            $hashPassword = md5($password . $salt);
            
            $user = new Application_Model_DbTable_User();
            // create the new user
            $verif = $user->createUser($username, $firstname, $lastname, $email, $hashPassword, $tel, $group, $access, $salt);
            
            if ($verif == "ok") {
                //$mailer = new Application_Model_Mailer();
                //$mailer->sendCreateAccountNotification($email, $username, $password);
                $msg = '42';
            } else
                $msg = "Un utilisateur possède déjà ce nom d'utilisateur ou cet email";
            
            $this->_helper->json(array(
                'code' => $msg
            ));
        }
    }
    
    
    /**
     * The default action - show the user's information page
     * param : message
     */
    public function informationsAction($msg = null)
    {      
        $form = new Application_Form_InformationsPwdUserForm();
        $form->createForm($this->_helper->url('modifypassword', 'user'));
        
        //Form for the edit of a user
        $editForm = new Application_Form_EditUserForm();
        $editForm->createForm();
        $editForm->setAction($this->_helper->url('edituser', 'user'));
        $this->view->editForm = $editForm;
        
        //Données de l'utilisateur
        $auth = Zend_Auth::getInstance();
        $user = array();
        $user['id'] = $auth->getIdentity()->UserId;
        $user['name'] = $auth->getIdentity()->UserLastName." ".$auth->getIdentity()->UserFirstName;
        $user['email']= $auth->getIdentity()->UserEmail;

        //passage des variables à la vue
        $this->view->object = 'Modifier mes informations';
        $msg =$this->getRequest()->getParam('msg');
        $this->view->msg = $msg;
        $this->view->form = $form;
        $this->view->user = $user;
        
        // hydrate les selects et input
        $editForm->hydrateForm(array(
            'firstnameEdit'      => $auth->getIdentity()->UserFirstName,
            'lastnameEdit'       => $auth->getIdentity()->UserLastName,
            'emailEdit'          => $auth->getIdentity()->UserEmail,
            'telEdit'            => $auth->getIdentity()->UserTel,
        ));
        
         
    }

    
    /**
     * Action to update the password
     */
    public function modifypasswordAction()
    {
        if (! $this->getRequest()->isPost()) {
            die();
        }
        $params = $this->getRequest()->getParams();
        $actualPwd = $params['actualPwd'];
        $newPwd = $params['newPwd'];
        $newPwdBis = $params['newPwdBis'];
       
        $auth = Zend_Auth::getInstance();
        $username = $auth->getIdentity()->UserId;
        $saltUser = $auth->getIdentity()->UserSalt;
        
       $user =  new  Application_Model_DbTable_User();
       //Check if user enter the right password
       $verif = $user->getCheckPwd($username,$actualPwd, $saltUser);
       if(!$verif)
       { 
           $error = 'Vous avez saisi un mauvais mot de passe';
           $this->_helper->json(array(
                    'code' => $error
                ));
       }
       if($newPwd != $newPwdBis)
       {
           $error = "Le nouveau mot de passe n'a pas été saisi correctement deux fois d'affilé";
           $this->_helper->json(array(
                    'code' => $error
                ));
       }
       if (strlen($newPwd)>50 || strlen($newPwd)<4) {
       //Check if newPwd's lenght is between 4 and 8
           $error = "La longueur du mot de passe doit être entre 4 et 50 caractères. ";
           $this->_helper->json(array(
                    'code' => $error
                ));
       
       }  
       //Update pwd
        $msg = $user->updatePwd($username, $newPwd, $saltUser);
        if($msg == "0")
             $msg = "42";
        $this->_helper->json(array(
                    'code' => $msg
                ));
   
    }
    
    public function exportexceluserAction() {
        
        $view = new Zend_View();
        $auth = Zend_Auth::getInstance();
        $idUserGroup = $auth->getIdentity()->UserGroupIdF;
        $user = new Application_Model_DbTable_User();
        $users = $user->fetchUserByGroup($idUserGroup);
            

        $view->users = $users;
        
 
        $pathFile = "../public/export.xls";
        $id_file = fopen($pathFile, 'w+');
        $view->setScriptPath("../application/views/scripts/user/exports");
        fwrite($id_file, $view->render("exportuser.phtml"));
        $size = filesize($pathFile);
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-disposition: attachment; filename=exportUser.xls");
        readfile($pathFile);
        unlink($pathFile);
        die();
    }
    
}
