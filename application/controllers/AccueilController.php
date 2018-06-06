<?php
/**
 * @author Cyprien POUDEVIGNE
 */

class AccueilController extends Zend_Controller_Action
{
    public function init()
    {
        /*$auth = Zend_Auth::getInstance();
    
        //If user is not connected, redirect to logout
        if (!$auth->hasIdentity()) {
            $this->_helper->redirector('logout', 'auth', null, array('reason' => '1')); // back to login page
        }*/
    }
    
    public function indexAction(){
        
        //Fonction de récupération des dates de la semaine et du menu
        $menuTab = new Application_Model_DbTable_Menu();
        $reservationTab = new Application_Model_DbTable_Reservation();
        $auth = Zend_Auth::getInstance();
        $userId = $auth->getIdentity()->UserId;
        $w = date("W");
        //$date = [];
        $date = ['0'=>'19/03/2018','1'=>'20/03/2018','2'=>'21/03/2018','3'=>'22/03/2018','4'=>'23/03/2018'];//pour les tests
        $entreeData = [];
        for($i = 1; $i <= 365; $i++) {
            $week = date("W", mktime(0, 0, 0, 1, $i, date("Y")));
            if($week == $w) {
                for($d = 0; $d < 5; $d++) {
                    //$date[$d] = date("d/m/Y", mktime(0, 0, 0, 1, $i+$d, date("Y")));
                    $entreeData[$d+1] = $menuTab->getMenuByDateAndType($date[$d], 'Entrée');
                    $platData[$d+1] = $menuTab->getMenuByDateAndType($date[$d], 'Plat');
                    $dessertData[$d+1] = $menuTab->getMenuByDateAndType($date[$d], 'Dessert');
                    $reservation[$d+1] = $reservationTab->getReservationByUserAndDate($userId, $date[$d]);
                }
                break;
            }
        }
        $entree = $this->miseEnForme($entreeData);
        $plat = $this->miseEnForme($platData);
        $dessert = $this->miseEnForme($dessertData);
        
        $this->view->entree = $entree;
        $this->view->plat = $plat;
        $this->view->dessert = $dessert;
        $this->view->reservation = $reservation;
        
        //Tableau des jours de la semaine en Français
        $jours = array('1'=>'Lundi','2'=>'Mardi','3'=>'Mercredi','4'=>'Jeudi','5'=>'Vendredi',);
        
        $this->view->jours = $jours;
        $this->view->date = $date;
        
    }
    
    function reservationAction() {
        // Check Post
        if (!$this->getRequest()->isPost())
            die();
        
        $params = $this->getRequest()->getParams();
        $date = $params['date'];
        $day = $params['day'];
        
        $menuTab = new Application_Model_DbTable_Menu();
        $this->view->entree = $menuTab->getMenuByDateAndType($date, 'Entrée');
        $this->view->plat = $menuTab->getMenuByDateAndType($date, 'Plat');
        $this->view->dessert = $menuTab->getMenuByDateAndType($date, 'Dessert');
        
        //Tableau des jours de la semaine en Français
        $jours = array('1'=>'Lundi','2'=>'Mardi','3'=>'Mercredi','4'=>'Jeudi','5'=>'Vendredi');
        $this->view->day = $jours[$day];
        $this->view->date = $date;
    }
    
    function reservationformAction() {
        $auth = Zend_Auth::getInstance();
        $params = $this->getRequest()->getParams();
        $userTab = new Application_Model_DbTable_User();
        $reservationTab = new Application_Model_DbTable_Reservation();
        $stockTab = new Application_Model_DbTable_Stock();
        
        // Verifie si le panier est rempli
        if ($params['total'] == 0) {
            $this->_helper->json(array(
                'code' => '-1',
                'msg' => 'Vous ne pouvez pas enregister un panier vide'
            ));
            die();
        }
        // Verifie si le solde est suffisant
        $userSolde = $userTab->getUserSolde($auth->getIdentity()->UserId);
        $solde = $userSolde - $params['total'];
        if ($solde < 0) {
            $this->_helper->json(array(
                'code' => '-1',
                'msg' => 'Votre solde est insuffisant, rechargez votre compte avant de reserver'
            ));
            die();
        }
        
        // Creation de la liste des id des éléments du repas
        $stockChaine = '';
        foreach ($params['menu'] as $id => $value) {
            $stockChaine .= ''.$id.',';
            // Modification du stock
            $stock = $stockTab->getStockById($id);
            $stockTab->updateStockNumber($id, $stock[0]['StockNumber']-1);
        }
        $stockIds = $this->str_replace_last(',', '', $stockChaine);
        
        // Creation de la reservation
        $reservationTab->createReservation($auth->getIdentity()->UserId, date('d/m/Y'), $params['date'], $params['total'], $stockIds);
        
        // Modification du solde
        $userTab->updateSolde($auth->getIdentity()->UserId, $solde);

        $this->_helper->json(array(
            'code' => '1'
        ));
        die();
        
    }
    
