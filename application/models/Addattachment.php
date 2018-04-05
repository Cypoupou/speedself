<?php

class Application_Model_Addattachment
{

    protected $attachmentPaths;

     /**
     *
     * @var string directory where the sent attachment files go (with extra slash at the end)
     */
    const ATTACHMENT_DIRECTORY = '../public/attachment/';


    public function __construct(){

        $this->attachmentPaths = array();
    }

    /**
     * Verify if the files are in a correct format (size, error, allowed types)
     * 
     * Save the files by moving them from the tmp directory to the attachment directory by
     * calling saveFiles method
     * 
     * @param array $files array of the form $_FILES
     * @param string $msg passed by reference, must return the error message
     * @param string $id fiche ID, to save in the good dir according to the fiche
     * @return NULL
     * @see Application_Model_Addattachment::saveFiles()
     */
    public function importFilesIntoServer($files, &$msg, $id){
        $msg = '';
        if (isset($files["files"]) && !empty($files["files"]['tmp_name'][0])) {
        
            // calcul de la taille totale des fichiers
            $total = 0;
            foreach ($files["files"]['size'] as $size)
                $total += $size; // size : en octet
        
            // We dont accept file over 3Mo
            if ($total > 30000000) {
                $msg = 'La taille des fichiers surpasse 30 Mo';
                return null;
            }

        
            // variables vérifiant si les fichiers sont corrects et message d'erreur
            $fichiersCorrects = true;
            $messageErreur = '';
        
            // vérification de tous les fichiers
            for ($i = 0; $i < count($files["files"]['name']); $i ++) {
                
                // for each files check if the lenght is upper than 50 carac
                if(strlen($files["files"]['name'][$i])>120)
                {
                    $fichiersCorrects = false;
                    $messageErreur .= "Problème : le nom de votre fichier dépasse 120 caractères.";
                }
                
                // for each files
                // check if the file is correct
                if ($files["files"]["error"][$i] != UPLOAD_ERR_OK) {
                    $fichiersCorrects = false;
                    $messageErreur .= " Problème lors du téléchargement du fichier " . $files["files"]['name'][$i];
                }
                // check the file type
                if (! $this->allowedType($files["files"]['type'][$i])) {
                    $fichiersCorrects = false;
                    $messageErreur .= " Type de fichier non pris en charge " . $files["files"]['name'][$i];
                }
            }
            if (! $fichiersCorrects) {
                // si les fichiers sont incorrects on renvoie au code js un code d'erreur
                $msg = $messageErreur;
                return null;
            }
            // si les fichiers sont corrects on peut les enregistrer sur le serveur
            else {
                $this->saveFiles($files, $id);
            }
        } //fin du if (isset($files["files"]))
    }
	
    /**
     * Iterate over $files
     * Rename them by suffixing a random number, and optionally prefix a string passed by args
     * Move them from tmp directory to attachment directory
     * Save the new path in the attachmentPaths variables
     * 
     * @param array $files array of the form $_FILES
     * @param string $id fiche ID
     * @param string|null $prefix (optional, default value : null) a string to place before
     *      the filename
     * @see Application_Model_Addattachment::ATTACHMENT_DIRECTORY
     */
	public function saveFiles($files, $id, $prefix = null)
    {
        // enregistrement des pièces jointes sur le serveur
        for ($i = 0; $i < count($files["files"]['name']); $i ++) {
            if(empty($files["files"]['name'][$i]))
                continue;
            /* enregistrement de la pièce jointe dans le serveur : à voir
             * s'il est nécessaire ou pas on supprime les espaces et on limite
             *  le nom du fichier à 250 caractères (sans extension)
             */
             $fileName = substr(str_replace(" ", "_", $files['files']['name'][$i]), 0, 250);
            $extension = substr($fileName, strrpos($fileName, '.')); // get file extention
            // random number ou bien id en bd
            $Random_Number = rand(0, 9999); // Random number to be added to name.
            $newFileName = substr($fileName, 0, strrpos($fileName, '.')) . "-" . $Random_Number . $extension; // new file name

            if(!empty($prefix))
                $newFileName = $prefix.$newFileName;
                
            $accents =  array("/é/","/è/","/ê/","/ë/","/ç/","/à/","/â/","/á/","/ä/","/ã/","/å/","/î/","/ï/","/í/","/ì/","/ù/","/ô/","/ò/","/ó/","/ö/");
            $sans =     array( "e" , "e" , "e" , "e" , "c" , "a" , "a" , "a" , "a" , "a" , "a" , "i" , "i" , "i" , "i" , "u" , "o" , "o" , "o" , "o" );
      
            $newFileName = preg_replace($accents, $sans,$newFileName);

            //we need to create the directory first if it does not exist
            $newPath = self::ATTACHMENT_DIRECTORY.$id.'/';
            if (!is_dir($newPath)) {
                mkdir($newPath, 0777, true);
            }
            $newPath = $newPath.$newFileName;
            // déplace du dossier temporaire vers le dossier des exports
            if (move_uploaded_file($files['files']['tmp_name'][$i], $newPath)) {
                $this->attachmentPaths[] = $newPath;
            }
        }}

