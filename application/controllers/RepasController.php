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
        
    }
    
    
    
}
