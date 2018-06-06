<<<<<<< HEAD
<?php

/**
 * Description of RepasController
 *
 * @author ddjk5031
 */
class RepasController extends Zend_Controller_Action {

    public function init() {
        $auth = Zend_Auth::getInstance();

        //If stock is not connected, redirect to logout
        if (!$auth->hasIdentity()) {
            $this->_helper->redirector('logout', 'auth', null, array('reason' => '1')); // back to login page
        }
    }

    public function indexAction() {
        
    }

    public function historiqueAction() {
        //Verifie si Utilisateur
        $auth = Zend_Auth::getInstance();
        $access = $auth->getIdentity()->IdAccessF;
        if ($access == 1) {
            $this->_helper->redirector('logout', 'auth', null, array('reason' => '1')); // back to login page
        }
        $historiqueTab = new Application_Model_DbTable_Historique();
        $historique = $historiqueTab->getHistoriqueByUser($auth->getIdentity()->UserId);

        $this->view->historique = $historique;
    }

    public function reservationAction() {
        //Verifie si Utilisateur
        $auth = Zend_Auth::getInstance();
        $access = $auth->getIdentity()->IdAccessF;
        if ($access == 1) {
            $this->_helper->redirector('logout', 'auth', null, array('reason' => '1')); // back to login page
        }
        $reservationTab = new Application_Model_DbTable_Reservation();
        $reservation = $reservationTab->getReservationByUser($auth->getIdentity()->UserId);

        $this->view->reservation = $reservation;
    }

    public function gestionticketAction() {
        //Verifie si Utilisateur
        $auth = Zend_Auth::getInstance();
        $access = $auth->getIdentity()->IdAccessF;
        if ($access == 1) {
            $this->_helper->redirector('logout', 'auth', null, array('reason' => '1')); // back to login page
        }
        $params = $this->getRequest()->getParams();

        $reservationTab = new Application_Model_DbTable_Reservation();
        $reservation = $reservationTab->getReservationByUserAndId($auth->getIdentity()->UserId, $params['ticket']);
        $this->view->reservation = $reservation;

        $stockTab = new Application_Model_DbTable_Stock();
        $ids = explode(',', $reservation[0]['StockIds']);
        $menu = [];
        foreach ($ids as $id) {
            $stock = $stockTab->getStockById($id);
            $menu[$stock[0]['StockType']][$stock[0]['id']] = $stock[0];
        }
        $this->view->menu = $menu;

        $userTab = new Application_Model_DbTable_User();
        $user = $userTab->fetchOne($auth->getIdentity()->UserId);
        $this->view->user = $user['UserFirstName'] . ' ' . $user['UserLastName'];
        
    }

    public function gestionstockAction($msg = null) {
        //Verifie si Administrateur
        $auth = Zend_Auth::getInstance();
        $access = $auth->getIdentity()->IdAccessF;
        if ($access == 2) {
            $this->_helper->redirector('logout', 'auth', null, array('reason' => '1')); // back to login page
        }

        // Form for the creation of a stock
        $form = new Application_Form_CreateStockForm();
        $form->createForm();
        $form->setAction($this->_helper->url('createstock', 'repas'));
        $this->view->form = $form;

        // Form for the edit of a stock
        $editForm = new Application_Form_EditStockForm();
        $editForm->createForm();
        $editForm->setAction($this->_helper->url('editstock', 'repas'));
        $this->view->editForm = $editForm;

        // Form to get all the stocks
        $stock = new Application_Model_DbTable_Stock();
        $stocks = $stock->getAllStock();
        //passage des variables à la vue
        $msg = $this->getRequest()->getParam('msg');
        $this->view->msg = $msg;
        $this->view->stocks = $stocks;

        // Verify if the stock is used or not
        $menuTab = new Application_Model_DbTable_Menu();
        foreach ($stocks as $stock) {
            $stockUsed[$stock['id']] = $menuTab->getUsedStockByDateAndId('19/03/2018', $stock['id']); // date du jour
        }
        $this->view->stockUsed = $stockUsed;
    }