    function editdaymenuAction(){
        $auth = Zend_Auth::getInstance();
        //If user is not allowed, redirect to accueil
        if ($auth->getIdentity()->IdAccessF != 1) {
            $this->_helper->redirector('accueil', 'index', null, true);
        }
        // Check Post
        if (!$this->getRequest()->isPost())
            die();
        
        $params = $this->getRequest()->getParams();
        $date = $params['date'];
        $day = $params['day'];
        
        // Récupération des items déjà selectionné dans le menu
        $menuTab = new Application_Model_DbTable_Menu();
        
        // Creation de la liste des ids des entrees
        $this->view->entreeselected = $menuTab->getMenuByDateAndType($date, 'Entrée');
        if($this->view->entreeselected != NULL){
            foreach ($this->view->entreeselected as $entree) {
                $entreeIds[$entree['StockId']] = $entree['StockName'];
            }$this->view->entreeIds = $entreeIds;
        }else{
            $this->view->entreeIds = NULL;
        }
        
        // Creation de la liste des ids des plats
        $this->view->platselected = $menuTab->getMenuByDateAndType($date, 'Plat');
        if($this->view->platselected != NULL){
            foreach ($this->view->platselected as $plat) {
                $platIds[$plat['StockId']] = $plat['StockName'];
            }$this->view->platIds = $platIds;
        }else{
            $this->view->platIds = NULL;
        }
        
        // Creation de la liste des ids des desserts
        $this->view->dessertselected = $menuTab->getMenuByDateAndType($date, 'Dessert');
        if($this->view->dessertselected != NULL){
            foreach ($this->view->dessertselected as $dessert) {
                $dessertIds[$dessert['StockId']] = $dessert['StockName'];
            }$this->view->dessertIds = $dessertIds;
        }else{
            $this->view->dessertIds = NULL;
        }
        
        // Récupération des ids utilisé dans les réservation (pour les bloquer dans la modification des menus)
        $reservationTab = new Application_Model_DbTable_Reservation();
        $usedIds = $reservationTab->getReservationByDate($date);
        $usedId = '';
        if ($usedIds == NULL){
            $this->view->idArray = array(0);
        }else{
            foreach($usedIds as $ids){
                $usedId .= $ids['StockIds'].',';
            }
            $idArray = explode(',', $usedId);
            $this->view->idArray = array_unique($idArray);
        }
        
        // Récupération des items non selectionné dans le menu
        $stockTab = new Application_Model_DbTable_Stock();
        $this->view->entreelist = $stockTab->getStockByType('Entrée');
        $this->view->platlist = $stockTab->getStockByType('Plat');
        $this->view->dessertlist = $stockTab->getStockByType('Dessert');
        
        //Tableau des jours de la semaine en Français
        $jours = array('1'=>'Lundi','2'=>'Mardi','3'=>'Mercredi','4'=>'Jeudi','5'=>'Vendredi');
        $this->view->day = $jours[$day];
        $this->view->date = $date;
        
    }
    
    function editdayformAction() {
        // Check Post
        if (!$this->getRequest()->isPost())
            die();
        $params = $this->getRequest()->getParams();
        
        if (empty($params['entree'])) {
            $this->_helper->json(array(
                'code' => '-1',
                'msg' => 'Vous devez avoir au minimum une entrée pour valider'
            ));
            die();
        }
        if (empty($params['plat'])) {
            $this->_helper->json(array(
                'code' => '-1',
                'msg' => 'Vous devez avoir au minimum un plat pour valider'
            ));
            die();
        }
        if (empty($params['dessert'])) {
            $this->_helper->json(array(
                'code' => '-1',
                'msg' => 'Vous devez avoir au minimum un dessert pour valider'
            ));
            die();
        }
        
        /*var_dump('ici');
        die();*/
        
        $menuTab = new Application_Model_DbTable_Menu();
        //Delete the old menu
        $menuTab->deleteMenuByDate($params['date']);
        //Insert the new elements of the menu
        $stockIds = $params['entree'].','.$params['plat'].','.$params['dessert'];
        $tabStockId = explode(',', $stockIds);
        foreach ($tabStockId as $stockId){
            $menuTab->insertMenu($params['date'], $stockId);
        }
        
        $this->_helper->json(array(
            'code' => '1'
        ));
        die();
        
    }
    
    function editweekmenuAction(){
        $auth = Zend_Auth::getInstance();
        //If user is not allowed, redirect to accueil
        if ($auth->getIdentity()->IdAccessF != 1) {
            $this->_helper->redirector('accueil', 'index', null, true);
        }
        // Check Post
        if (!$this->getRequest()->isPost())
            die();
        
        $params = $this->getRequest()->getParams();
        
        
    }
    
    public function miseEnForme($tableauDepart) {
        $tableauFin = [];
        foreach ($tableauDepart as $key => $value) {
            if(is_null($value)){
                continue;
            }
            foreach ($value as $ky => $val) {
                $tableauFin[$ky+1][$key] = $val['StockName'];
            }
        }
        return $tableauFin;
    }
    
    public function str_replace_last($search ,$replace ,$str) {
        if(($pos = strrpos($str ,$search)) !== false) {
            $search_length = strlen($search);
            $str = substr_replace($str ,$replace ,$pos ,$search_length);
        }
        return $str;
    }
    
}
