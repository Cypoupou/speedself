<?php

class Application_Model_DbTable_Historique extends Zend_Db_Table_Abstract {
    protected $_name = 'historique';  // table name in the database
    protected $_primary = 'HistoriqueId'; //primary key
    protected $_userIdF = 'UserIdF';
    protected $_historiqueDate = 'HistoriqueDate';
    protected $_historiqueReference = 'HistoriqueReference';
    protected $_historiqueMontant = 'HistoriqueMontant';
    
    
    public function init(){
        
    }
    
    
    /**
     * Insert a stock in the database
     * @param $stockname, $firstname, $lastname, $email, $password, $signature, $access
     * @return ok if correct, else return the exception message
     */
    public function createHistorique($stockName, $stockNumber, $stockPrice, $stockType){
        try{
            $insert = $this->insert(array(
                $this->_stockName   => $stockName ,
                $this->_stockNumber => $stockNumber ,
                $this->_stockPrice  => $stockPrice ,
                $this->_stockType  => $stockType ,
            ));
            return "ok";
    
        }catch(Zend_Exception $e){
            return $e->getMessage();
        }
    }

    
    public function getHistoriqueByUser($userId) {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        
        $select->where($this->_userIdF.' = ?', $userId);
        $res = $this->fetchAll($select);

        if($res == null || $res->count() == 0)
            return null;
        else {
            $returned_arr = array();
            foreach($res as $row) {
                // Formatage de la date
                $historiqueDate = strtotime($row->HistoriqueDate);
                $historiqueDateUS = date('Y-m-d', $historiqueDate);
                    
                $returned_arr[] = array(
                    'HistoriqueId'          => $row->HistoriqueId,
                    'HistoriqueDate'        => $row->HistoriqueDate,
                    'HistoriqueDateUS'      => $historiqueDateUS,
                    'HistoriqueReference'   => $row->HistoriqueReference,
                    'HistoriqueMontant'     => $row->HistoriqueMontant,
                );
            }
            return $returned_arr;
        }
    }

}
