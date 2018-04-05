<?php
/**
 * Created by PhpStorm.
 * User: DDJK5031
 * Date: 03/02/2016
 * Time: 12:18
 */

/**
 * This class is used to deal with administration pannel
 *
 * @author Cyprien POUDEVIGNE
 *
 */
class AdminController extends Zend_Controller_Action
{
    public function init()
    {
        $auth = Zend_Auth::getInstance();

        //If user is not connected, redirect to logout
        if (!$auth->hasIdentity()) {
            $this->_helper->redirector('logout', 'auth', null, array('reason' => '1')); // back to login page
        }
        if ($auth->getIdentity()->IdAccessF != '1') {
            $this->_helper->redirector('index', 'recherche', null, array());
        }
    }


    /**
     * Used to show the qualite
     */
    public function gestionqualiteAction()
    {
        $auth = Zend_Auth::getInstance();
        $userGroupId = $auth->getIdentity()->UserGroupIdF;

        $qualite = new Application_Model_DbTable_FicheQualite();
        $qualite = $qualite->getAllQualiteByGroupCrea($userGroupId);

        $this->view->qualite = $qualite;
    }

    /**
     * This action is used to create a qualite
     */
    public function createqualiteAction()
    {

        // Check Post
        if ($this->getRequest()->isPost()) {

            $params = $this->getRequest()->getParams();
            $qualitename = trim($params['qualiteNameCreate']);


            $error = "";
            

            if (strlen($qualitename)>50)
                $error .= "Le nom de la qualite doit être compris entre 1 et 50 caractères. ";
            if (!empty($error)) {
                $this->_helper->json(array(
                    'code' => $error
                ));
            }
            $auth = Zend_Auth::getInstance();
            $userGroupId = $auth->getIdentity()->UserGroupIdF;

            $qualite = new Application_Model_DbTable_FicheQualite();
            // create the new group
            $idInsert = $qualite->createQualite($qualitename, $userGroupId);

            if ($idInsert == "nok") {
                $msg = "Contactez un administrateur svp.";
                $this->_helper->json(array(
                    'code' => $msg
                ));
            }
            $this->_helper->json(array(
                'code' => '42'
            ));
        }
    }

