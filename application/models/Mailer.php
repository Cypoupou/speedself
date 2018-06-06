<?php

class Application_Model_Mailer
{
    protected $mail;

    private static $contactFields = array('from', 'to', 'cc', 'bcc');
    const SEPARATOR = ';';   
    
    public function __construct(){
        $this->mail = new Zend_Mail('UTF-8');
        $this->mail->setType(Zend_Mime::MULTIPART_RELATED);
    }
    
    /**
     * Send a notification to a new user that his account has been created,
     * with his username/password
     * Do not send a notification when SEND_NOTIFICATION is "none"
     * @param string $email email of the new user
     * @param string $codeAlliance username of the new user
     * @param string $password not a hash of the password 
     */
    public function sendCreateAccountNotification($email, $codeAlliance, $password){
        $this->addContact('to', $email);
        $this->addContact('from', 'noreply.speedself@gmail.com', 'Speed Self Application');
        $this->mail->setSubject('Création du compte');
        
        //create the view to render the html file
        $view = new Zend_View();
        //to change the directory (scripts is the default directory)
        $view->setScriptPath(APPLICATION_PATH."/views/email_templates/");
        //pass the variables to the view script
        $view->username = $codeAlliance;
        $view->password = $password;
        $this->mail->setBodyHtml($view->render('account_creation.phtml'),
        'UTF-8',
        Zend_Mime::ENCODING_QUOTEDPRINTABLE);

        try{
            $this->mail->send();
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }
    
    public function sendTicket($email, $date, $path, $filename){
        $this->addContact('to', $email);
        $this->addContact('from', 'noreply.speedself@gmail.com', 'Speed Self Application');
        $this->addAttachmentToMail($path, $filename);
        $this->mail->setSubject('Ticket du '.$date);
        
        //create the view to render the html file
        $view = new Zend_View();
        //to change the directory (scripts is the default directory)
        $view->setScriptPath(APPLICATION_PATH."/views/email_templates/");
        //pass the variables to the view script
        $view->date = $date;
        $this->mail->setBodyHtml($view->render('ticket.phtml'),
        'UTF-8',
        Zend_Mime::ENCODING_QUOTEDPRINTABLE);

        try{
            $this->mail->send();
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }
    
        public function sendNewPassword($email, $password){
        $this->addContact('to', $email);
        $this->addContact('from', 'noreply.tracfibre@orange.com', 'TRAC Fibre Application');
        $this->mail->setSubject('Changement de mot de passe');
        
        //create the view to render the html file
        $view = new Zend_View();
        //to change the directory (scripts is the default directory)
        $view->setScriptPath(APPLICATION_PATH."/views/email_templates/");
        //pass the variables to the view script
        $view->password = $password;
        $this->mail->setBodyHtml($view->render('newpassword.phtml'),
        'UTF-8',
        Zend_Mime::ENCODING_QUOTEDPRINTABLE);

        try{
            $this->mail->send();
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }
    
    /**
     * Set the email addresses of the different contacts to the Zend_Mail
     * @param string $tos 
     * @param string $ccs
     * @param unknown $ccis
     * @return boolean false the credentials are incorrect
     */
    public function setEmailAdresses($tos, $ccs = null, $ccis = null){
        $tos = $this->extractEmailAddresses($tos);
        $ccs = $this->extractEmailAddresses($ccs);
        $ccis = $this->extractEmailAddresses($ccis);
        
        if ($tos == null) {
            return false;
        }
        // champ to
        $this->mail->addTo($tos);
        // champ cc
        if ($ccs != null)
            $this->mail->addCc($ccs);
        // champ cci
        if ($ccis != null)
            $this->mail->addBcc($ccis);
        
        return true;
    }
    
    /**
     * Retourne un tableau d'adresses email à partir d'une chaine de caractères
     *
     * @param string $string
     *            la chaîne contenant les adresses e-mails, séparé par des ;
     * @return array liste des adresses mails
     */
    private function extractEmailAddresses($string)
    {
        $array = array();
        // split la chaine en une array
        $array = explode(self::SEPARATOR, $string);
        $i = 0;
        // on stocke le nombre d'élements ($limit - 1 est l'index du dernier élement)
        // à cause des unset, le nombre d'éléments peut changer
        $limit = count($array);
        do {
        $array[$i] = htmlspecialchars(trim($array[$i]));
        // si à l'index c'est vide, on supprime l'index
        if (empty($array[$i]))
        unset($array[$i]);
        $i ++;
        } while ($i < $limit);
    
        // renumérote les index dans l'array
        $array = array_values($array);
        return $array;
    }
    
    /**
     * Set the subject to the mail
     * @param string $subject subject
     */
    public function setSubject($subject){
        $this->mail->setSubject($subject);
    }
    
    /**
     * Add a contact or a contact list to the email
     * @param string $field the concerned field (to, from...)
     * @param string $emails a contact email, or several email adresses separated by ;
     * @param string $name name linked the the email adress, null by default
     * @throws Exception when $field indicates a unknown field
     */
    public function addContact($field, $emails, $name = null){
        if(array_search(strtolower($field), self::$contactFields) === false)
            throw new Exception("Unable to put contact email in this field : ".$field);
        
        //the method to set the from is different
        if(ucfirst($field) == 'From'){
            $name = ($name == null) ? 'SAV FTTH' : $name;
            $this->mail->setFrom($emails, $name);
        }
        else{
            $field = (ucfirst($field) == 'From') ? 'setFrom' : 'add'.ucfirst($field);
            $arrContacts = $this->extractEmailAddresses($emails);
            $this->mail->$field($arrContacts);
        }
    }
    
    public function importFilesIntoMail($files, &$msg){
        $msg = '';
        // si il y a des fichiers on les joint à l'email
        if (isset($files["files"]) && count($files["files"]['tmp_name']) > 1) {

            // calcul de la taille totale des fichiers
            $total = 0;
            foreach ($files["files"]['size'] as $size)
                $total += $size; // size : en octet
        
            // un mail accepte des pièce jointe jusqu'à 10mo
            if ($total > 9000000) {
                $msg = 'Fichiers trop volumineux.';
                return null;
            }
        
            // variables vérifiant si les fichiers sont corrects et message d'erreur
            $fichiersCorrects = true;
            $messageErreur = '';
        
            // vérification de tous les fichiers
            for ($i = 0; $i < count($files["files"]['name']); $i ++) {
                if(empty($files["files"]['tmp_name'][$i]))
                    continue;
                // for each files
                // check if the file is correct
                if ($files["files"]["error"][$i] != UPLOAD_ERR_OK) {
                    $fichiersCorrects = false;
                    $messageErreur .= " Problème lors du téléchargement du fichier " . $files["files"]['name'][$i];
                }
                // check the file type
                if (! $this->allowedType($files["files"]['type'][$i])) {
                    $fichiersCorrects = false;
                    $messageErreur .= $files["files"]['type'][$i]." Type de fichier non pris en charge " . $files["files"]['name'][$i];
                }
            }
            if (! $fichiersCorrects) {
                // si les fichiers sont incorrects on renvoie au code js un code d'erreur
                $msg = $messageErreur;
                return null;
            }
            // si les fichiers sont corrects on peut les enregistrer sur le serveur
            else {
        
                // on met les pieces jointes dans l'email
                for ($i = 0; $i < count($files["files"]['name']); $i ++) {
                    if(empty($files["files"]['tmp_name'][$i]))
                        continue;
                    $this->addAttachmentToMail($files["files"]['tmp_name'][$i],
                        $files["files"]['name'][$i]);
                }

            }
        } //fin du if (isset($files["files"]))
    }
    
    /**
     * Send the email
     */
    public function send(){
        $transport = new Zend_Mail_Transport_Smtp('localhost');
        
        $this->mail->send($transport);
    }
    
    /**
     * Allow to replace the inline images (in base64, from captures usually) to put them in
     * attachment inline
     *
     * It encapsulates the content written by the user in a HTML div with a specific id. This div
     * will be the "border" for the inbox import batch, between the message sent by reclafibre and
     * the response of the client.
     *
     * Set the body html to the mail
     * @param string $chaine html to put in the mail
     * @param int $idConv the conversation id (to retrieve the history of the conversation)
     */
    public function buildHTML($chaine){
    
        $matches = array();
        //search the inline images in html
        preg_match_all("/<img.*src=['\"]data:([a-z]+\\/[a-z]+);base64,([^'\"]+)/", $chaine, $matches);
        $newChaine = $chaine;
        //suppress the first index (exact match)
        array_splice($matches, 0, 1);
        //matches[0] contains the type of the image
        //matches[1] contains base64 code
        if (count($matches) == 2 && count($matches[0]) > 0 && count($matches[1]) > 0) {
            for($i = 0 ; $i < count($matches[0]) ; $i++){
                $at = $this->mail->createAttachment(base64_decode($matches[1][$i]));
                $at->type = $matches[0][$i];
                $at->disposition = Zend_Mime::DISPOSITION_INLINE;
                $at->encoding = Zend_Mime::ENCODING_BASE64;
                $at->filename = 'image'.strval($i).'.png';
                $at->id = 'cid_' . md5($at->filename);
                //replace in the html the src by the id of the attachment with the tag cid
                $newChaine = str_replace('data:'.$matches[0][$i].';base64,'.$matches[1][$i],
                    'cid:' . $at->id,
                    $newChaine);
            }
        }
        $this->mail->setBodyHtml($newChaine,
            'UTF-8',
            Zend_Mime::ENCODING_QUOTEDPRINTABLE);
    }
    
    /**
     * Get the content of the Zend_Mail in a string
     * Used to generate .eml files
     * @return string
     */
    public function __toString(){
        $headers = $this->mail->getHeaders();
        $string = empty($headers['From']) ? "From: noreply.tracfibre@orange.com" : 
                    "From: ".$headers['From'][0];
        if(!empty($headers['To'])){
            $string .= "\nTo: ";
            foreach ($headers['To'] as $key => $tos){
                if(strpos($tos, "@") !== false)
                    $string .= $tos.",";
            }
            $string = trim($string, ",");
        }
        if(!empty($headers['Cc'])){
            $string .= "\nCc: ";
            foreach ($headers['Cc'] as $key => $ccs){
                if(strpos($ccs, "@") !== false)
                    $string .= $ccs.",";
            }
            $string = trim($string, ",");
        }
        $string .= "\nSubject: ".$this->mail->getSubject();
        $string .= "\nContent-Type: multipart/related; boundary=\"".$this->mail->getMime()->boundary()."\"";
        $string .= "\nMIME-Version: 1.0\n";
        $string .= "\n--".$this->mail->getMime()->boundary()."";
        $string .= "\nContent-type: text/html; charset=\"utf-8\"";
        
        $string .= "\n\n<html><head></head><body>"
            .quoted_printable_decode($this->mail->getBodyHtml(true))."</body></html>\n";
        /* @var $part Zend_Mime_Part */
        foreach($this->mail->getParts() as $part){
            $string.=$this->traitementPart($part);
        }
            $string .= "\n--".$this->mail->getMime()->boundary()."--";
        return $string;
    }
    
    /**
     * Return the toString of a part
     * Used to generate .eml content
     * @param Zend_Mime_Part $part part to read
     * @return string
     */
    private function traitementPart(Zend_Mime_Part $part){
        $string = "\n--".$this->mail->getMime()->boundary()."";
        $string .= "\nContent-type: ".$part->type."; charset=\"".$part->charset."\"";
        $string .= "\nContent-ID: ".$part->id;
        if(!empty($part->description))
            $string .= "\nContent-Description: ".$part->description;
        if(!empty($part->disposition))
            $string .= "\nContent-Disposition: ".$part->disposition;
        if(!empty($part->filename))
            $string .= "; filename=\"".$part->filename."\"";
        if(!empty($part->encoding))
            $string .= "\nContent-Transfer-Encoding: ".$part->encoding;
        $string .= "\n\n".$part->getContent("\n")."\n";
        return $string;
    }
    /**
     * Check if the type is an allowed type for files
     *
     * @param string $string
     *            name of the type (find in $_FILES['name']['type'])
     * @return boolean true if good, else false
     */
    private function allowedType($string)
    {
        $string = strtolower($string);
        switch ($string) {
            // allowed file types
            case 'image/png':
            case 'application/vnd.ms-outlook' :
            case 'application/octet-stream':
            case 'application/x-msdownload':
            case 'message/rfc822' :
            case 'image/gif':
            case 'image/jpeg':
            case 'image/pjpeg':
            case 'text/plain':
            case 'text/html': // html file
            case 'application/x-zip-compressed':
            case 'application/zip': // .zip
            case 'application/pdf': // .pdf
            case 'application/msword': // .doc
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document': // .docx
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': // .xlsx
            case 'application/vnd.ms-excel': // .xls, .csv
            case 'application/vnd.ms-powerpoint': // .ppt
            case 'application/vnd.openxmlformats-officedocument.presentationml.presentation': // .pptx
                return true;
            default:
                return false;
        }
    }
    
    /**
     * Ajoute un fichier en pièce jointe d'un mail
     *
     * @param string $path le chemin du fichier
     * @param string $filename nom du fichier que l'on veut donner à la pièce jointe
     */
    private function addAttachmentToMail($path, $filename)
    {
        // Enregistrement de la pièce jointe
        // on extrait le contenu (string)
        $content = file_get_contents($path);
        // on crée une pièce jointe à partir du contenu
        $file = $this->mail->createAttachment($content);
        // // on donne le nom de la pièce jointe
        $file->filename = $filename;
    
    }
    
    /**
     * Return a HTML template filled with provided infomation 
     * 
     * @param string $nameTemplate name of the template file (without .phtml)
     * @param array $recla array containing informations to integrate in the template
     * @return string string of the content of the email
     */
    public static function generateContentTemplate($nameTemplate){
        //create the view to render the html file
        $view = new Zend_View();
        //to change the directory (scripts is the default directory)
        $view->setScriptPath(APPLICATION_PATH."/views/email_templates/");       
        return $view->render($nameTemplate.'.phtml');
    }
}

?>