    public function razAction() {

        // Form to get all the stocks
        $stockTab = new Application_Model_DbTable_Stock();
        $stocks = $stockTab->getAllStock();

        // Verify if the stock is used or not
        $menuTab = new Application_Model_DbTable_Menu();
        foreach ($stocks as $stock) {
            $stockUsed[$stock['id']] = $menuTab->getUsedStockByDateAndId('19/03/2018', $stock['id']); // date du jour
        }

        // Mise à zero des stocks non utilisé
        foreach ($stockUsed as $id => $value) {
            if ($value == 0) {
                $stockTab->razStock($id);
            }
        }

        $this->_helper->json(array(
            'result' => 'ok',
        ));
        die();
    }

    /**
     * Edit a stock
     */
    public function editstockAction() {
        // Check Post
        if (!$this->getRequest()->isPost())
            die();

        $params = $this->getRequest()->getParams();
        $idStock = $params['idStock'];
        $StockName = $params['StockName'];
        $StockNumber = $params['StockNumber'];
        $StockPrice = $params['StockPrice'];
        $StockType = $params['StockType'];


        // Check if firstname's and lastname's lenght is between 1 and 50

        if (strlen($StockName) > 50) {
            $error = "Le nom doit être compris entre 1 et 50 caractère";
            $this->_helper->json(array(
                'code' => $error
            ));
        }

        $stock = new Application_Model_DbTable_Stock();
        // Update the stock
        $verif = $stock->updateStock($idStock, $StockName, $StockNumber, $StockPrice, $StockType);

        //If ok, response is 42
        if ($verif == "ok") {
            $msg = '42';
        }else
            $msg = $verif;

        $this->_helper->json(array(
            'code' => $msg
        ));
    }