    /**
     * This action is used to delete a qualite
     */
    public function deletequaliteAction()
    {
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getParams();
            $qualiteId = $params['qualiteId'];


            $qualite = new Application_Model_DbTable_FicheQualite();
            // delete the qualite
            $qualite->deleteQualite($qualiteId);

            // Return to the page informations
            $this->_helper->redirector('gestionqualite', 'admin', null);

        }

    }

    /**
     * This action hydrate the formular of the edit of the qualite
     *
     */
    public function hydrateeditqualiteformAction()
    {

        // Check Post
        if (!$this->getRequest()->isPost())
            die();

        $params = $this->getRequest()->getParams();
        $qualiteId = $params['qualiteId'];


        $qualite = new Application_Model_DbTable_FicheQualite();
        $qualite = $qualite->getOneQualiteById($qualiteId);
        $this->_helper->json($qualite);

    }

    /**
     * Edit a qualite
     */
    public function editqualiteAction()
    {
        // Check Post
        if (!$this->getRequest()->isPost())
            die();

        // get param
        $params = $this->getRequest()->getParams();
        $qualiteId = $params['qualiteIdEdit'];
        $qualitename = trim($params['qualiteNameEdit']);
        $error = "";

            if (strlen($qualitename)>50)
                $error .= "Le nom de la qualite doit être compris entre 1 et 50 caractères. ";
            if (!empty($error)) {
                $this->_helper->json(array(
                    'code' => $error
                ));
            }

        $qualite = new Application_Model_DbTable_FicheQualite();
        // Update the user
        $verif = $qualite->updateQualite($qualiteId, $qualitename);

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
     * Used to show the question
     */
    public function gestionquestionAction()
    {
        $auth = Zend_Auth::getInstance();
        $userGroupId = $auth->getIdentity()->UserGroupIdF;

        $question = new Application_Model_DbTable_FicheQuestion();
        $question = $question->getAllQuestionByGroupCrea($userGroupId);

        $this->view->question = $question;
    }

    /**
     * This action is used to create a question
     */
    public function createquestionAction()
    {

        // Check Post
        if ($this->getRequest()->isPost()) {

            $params = $this->getRequest()->getParams();
            $questionname = trim($params['questionNameCreate']);


            $error = "";
            

            if (strlen($questionname)>50)
                $error .= "Le nom de la question doit être compris entre 1 et 50 caractères. ";
            if (!empty($error)) {
                $this->_helper->json(array(
                    'code' => $error
                ));
            }
            $auth = Zend_Auth::getInstance();
            $userGroupId = $auth->getIdentity()->UserGroupIdF;

            $question = new Application_Model_DbTable_FicheQuestion();
            // create the new group
            $idInsert = $question->createQuestion($questionname, $userGroupId);

            if ($idInsert == "nok") {
                $msg = "Contactez un administrateur svp.";
                $this->_helper->json(array(
                    'code' => $msg
                ));
            }
            $this->_helper->json(array(
                'code' => '42'
            ));
        }
    }

    /**
     * This action is used to delete a question
     */
    public function deletequestionAction()
    {
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getParams();
            $questionId = $params['questionId'];


            $question = new Application_Model_DbTable_FicheQuestion();
            // delete the question
            $question->deleteQuestion($questionId);

            // Return to the page informations
            $this->_helper->redirector('gestionquestion', 'admin', null);

        }

    }

    /**
     * This action hydrate the formular of the edit of the question
     *
     */
    public function hydrateeditquestionformAction()
    {

        // Check Post
        if (!$this->getRequest()->isPost())
            die();

        $params = $this->getRequest()->getParams();
        $questionId = $params['questionId'];


        $question = new Application_Model_DbTable_FicheQuestion();
        $question = $question->getOneQuestionById($questionId);
        $this->_helper->json($question);

    }

    /**
     * Edit a question
     */
    public function editquestionAction()
    {
        // Check Post
        if (!$this->getRequest()->isPost())
            die();

        // get param
        $params = $this->getRequest()->getParams();
        $questionId = $params['questionIdEdit'];
        $questionname = trim($params['questionNameEdit']);
        $error = "";

            if (strlen($questionname)>50)
                $error .= "Le nom de la question doit être compris entre 1 et 50 caractères. ";
            if (!empty($error)) {
                $this->_helper->json(array(
                    'code' => $error
                ));
            }

        $question = new Application_Model_DbTable_FicheQuestion();
        // Update the user
        $verif = $question->updateQuestion($questionId, $questionname);

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
     * Used to show the contact
     */
    public function gestioncontactAction()
    {
        $auth = Zend_Auth::getInstance();
        $userGroupId = $auth->getIdentity()->UserGroupIdF;

        $contacts = new Application_Model_DbTable_Contact();
        $contact = $contacts->getAllContactByGroup($userGroupId);
        $this->view->contacts = $contact;

        // Form for the creation of a contact
        $createForm = new Application_Form_CreateContactForm();
        $createForm->createForm();
        $createForm->setAction($this->_helper->url('createcontact', 'admin'));
        $this->view->form = $createForm;

        // Form for the edit of a contact
        $editForm = new Application_Form_EditContactForm();
        $editForm->createForm();
        $editForm->setAction($this->_helper->url('editcontact', 'admin'));
        $this->view->editform = $editForm;

    }
    
    /**
     * This action is used to create a contact
     */
    public function createcontactAction()
    {

        $auth = Zend_Auth::getInstance();
        $userGroupId = $auth->getIdentity()->UserGroupIdF;
        
        // Check Post
        if ($this->getRequest()->isPost()) {

            $params = $this->getRequest()->getParams();
            $contactfirstname = trim($params['contactfirstname']);
            $contactlastname = trim($params['contactlastname']);
            $contactemail = trim($params['contactemail']);



            $error = "";

            // Check if contact's name lenght is between 1 and 50
            
             if (strlen($contactfirstname)>50)
                $error .= "Le prénom doit être compris entre 1 et 50 caractères. ";
             if (strlen($contactlastname)>50)
                $error .= "Le nom doit être compris entre 1 et 50 caractères. ";


            // Check if mail is valid
            $validateur = new Zend_Validate_EmailAddress();
            if (! $validateur->isValid($contactemail))
                $error = "L'email est invalide";

            if (!empty($error)) {
                $this->_helper->json(array(
                    'code' => $error
                ));
            }



            $contact = new Application_Model_DbTable_Contact();
            $idInsert = $contact->createContact($contactfirstname, $contactlastname,$contactemail, $userGroupId);

            if ($idInsert == "nok") {
                $msg = "Contactez un administrateur svp.";
                $this->_helper->json(array(
                    'code' => $msg
                ));
            }
            $this->_helper->json(array(
                'code' => '42'
            ));
        }
    }
    
    /**
     * This action is used to delete a contact
     */
    public function deletecontactAction()
    {
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getParams();
            $contactId = $params['contactId'];


            $contact = new Application_Model_DbTable_Contact();
            // delete the contact
            $contact->deleteContact($contactId);

            // Return to the page informations
            $this->_helper->redirector('gestioncontact', 'admin', null);

        }

    }

    /**
     * This action hydrate the formular of the edit of the contact
     *
     */
    public function hydrateeditcontactformAction()
    {

        // Check Post
        if (!$this->getRequest()->isPost())
            die();

        $params = $this->getRequest()->getParams();
        $contactId = $params['contactId'];

        $contacts = new Application_Model_DbTable_Contact();
        $contact = $contacts->fetchOne($contactId);
        $this->_helper->json($contact);

    }

    /**
     * Update a contact
     */
    public function editcontactAction()
    {
        // Check Post
        if (!$this->getRequest()->isPost())
            die();

        $params = $this->getRequest()->getParams();
        $contactfirstname = trim($params['contactfirstnameEdit']);
        $contactlastname = trim($params['contactlastnameEdit']);
        $contactemail = trim($params['contactemailEdit']);
        $contactId = $params['contactId'];



        $error = "";

        // Check if contact's name lenght is between 1 and 50

        if (strlen($contactfirstname)>50)
                $error .= "Le prénom doit être compris entre 1 et 50 caractères. ";
        if (strlen($contactlastname)>50)
                $error .= "Le nom doit être compris entre 1 et 50 caractères. ";
        
        

        // Check if mail is valid
        $validateur = new Zend_Validate_EmailAddress();
        if (! $validateur->isValid($contactemail))
            $error = "L'email est invalide";

        if (!empty($error)) {
            $this->_helper->json(array(
                'code' => $error
            ));
        }

        $contact = new Application_Model_DbTable_Contact();
        $verif = $contact->updateContact($contactId,$contactfirstname, $contactlastname,$contactemail);

        //If ok, response is 42
        if ($verif == "ok") {
            $msg = '42';
        } else
            $msg = $verif;

        $this->_helper->json(array(
            'code' => $msg
        ));

    }
    
    public function gestionficheAction() {
        
        //Récupération des fiches vide
        $ficheTab = new Application_Model_DbTable_Fiche();
        $fiche = $ficheTab->fetchEmptyFiche();
        
        //Récupération des fiches rapide vide
        $ficheRapTab = new Application_Model_DbTable_FicheRapide();
        $ficheRap = $ficheRapTab->fetchEmptyFiche();
        
        //Fusionne les deux tableaux
        $allFiche = $this->mergeArray($fiche,$ficheRap);
        if($allFiche == NULL){
            $emptyFiche = 0;
        }else{
            $emptyFiche = count($allFiche);
        }
        
        //Mise en place de la phrase
        if($emptyFiche == 1 || $emptyFiche == 0){
            $phrase = "Il y a actuellement ".$emptyFiche." fiche vide";
            if($emptyFiche == 1){
                $question= "Souhaitez-vous la supprimer ?";
            }else{
                $question= "";
            }
        }else{
            $phrase = "Il y a actuellement ".$emptyFiche." fiches vides";
            $question= "Souhaitez-vous les supprimer ?";
        }
        $this->view->emptyFiche = $emptyFiche;
        $this->view->phrase = $phrase;
        $this->view->question = $question;
        
    }
    
    public function deleteficheAction() {
        
        //Récupération des fiches vide
        $ficheTab = new Application_Model_DbTable_Fiche();
        $fiche = $ficheTab->fetchEmptyFiche();
        //Suppression des fiches vide
        if($fiche != NULL){
            foreach ($fiche as $ficheId) {
                $ficheTab->deleteFiche($ficheId['FicheId']);
            }
        }
        
        //Récupération des fiches rapide vide
        $ficheRapTab = new Application_Model_DbTable_FicheRapide();
        $ficheRap = $ficheRapTab->fetchEmptyFiche();
        //Suppression des fiches rapide vide
        if($ficheRap != NULL){
            foreach ($ficheRap as $ficheRapId) {
                $ficheRapTab->deleteFiche($ficheRapId['FicheId']);
            }
        }
        
        $this->_helper->json(array(
                'code' => "ok"
            ));
        die();
        
    }
    
    public function mergeArray($array1, $array2){
        if(is_array($array1)){
            if(is_array($array2)){
                $merge = array_merge($array1,$array2);
            }else {
                $merge = $array1;
            }
        }elseif (is_array($array2)) {
            $merge = $array2;
        }else {
            $merge = NULL;
        }
        return $merge;
    }
    
}