    /**
     * Function used to upload file without the button upload/parcourir
     * 
     * @param unknown $path
     * @param unknown $id
     * @param unknown $fileName
     * @return multitype:string boolean Return true si fichier deja existant, sinon retourne le path
     */
    public function importFilesIntoServerNotFromUpload($path, $id, $fileName){

        $fileParam = array();
                    //we need to create the directory first if it does not exist
        $fileParam['path'] = self::ATTACHMENT_DIRECTORY.$id.'/';
        if (!is_dir($fileParam['path'])) {
            mkdir($fileParam['path'], 0777, true);
        }
        $fileParam['path'] = $fileParam['path'].$fileName;
        $fileParam['exist'] = file_exists($fileParam['path']);

        // déplace du dossier temporaire vers le dossier des attachment
        if (rename($path, $fileParam['path'])) {
            // Si file n'existait pas, on l'enregistre en bdd
            if(!$fileParam['exist'])
            {

                $this->attachmentPaths[] = $fileParam['path'];
            }

        }
        else
        {
            var_dump($path." ".$fileParam['path']);
            die("hehe");
        }
        return $fileParam;

    }
    
    /**
     * Write in the attachment directory a EML File with the content transmitted by args
     * Save the newly created attachment in the attachmentPath variable of the class
     * 
     * @param string $filename name of the file to write, without extension
     * @param string $content content to write in the EML File
     * @param string $id fiche ID
     * @see Application_Model_Addattachment::ATTACHMENT_DIRECTORY
     * @see Application_Model_Addattachment::$attachmentPaths
     */
    public function writeEMLFile($filename, $content, $id)
    {
        $path = self::ATTACHMENT_DIRECTORY.$id."/";
        if(!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $randomNumber = rand(0, 99); // Random number to be added to name.
        $path .= $filename."-".$randomNumber.".eml";
        $body = fopen($path, "w+");
        fwrite($body, $content);
        fclose($body);
        $this->attachmentPaths[] = $path;
    }
    
    /**
     * Saves on the database the paths of the attachments previously put in the attachmentPath
     * 
     * @param number $idFiche fiche ID
     * @param number $userId user ID of the user that must be linked to the attachments list 
     * @see Application_Model_Addattachment::$attachmentPaths
     */
    public function saveAttachmentsToDatabase($idFiche,$userId){
        if(count($this->attachmentPaths) > 0)
        {
            $db_att = new Application_Model_DbTable_Attachment();
            foreach($this->attachmentPaths as $path)
            {
                $db_att->insert(array(
                    'AttachmentPath'    => $path,
                    'AttachmentDate'    => date('Y-m-d H:i:s', time()),
                    'IdFicheF'          => $idFiche,
                    'IdUserF'           => $userId
                ));
            }
        }
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

}

?>