    /**
     * This action is used to delete a stock
     */
    public function deletestockAction() {
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getParams();
            $idStock = $params['idStock'];
            $stock = new Application_Model_DbTable_Stock();
            // delete the stock
            $verif = $stock->deleteStock($idStock);
            if ($verif == "ok") {
                $msg = 'L\'utilisateur a bien été supprimé';
            }else
                $msg = $verif;

            // Return to the page informations
            $this->_helper->redirector('gestionstock', 'repas', null);
        }
    }

    /**
     * This action hydrate the formular of the edit of the stock
     * 
     */
    public function hydrateeditstockformAction() {

        // Check Post
        if (!$this->getRequest()->isPost()) {
            die();
        }
        $params = $this->getRequest()->getParams();
        $idStock = $params['idStock'];

        $stockTab = new Application_Model_DbTable_Stock();
        $stock = $stockTab->fetchOne($idStock);
        //$this->_helper->json($stock);
        $this->_helper->json(array(
            'StockName' => $stock['StockName'],
            'StockNumber' => $stock['StockNumber'],
            'StockPrice' => $stock['StockPrice'],
            'StockType' => $stock['StockType']
        ));
    }

    /**
     * This action is used to create a stock
     */
    public function createstockAction() {

        // Check Post
        if ($this->getRequest()->isPost()) {

            $params = $this->getRequest()->getParams();
            $stockname = trim($params['name']);
            $stocknumber = trim($params['number']);
            $stockprice = trim($params['price']);
            $stockType = trim($params['type']);

            $error = "";

            if (strlen($stockname) > 50)
                $error .= "Le nom doit être compris entre 1 et 50 caractères. ";

            if (!empty($error)) {
                $this->_helper->json(array(
                    'code' => $error
                ));
            }

            $stock = new Application_Model_DbTable_Stock();
            // create the new stock
            $verif = $stock->createStock($stockname, $stocknumber, $stockprice, $stockType);

            if ($verif == "ok") {
                //$mailer = new Application_Model_Mailer();
                //$mailer->sendCreateAccountNotification($email, $stockname, $password);
                $msg = '42';
            }else
            //$msg = "Un utilisateur possède déjà ce nom d'utilisateur ou cet email";
                $msg = 'erreur';

            $this->_helper->json(array(
                'code' => $msg
            ));
        }
    }
    
    // Création et envoie par mail du ticket
    public function sendAction() {
        // Création du ticket => image
        $id = $this->getRequest()->getParam("ticket");
        $this->createticket($id);
        
        // récupération de l'id de l'utilisateur
        $auth = Zend_Auth::getInstance();
        $userId = $auth->getIdentity()->UserId;
        
        // récupération de l'email de l'utilisateur
        $userTab = new Application_Model_DbTable_User();
        $user = $userTab->fetchOne($userId);
        
        // récupération de la date du repas
        $reservationTab = new Application_Model_DbTable_Reservation();
        $reservation = $reservationTab->getReservationByUserAndId($userId, $id);
        
        // définition des variables
        $path = "../public/attachment/";
        $ticketName = 'ticket'.str_replace("/", "-", $reservation[0]['ReservationDateRepas']).'.png';
        $QRName = 'QRcode'.$id.'.png';
        
        // envoie par mail
        $mailer = new Application_Model_Mailer();
        $mailer->sendTicket($user['UserEmail'], $reservation[0]['ReservationDateRepas'], $path.$ticketName, $ticketName);
        
        // suppression de l'image sur le serveur
        unlink($path.$ticketName);
        unlink($path.$QRName);
    }
    
    // Création et enregistrement du ticket
    public function downloadAction() {
        // Création du ticket => image
        $id = $this->getRequest()->getParam("ticket");
        $this->createticket($id);
        
        // Chemin et nom du png
        $savedir = '../public/attachment/';
        $QRName = 'QRcode'.$id.'.png';
        $ticketName = "ticket".$id.".png";
        $path = $savedir.$ticketName;

        // Récupération du type mime de l'extension
        $extension = substr($path, strrpos($path, "."));
        $mime = Application_Model_Filexplorer::getMimeTypeFromExtension($extension);
        if(!file_exists($path)){
            //file not found
            $this->_helper->redirector("error404", "error");
        }
        // Boite de telechargement
        $this->getResponse()
        ->setHeader('Content-type', $mime)
        ->setHeader('Content-Disposition',  'attachment; filename="'.$ticketName.'"')
        ->setHeader('Content-Length', filesize($path));
        readfile($path);

        // Désactiver la redirection vers une autre page
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        // Suppression des l'images sur le serveur
        unlink($savedir.$ticketName);
        unlink($savedir.$QRName);
    }
    
    // Création du ticket au format PNG
    public function createticket($id) {
        //--- Récupération des informations sur le ticket ---//
        // Information sur la reservation
        $auth = Zend_Auth::getInstance();
        $reservationTab = new Application_Model_DbTable_Reservation();
        $reservation = $reservationTab->getReservationByUserAndId($auth->getIdentity()->UserId, $id);
        // Information sur la commande
        $stockTab = new Application_Model_DbTable_Stock();
        $ids = explode(',', $reservation[0]['StockIds']);
        $menu = [];
        foreach ($ids as $id) {
            $stock = $stockTab->getStockById($id);
            $menu[$stock[0]['StockType']][$stock[0]['id']] = $stock[0];
        }
        // Information sur l'utilisateur
        $userTab = new Application_Model_DbTable_User();
        $userInfo = $userTab->fetchOne($auth->getIdentity()->UserId);
        $user = $userInfo['UserFirstName'].' '.$userInfo['UserLastName'];
        // Chemin vers le dossier de sauvegarde
        $savedir = '../public/attachment/';
        
        //--- Calcul de la zone d'image ---//
        $widthZone = 350;
        $heightZone = $this->calculzone($menu); //à modifier => dynamique
        
        //--- Création de la zone d'image ---//
        $image = imageCreate($widthZone ,$heightZone);
        
        //--- Couleurs et police ---//
        imageColorAllocate($image, '255', '255', '255'); //couleur du fond => blanc
        $noir = imageColorAllocate($image, 0, 0, 0); //couleur du texte => noir
        $regular = "../public/styles/police/OpenSans-Regular.ttf"; //police normal
        $bold = "../public/styles/police/OpenSans-Bold.ttf"; // police gras
        
        //--- Création des bordures ---//
        imageRectangle($image, 0,0, $widthZone-1,$heightZone-1, $noir);
        
        //--- Texte ---//
        // Titre
        imagettftext($image, 12, 0, 135, 20, $noir, $bold, "SpeedSelf");
        // Informations sur le ticket
        imagettftext($image, 10, 0, 40, 50, $noir, $bold, "Ticket numero :");
        imagettftext($image, 10, 0, 170, 50, $noir, $regular, $reservation[0]['ReservationId']);
        imagettftext($image, 10, 0, 40, 70, $noir, $bold, "Date :");
        imagettftext($image, 10, 0, 170, 70, $noir, $regular, $reservation[0]['ReservationDateRepas']);
        imagettftext($image, 10, 0, 40, 90, $noir, $bold, "Beneficiaire :");
        imagettftext($image, 10, 0, 170, 90, $noir, $regular, $user);
        // Entete du tableau
        imagettftext($image, 10, 0, 120, 120, $noir, $bold, "Nom");
        imagettftext($image, 10, 0, 280, 120, $noir, $bold, "Prix");
        // Entrée
        $height = 140;
        imagettftext($image, 10, 0, 40, 140, $noir, $bold, "Entrée");
        if(!isset($menu['Entrée'])){
            $height += 20;
            imagettftext($image, 10, 0, 120, $height, $noir, $regular, "--");
            imagettftext($image, 10, 0, 290, $height, $noir, $regular, "--");
        }else{
            foreach ($menu['Entrée'] as $key => $value) { 
                $height += 20;
                imagettftext($image, 10, 0, 120, $height, $noir, $regular, $value['StockName']);
                imagettftext($image, 10, 0, 290, $height, $noir, $regular, $value['StockPrice']);
            }
        }$height += 20;
        // Plat
        imagettftext($image, 10, 0, 40, $height, $noir, $bold, "Plat");
        if(!isset($menu['Plat'])){
            $height += 20;
            imagettftext($image, 10, 0, 120, $height, $noir, $regular, "--");
            imagettftext($image, 10, 0, 290, $height, $noir, $regular, "--");
        }else{
            foreach ($menu['Plat'] as $key => $value) { 
                $height += 20;
                imagettftext($image, 10, 0, 120, $height, $noir, $regular, $value['StockName']);
                imagettftext($image, 10, 0, 290, $height, $noir, $regular, $value['StockPrice']);
            }
        }$height += 20;
        // Dessert
        imagettftext($image, 10, 0, 40, $height, $noir, $bold, "Dessert");
        if(!isset($menu['Dessert'])){
            $height += 20;
            imagettftext($image, 10, 0, 120, $height, $noir, $regular, "--");
            imagettftext($image, 10, 0, 290, $height, $noir, $regular, "--");
        }else{
            foreach ($menu['Dessert'] as $key => $value) { 
                $height += 20;
                imagettftext($image, 10, 0, 120, $height, $noir, $regular, $value['StockName']);
                imagettftext($image, 10, 0, 290, $height, $noir, $regular, $value['StockPrice']);
            }
        }$height += 20;
        // Total
        imagettftext($image, 10, 0, 40, $height, $noir, $bold, "Total");
        imagettftext($image, 10, 0, 280, $height, $noir, $regular, $reservation[0]['ReservationMontant'].' €');
        $height += 40;
        
        //--- QRcode ---//
        require_once '../library/phpqrcode/qrlib.php';
        // Creation des variables du QRcode
        $qrValue = 'test';
        $QRName = 'QRcode'.$reservation[0]['ReservationId'].'.png';
        // Creation et enregistrement du QRcode
        QRcode::png($qrValue, $savedir.$QRName, QR_ECLEVEL_M, 4, 1);
        
        //--- Enregistrement de l'image ---//
        $ticketName = "ticket".$reservation[0]['ReservationId'].".png";
        imagepng($image, $savedir.$ticketName);
        
        //--- Fusion du ticket et du QRcode ---//
        // Chargement des images
        $source = imagecreatefrompng($savedir.$QRName); // Le QRcode est la source
        $destination = imagecreatefrompng($savedir.$ticketName); // Le ticket est la destination
        // Les fonctions imagesx et imagesy renvoient la largeur et la hauteur d'une image
        $largeur_source = imagesx($source);
        $hauteur_source = imagesy($source);
        $destination_x = 129;
        $destination_y =  $height;
        // On met la source dans l'image de destination
        imagecopymerge($destination, $source, $destination_x, $destination_y, 0, 0, $largeur_source, $hauteur_source, 100);
        imagepng($destination, $savedir.$ticketName);
        
    }
    
    // Calcule la hauteur du ticket pour y faire rentrer toutes les informations
    public function calculzone($menu){
        $height = 140;
        if(!isset($menu['Entrée'])){
            $height += 20;
        }else{
            foreach ($menu['Entrée'] as $key => $value) { 
                $height += 20;
            }
        }
        $height += 20; // saut de ligne
        $height += 20; // plat
        if(!isset($menu['Plat'])){
            $height += 20;
        }else{
            foreach ($menu['Plat'] as $key => $value) { 
                $height += 20;
            }
        }
        $height += 20; // saut de ligne
        $height += 20; // dessert
        if(!isset($menu['Dessert'])){
            $height += 20;
        }else{
            foreach ($menu['Dessert'] as $key => $value) { 
                $height += 20;
            }
        }
        $height += 20; // saut de ligne
        $height += 20; // total
        $height += 40; // saut de ligne
        $height += 100; // QRcode
        
        return $height;
    }
    
}
=======
<?php

