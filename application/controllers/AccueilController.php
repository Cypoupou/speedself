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
    
    public function indexAction($param = null){
        
        //Fonction de récupération des dates de la semaine et du menu
        $menuTab = new Application_Model_DbTable_Menu();
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
        
        //Tableau des jours de la semaine en Français
        $jours = array('1'=>'Lundi','2'=>'Mardi','3'=>'Mercredi','4'=>'Jeudi','5'=>'Vendredi',);
        
        $this->view->jours = $jours;
        $this->view->date = $date;
        
    }
    
    /*function reservationFormAction() {
        // Check Post
        if (!$this->getRequest()->isPost())
            die();
        
        $params = $this->getRequest()->getParams();
        $champsId = $params['champsId'];
        
        $menuTab = new Application_Model_DbTable_Menu();
        $menu = $menuTab->getMenuByDate($date);
    }
    
    function reservationAction() {
        
    }*/
    
    function editweekAction(){
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

    public function reserverAction() {
        //Verifie si Utilisateur
        $auth = Zend_Auth::getInstance();
        $access = $auth->getIdentity()->IdAccessF;
        if ($access == 1){
            $this->_helper->redirector('logout', 'auth', null, array('reason' => '1')); // back to login page
        }
        
        // Check Post
        if (!$this->getRequest()->isPost())
            die();
        $params = $this->getRequest()->getParams();
        
        $this->view->date = $params['date'];
        
        $menuTab = new Application_Model_DbTable_Menu();
        $entreeData = $menuTab->getMenuByDateAndType($params['date'], 'Entrée');
        $platData = $menuTab->getMenuByDateAndType($params['date'], 'Plat');
        $dessertData = $menuTab->getMenuByDateAndType($params['date'], 'Dessert');
        $this->view->entree = $entreeData;
        $this->view->plat = $platData;
        $this->view->dessert = $dessertData;
        
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
    
}
