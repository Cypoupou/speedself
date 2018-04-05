<?php
/**
 * Created by PhpStorm.
 * User: DDJK5031
 * Date: 05/11/2015
 * Time: 14:59
 * AuthController
 *
 * @author
 * @version
 */
require_once 'Zend/Controller/Action.php';

class AuthController extends Zend_Controller_Action
{
    /**
     * Redirect the default action to the login action
     */
    public function indexAction(){
        $this->_helper->redirector('login');
    }

    /**
     * Return to login page
     */
    public function logoutAction($reason = null)
    {
        Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::ForgetMe();
        // back to login page
        $this->_helper->redirector('login', 'auth', null);
    }

    /**
     * The default action - show the login page
     */
    public function loginAction($msg = null)
    {
        // getInstance of Zend
        $auth = Zend_Auth::getInstance();

        //check if the user is already connected
        if($auth->hasIdentity()){
            $this->_helper->redirector('index', 'accueil');
        }
        //No idea
        $db = $this->_getParam('db');

        //Build form
        $loginForm = new Application_Form_LoginForm();
        $loginForm->createForm();
        $loginForm->setAction($this->_helper->url('login', 'auth'));

        $passwordforgottenForm = new Application_Form_PasswordForgottenForm();
        $passwordforgottenForm->createForm();
        $passwordforgottenForm->setAction($this->_helper->url('forgottenpassword', 'auth'));

        //Check Post
        if ($this->getRequest()->isPost()) {
            if ($loginForm->isValid($_POST)) {

                //Build auth adapter
                $adapter = new Zend_Auth_Adapter_DbTable(
                    $db,
                    'user',
                    'UserId',
                    'UserPassword',
                    "MD5(CONCAT(?, UserSalt))"
                );
                
                $adapter->setIdentity($loginForm->getValue('username'));
                $adapter->setCredential($loginForm->getValue('password'));
                //remember the user if he checks the checkbox
                if(intval($loginForm->getValue('rememberme')) === 1){
                    $time  = 60*60*24*30*12*10; // environ 10 ans (secondes*minutes*heures*jours*mois*années)
                    Zend_Session::RememberMe($time);
                }else{
                    Zend_Session::ForgetMe();
                }
                
                //result of the authentification
                $result = $auth->authenticate($adapter);

                if ($result->isValid()) {
                    // renvoi 42 si connexion valide
                    $user = $adapter->getResultRowObject();
                    unset($user->PasswordUser);
                    
                    $auth->getStorage()->write($user);
                    $this->_helper->json(array(
                        'code' => '42',
                    ));

                }
                else
                    //Renvoi msg erreur si connexion non valide
                {   $msg = 'Nom d\'utilisateur ou mot de passe incorrect';
                    $this->_helper->json(array(
                        'code' => $msg
                    ));
                }

            }
        }
        
        $this->view->form = $loginForm;
        $this->view->msg = $msg;
        $this->view->passwordforgottenForm = $passwordforgottenForm;
    }


    /**
     * This action is used to send a new password to the user
     */
    public function forgottenpasswordAction()
    {

        if (! $this->getRequest()->isPost()) {
            die();
        }
        $params = $this->getRequest()->getParams();
        $mail = $params['usermail'];

        $user = new Application_Model_DbTable_User();
        $validMail = $user->fetchOneWithMail($mail);


        if($validMail != null) {
            $ranges = array(range('a', 'z'), range('A', 'Z'), range(1, 9));
            $password = '';
            
            for($i = 0; $i < 8; $i++) {
                $rkey = array_rand($ranges);
                $vkey = array_rand($ranges[$rkey]);
                $password .= $ranges[$rkey][$vkey];
            }
            $validPassword = $user->updatePwd($validMail['UserId'],$password,$validMail['UserSalt']);
            
            if($validPassword == "0") { //send mail password
                $mailer = new Application_Model_Mailer();
                $mailer->sendNewPassword($mail, $password);
                $this->_helper->json(array(
                    'code' => "42"
                ));
            }else { //Return error
                $this->_helper->json(array(
                    'code' => "Veuillez contacter un administrateur, merci"
                ));
            }
            
        }else { //Renvoi msg erreur si le mail n'existe pas en bdd
            $this->_helper->json(array(
                'code' => "L'adresse mail rentré n'est pas valide"
            ));
        }
    }
    
}