/**
 * Description of RepasController
 *
 * @author ddjk5031
 */
class RepasController extends Zend_Controller_Action {
    
    public function init()
    {
        $auth = Zend_Auth::getInstance();
    
        //If stock is not connected, redirect to logout
        if (!$auth->hasIdentity()) {
            $this->_helper->redirector('logout', 'auth', null, array('reason' => '1')); // back to login page
        }
    }
    
    public function indexAction(){
        
    }
    
    public function historiqueAction(){
        //Verifie si Utilisateur
        $auth = Zend_Auth::getInstance();
        $access = $auth->getIdentity()->IdAccessF;
        if ($access == 1){
            $this->_helper->redirector('logout', 'auth', null, array('reason' => '1')); // back to login page
        }
        
        
        
    }
    
    public function gestionstockAction($msg = null){
        //Verifie si Administrateur
        $auth = Zend_Auth::getInstance();
        $access = $auth->getIdentity()->IdAccessF;
        if ($access == 2){
            $this->_helper->redirector('logout', 'auth', null, array('reason' => '1')); // back to login page
        }
        
        // Form for the creation of a stock
        $form = new Application_Form_CreateStockForm();
        $form->createForm();
        $form->setAction($this->_helper->url('createstock', 'repas'));
        $this->view->form = $form;
        
        //Form for the edit of a stock
        $editForm = new Application_Form_EditStockForm();
        $editForm->createForm();
        $editForm->setAction($this->_helper->url('editstock', 'repas'));
        $this->view->editForm = $editForm;
        
        // Form to get all the stocks
        $stock = new Application_Model_DbTable_Stock();
        $stocks = $stock->getAllStock();
        //passage des variables à la vue
        $msg =$this->getRequest()->getParam('msg');
        $this->view->msg = $msg;
        $this->view->stocks = $stocks;
        
        
    }
    
