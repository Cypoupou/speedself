<?php

class Application_Model_DbTable_Reservation extends Zend_Db_Table_Abstract {
    protected $_name = 'reservation';  // table name in the database
    protected $_primary = 'ReservationId'; //primary key
    protected $_userIdF = 'UserIdF';
    protected $_reservationDate = 'ReservationDate';
    protected $_reservationDateRepas = 'ReservationDateRepas';
    protected $_reservationMontant = 'ReservationMontant';
    protected $_stockIds = 'StockIds';
    
    
    public function init(){
        
    }
    
    
    public function createReservation($userIdF, $reservationDate, $reservationDateRepas, $reservationMontant, $stockIds){
        try{
            $insert = $this->insert(array(
                $this->_userIdF   => $userIdF ,
                $this->_reservationDate => $reservationDate ,
                $this->_reservationDateRepas  => $reservationDateRepas ,
                $this->_reservationMontant  => $reservationMontant ,
                $this->_stockIds  => $stockIds ,
            ));
            return "ok";
    
        }catch(Zend_Exception $e){
            return $e->getMessage();
        }
    }

    
    public function getReservationByUser($userId) {
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
                $reservationDate = strtotime($row->ReservationDate);
                $reservationDateUS = date('Y-m-d', $reservationDate);
                
                $reservationDateRepas = strtotime($row->ReservationDateRepas);
                $reservationDateRepasUS = date('Y-m-d', $reservationDateRepas);
                    
                $returned_arr[] = array(
                    'ReservationId'          => $row->ReservationId,
                    'ReservationDate'        => $row->ReservationDate,
                    'ReservationDateUS'      => $reservationDateUS,
                    'ReservationDateRepas'   => $row->ReservationDateRepas,
                    'ReservationDateRepasUS' => $reservationDateRepasUS,
                    'ReservationMontant'     => $row->ReservationMontant,
                );
            }
            return $returned_arr;
        }
    }
    
    public function getReservationByUserAndId($userId, $id) {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        
        $select->where($this->_userIdF.' = ?', $userId);
        $select->where('ReservationId = ?', $id);
        $res = $this->fetchAll($select);

        if($res == null || $res->count() == 0)
            return null;
        else {
            $returned_arr = array();
            foreach($res as $row) {
                // Formatage de la date
                $reservationDate = strtotime($row->ReservationDate);
                $reservationDateUS = date('Y-m-d', $reservationDate);
                
                $reservationDateRepas = strtotime($row->ReservationDateRepas);
                $reservationDateRepasUS = date('Y-m-d', $reservationDateRepas);
                    
                $returned_arr[] = array(
                    'ReservationId'          => $row->ReservationId,
                    'ReservationDate'        => $row->ReservationDate,
                    'ReservationDateUS'      => $reservationDateUS,
                    'ReservationDateRepas'   => $row->ReservationDateRepas,
                    'ReservationDateRepasUS' => $reservationDateRepasUS,
                    'ReservationMontant'     => $row->ReservationMontant,
                    'StockIds'               => $row->StockIds,
                );
            }
            return $returned_arr;
        }
    }
    
    public function getReservationByUserAndDate($userId, $dateRepas) {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        
        $select->where($this->_userIdF.' = ?', $userId);
        $select->where($this->_reservationDateRepas.' = ?', $dateRepas);
        $res = $this->fetchAll($select);
        
        if($res == null || $res->count() == 0){
            return 0;
        }else {
            $returned_arr = array();
            foreach($res as $row) {
                $returned_arr[$row->ReservationDateRepas] = $row->ReservationId;
            }
            return $returned_arr;
        }
    }
    
    public function getReservationByDate($dateRepas) {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        
        $select->where($this->_reservationDateRepas.' = ?', $dateRepas);
        $res = $this->fetchAll($select);

        if($res == null || $res->count() == 0)
            return null;
        else {
            $returned_arr = array();
            foreach($res as $row) {
                $returned_arr[] = array(
                    'StockIds'  => $row->StockIds,
                );
            }
            return $returned_arr;
        }
    }

}
