<?php
/**
 * Controller
 * This class is in charge of the sending  of the different kinds of emails
 * @author NXSR1654
 *
 */
class MailerController extends Zend_Controller_Action
{
    /**
     * The user must be connected to acces to the actions of this controller
     * (non-PHPdoc)
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
        $auth = Zend_Auth::getInstance();
    
        //If user is not connected, redirect to logout
        if (!$auth->hasIdentity()) {
            $this->_helper->redirector('logout', 'auth', null, array('reason' => '1')); // back to login page
        }
    }
    
    public function mailAction(){
        
        $params = $this->getRequest()->getParams();
        $idFiche = $params['id'];
        $type = $params['fiche'];
        
        if($type == 'rapide'){
            $id = str_replace("'","",$idFiche);
            $idFiche = str_replace('b','',$id);
            $ficheRapTab = new Application_Model_DbTable_FicheRapide();
            $fiche = $ficheRapTab->fetchFicheById($idFiche);
            $fiche = $fiche->toArray();
        }else {
            $ficheTab = new Application_Model_DbTable_Fiche();
            $fiche = $ficheTab->fetchFicheById($idFiche);
            $fiche = $fiche->toArray();

            $db_site = new Application_Model_DbTable_Site();
            $site = $db_site->fetchSiteForFiche($fiche['FicheIMB']);
        }
        
        
        // récupération du nom de l'utilisateur pour la signature (interne)
        $auth = Zend_Auth::getInstance();
        $userLastName = utf8_decode($auth->getIdentity()->UserLastName);
        $userFirstName = utf8_decode($auth->getIdentity()->UserFirstName);
        //met en forme la signature (externe)
        $uFirstName = substr($userFirstName, 0, 1);
        $uLastName = substr($userLastName, 0, 1);

        $idUserGroup = $auth->getIdentity()->UserGroupIdF;

        $groupDb = new Application_Model_DbTable_UserGroup();
        $group = $groupDb->fetchGroupById($idUserGroup);
        $signature = $this->fixSignatureImages($group['GroupSignature']);
        
        //prepare the html content of the e-mail with the template
        $content = Application_Model_Mailer::generateContentTemplate("mail");
        
        $typeMail = $params['type'];
        if($typeMail == 'interne'){
            $user = $userFirstName.' '.$userLastName;
            $this->view->dest = 'interne';
        }else{
            $user = $uFirstName.'. '.$uLastName.'.';
            $this->view->dest = 'externe';
        }
        $this->view->form = new Application_Form_WriteMail();
        $this->view->form->createForm($this->_helper->url("send"));
        if($type == 'rapide'){
            $objet = $fiche['FicheCodePostal'];
            if(!empty($fiche['FicheAdresse'])){
                $objet .= ' - '.$fiche['FicheAdresse'];
            }
            if(!empty($fiche['FicheVille'])){
                $objet .= ' - '.$fiche['FicheVille'];
            }
            $this->view->form->hydrateForm(array(
            'id'    => $id,
            'to'    => '',
            'subject'   => 'Fiche : '.$objet,
            'content'   => $content .''. $user. '</br>'. $signature
            ));
        }else {
            $this->view->form->hydrateForm(array(
            'id'    => $idFiche,
            'to'    => '',
            'subject'   => 'Fiche : '.$fiche['FicheIMB'].' - '.$site['adress'].' - '.$site['city'],
            'content'   => $content .''. $user. '</br>'. $signature
            ));
        }
        $this->view->type = "mail";
        
        $this->_helper->viewRenderer("write");
    }
    
    /**
     * AJAX Request
     * 
     * Send the email to the provided credentials
     * Update the dates in the database according to the action 
     * Save the email and its attachments in the server and in the database
     * 
     * Return a JSON array with key ("code", "msg").
     * code is -1 in case of error, 0 in case of success
     * msg precises the message to show to the user
     */
    public function sendAction()
    {
        //verify if it is an AJAX request
        if(!$this->getRequest()->isPost())
            $this->_helper->redirector('index', 'recherche');
        // empty post when post max value is exceeded
        if(empty($_POST)){
            $maxSize = ini_get('post_max_size');
            switch ( substr($maxSize,-1) )
            {
                case 'G':
                    $maxSize = $maxSize * 1024;
                case 'M':
                    $maxSize = $maxSize * 1024;
                case 'K':
                    $maxSize = $maxSize * 1024;
            }
            if($_SERVER["CONTENT_LENGTH"] >= $maxSize){
                $this->_helper->json(array(
                    'code' => '-1',
                    'msg' => "Fichiers trop volumineux."
                ));
                die();
            }
            else{
                $this->_helper->json(array(
                    'code' => '-1',
                    'msg' => "Erreur! Veuillez contacter l'administrateur"
                ));
            }
        }
        $params = $this->getRequest()->getParams();
        
        $mail = new Application_Model_Mailer();
        
        //if the credentials are not valid : error
        if(!$mail->setEmailAdresses($params['to'], $params['cc'], $params['cci'])){
            $this->_helper->json(array(
                'code' => '-1',
                'msg' => 'Aucun destinataire sélectionné'
            ));
            die();
        }
        $auth = Zend_Auth::getInstance();
        $idUserGroup = $auth->getIdentity()->UserGroupIdF;
        if($idUserGroup == 1){
            $mail->addContact('from', 'comment.avoirlafibre@orange.com', "Comment avoir la Fibre");
        }elseif($idUserGroup == 3){
            $mail->addContact('from', 'lafibre.paris100@orange.com', "100% Paris");
        }else{
            $mail->addContact('from', 'noreply.tracfibre@orange.com', "Trac Fibre");
        }
        
        // add files to the email
        $msg = '';
        $mail->importFilesIntoMail($_FILES, $msg);
        if(!empty($msg)){
            $this->_helper->json(array(
                'code' => '-1',
                'msg' => $msg
            ));
            die();
        }
        
        //build the body of the mail : replace the <img> by attachments inline
        $mail->buildHTML($params['content']);
        
        $mail->setSubject($params['subject']);
        
        if(APPLICATION_ENV != 'development'){
            try{
                $mail->send();
            } catch(Zend_Mail_Exception $e){
                if($e->getCode() == 552)        //error : message file too big
                    $msg = "Fichiers trop volumineux.";
                else
                    $msg = $e->getMessage();
                $this->_helper->json(array(
                    'code' => '-1',
                    'msg' => array(
                        $msg
                    )
                ));
                die();
            }
        }

        //save the attachements in the server
        $prefix_fichier = str_replace('/', '-', $params['subject']);
        $adder = new Application_Model_Addattachment();
        $adder->saveFiles($_FILES, $params['id'], "MAIL_".$prefix_fichier."_PJ_");
        
        //and then the e-mail body
        $adder->writeEMLFile("MAIL_$prefix_fichier", $mail->__toString(), $params['id']);
        $adder->saveAttachmentsToDatabase(/*intval(*/$params['id']/*)*/, Zend_Auth::getInstance()->getIdentity()->UserId);
        

        $params['userId'] = Zend_Auth::getInstance()->getIdentity()->UserId;
        $params['ficheId'] = $params['id'];
        //update the last modification date
        if(strstr($params['ficheId'], 'b') == 'b'){
            $params['ficheId'] = str_replace('b','',$params['ficheId']);
            $db_fiche = new Application_Model_DbTable_FicheRapide();
            $update = $db_fiche->updateFicheModification($params);
        }else {
            $db_fiche = new Application_Model_DbTable_Fiche();
            $update = $db_fiche->updateFicheModification($params);
        }
        
        //Ajout des adresse mails pour les mails interne si ils ne sont pas en BDD
        if($params['dest'] == 'interne'){
            $this->createcontactAction($params);
        }

        //message avec code ok
        $this->_helper->json(array(
            'code' => '0',
            'msg' => 'Votre message a bien été envoyé.'
        ));
        die();
    }
    