    /**
     * Edit a stock
     */
    public function editstockAction()
    {
        // Check Post
        if (!$this->getRequest()->isPost())
            die();
        
        $params = $this->getRequest()->getParams();
        $idStock = $params['idStock'];
        $StockName = $params['StockName'];
        $StockNumber = $params['StockNumber'];
        $StockPrice = $params['StockPrice'];
        $StockType = $params['StockType'];


        // Check if firstname's and lastname's lenght is between 1 and 50

        if (strlen($StockName)>50 ) {
            $error = "Le nom doit être compris entre 1 et 50 caractère";
            $this->_helper->json(array(
                'code' => $error
            ));
        }

        $stock = new Application_Model_DbTable_Stock();
        // Update the stock
        $verif = $stock->updateStock($idStock, $StockName, $StockNumber, $StockPrice, $StockType);

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
     * This action is used to delete a stock
     */
    public function deletestockAction()
    {
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getParams();
            $idStock = $params['idStock'];
            $stock = new Application_Model_DbTable_Stock();
            // delete the stock
            $verif = $stock->deleteStock($idStock);
            if ($verif == "ok") {
                $msg = 'L\'utilisateur a bien été supprimé';
            } else
                $msg = $verif;
            
            // Return to the page informations
            $this->_helper->redirector('gestionstock','repas', null);

        }
        
    }
    
    /**
     * This action hydrate the formular of the edit of the stock
     * 
     */
    public function hydrateeditstockformAction(){
        
        // Check Post
        if (!$this->getRequest()->isPost()){
            die();
        }
        $params = $this->getRequest()->getParams();
        $idStock = $params['idStock'];
        
        $stockTab = new Application_Model_DbTable_Stock();
        $stock = $stockTab->fetchOne($idStock);
        //$this->_helper->json($stock);
        $this->_helper->json(array(
            'StockName'     => $stock['StockName'],
            'StockNumber'   => $stock['StockNumber'],
            'StockPrice'    => $stock['StockPrice'],
            'StockType'    => $stock['StockType']
        ));
      
    }
    
    /**
     * This action is used to create a stock
     */
    public function createstockAction()
    {
        
        // Check Post
        if ($this->getRequest()->isPost()) {
            
            $params = $this->getRequest()->getParams();
            $stockname = trim($params['name']);
            $stocknumber = trim($params['number']);
            $stockprice = trim($params['price']);
            $stockType = trim($params['type']);
            
            $error = "";
            
            if (strlen($stockname)>50 ) 
                $error .= "Le nom doit être compris entre 1 et 50 caractères. ";
                
            if(!empty($error)){
                $this->_helper->json(array(
                    'code' => $error
                ));
            }
            
            $stock = new Application_Model_DbTable_Stock();
            // create the new stock
            $verif = $stock->createStock($stockname, $stocknumber, $stockprice, $stockType);
            
            if ($verif == "ok") {
                //$mailer = new Application_Model_Mailer();
                //$mailer->sendCreateAccountNotification($email, $stockname, $password);
                $msg = '42';
            } else
                //$msg = "Un utilisateur possède déjà ce nom d'utilisateur ou cet email";
                $msg = 'erreur';
            
            $this->_helper->json(array(
                'code' => $msg
            ));
        }
    }
    
    public function reservationAction() {
        //Verifie si Utilisateur
        $auth = Zend_Auth::getInstance();
        $access = $auth->getIdentity()->IdAccessF;
        if ($access == 1){
            $this->_helper->redirector('logout', 'auth', null, array('reason' => '1')); // back to login page
        }
        $this->genereQR();
        
    }
    
    public function genereQR(){
        include ("../library/phpqrcode.php");
        QRcode::png('code data text', '../library/qrTest.png');
    }
    
    
}
>>>>>>> 4aa32df1b73ab9b8f0b37ade4b6f9096072ebc8a