    /**
     * AJAX request
     * 
     * Called by the JQUERY autocomplete function
     * Send by GET a param "term", corresponding to the string typed by the user
     * 
     * Return in JSON the list of name/email addresses containing "term"
     */
    public function autocompleteaddressAction(){
        $auth = Zend_Auth::getInstance();
        $idUserGroup = $auth->getIdentity()->UserGroupIdF;
        $input = $this->getRequest()->getParam("term", "");
        $db_contacts = new Application_Model_DbTable_Contact();
        $this->_helper->json($db_contacts->get($input, $idUserGroup));
    }
    
    public function createcontactAction($params) {
        //Récupération du groupe
        $auth = Zend_Auth::getInstance();
        $idUserGroup = $auth->getIdentity()->UserGroupIdF;
        
        //Verifie les parametres et récupere les adresses mails
        if(!empty($params['to'])){
            if(strpos($params['to'], ';') != FALSE){
                $to = explode(";", $params['to']);
            }else {
                $to[0] = $params['to'];
            }
        }else {
            $to = NULL;
        }
        if(!empty($params['cc'])){
            if(strpos($params['cc'], ';') != FALSE){
                $cc = explode(";", $params['cc']);
            }else {
                $cc[0] = $params['cc'];
            }
        }else {
            $cc = NULL;
        }
        if(!empty($params['cci'])){
            if(strpos($params['cci'], ';') != FALSE){
                $cci = explode(";", $params['cci']);
            }else {
                $cci[0] = $params['cci'];
            }
        }else {
            $cci = NULL;
        }
        
        //regroupe les adresses mails dans un tableau
        $tabInter = $this->mergeArray($to, $cc);
        $tabAdresseMail = $this->mergeArray($tabInter, $cci);
        
        $contactTab = new Application_Model_DbTable_Contact();
        foreach ($tabAdresseMail as $adresseMail){
            //Verification si les destinaires existent déjà
            $verif = $contactTab->fetchOneByMail($adresseMail);
            if($verif == NULL){
                $name = explode(".", $adresseMail);
                $lastName = explode("@", $name[1]);
                if($lastName[1] == 'orange'){
                    //Création des contacts
                    $contactTab->createContact(ucfirst($name[0]), ucfirst($lastName[0]), $adresseMail, $idUserGroup);
                }
            }
        }
        
        return true;
        
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

     private function fixSignatureImages($signature){
        $needle = "/images/signatures/";
        $lastPos = 0;
        $positions = array();

        while (($lastPos = strpos($signature, $needle, $lastPos))!== false) {
            $positions[] = $lastPos;
            $lastPos = $lastPos + strlen($needle);
        }

        $offset = 0;
        foreach($positions as $position){   
            $signature = substr_replace($signature, $this->view->baseUrl(), $position + (strlen($this->view->baseUrl())*$offset), 0);
            $offset++;
        }

        return $signature;
    }
    
}
