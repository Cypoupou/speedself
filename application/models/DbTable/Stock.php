<?php

class Application_Model_DbTable_Stock extends Zend_Db_Table_Abstract {
    protected $_name = 'stock';  // table name in the database
    protected $_primary = 'StockId'; //primary key
    protected $_stockName = 'StockName';
    protected $_stockNumber = 'StockNumber';
    protected $_stockPrice = 'StockPrice';
    protected $_stockType = 'StockType';
    
    
    public function init(){
        
    }
    
    /**
     * fetch one stock
     * @param IdStock
     * @Return a row
     */
    public function fetchOne($idStock){
    

        $select = $this->select();
        $select->where($this->_primary[1].' = ?', $idStock);
        $row = $this->fetchRow($select);
        return $row;
    
    }
    
    /**
     * Update the stock
     * @param idStock, firstname, lastname, email, signature, accees
     * @return ok if correct, else return the exception message
     */
    public function updateStock($idStock, $stockName, $stockNumber, $stockPrice, $stockType){
        try{
            $update = $this->update(array(
                $this->_stockName   => $stockName ,
                $this->_stockNumber => $stockNumber ,
                $this->_stockPrice  => $stockPrice ,
                $this->_stockType  => $stockType ,
            ), array(
                $this->_primary.'= ?' => $idStock ));
            return "ok";
        }catch (Zend_Exception $e){
            return $e->getMessage();
        }
    }
    
    

    /**
     * Delete a stock
     * @param idStock
     * @return ok if correct, else return exception message
     */
    public function deletestock($idStock)
    {
        
        try{
            $where = array(
                $this->_primary.'= ?' => $idStock
            );
            $this->delete($where);  
            return "ok";
            
        }catch (Zend_Exception $e){
            return $e->getMessage();
        }
    }
    
    /**
     * Insert a stock in the database
     * @param $stockname, $firstname, $lastname, $email, $password, $signature, $access
     * @return ok if correct, else return the exception message
     */
    public function createStock($stockName, $stockNumber, $stockPrice, $stockType)
    {
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

    
    public function getAllStock() {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $res = $this->fetchAll($select);

        if($res == null || $res->count() == 0)
            return null;
        else {
            $returned_arr = array();
            foreach($res as $row) {
                $returned_arr[] = array(
                    'id'  => $row->StockId,
                    'StockName' => $row->StockName,
                    'StockNumber'  => $row->StockNumber,
                    'StockPrice' => $row->StockPrice,
                    'StockType' => $row->StockType,
                );
            }
            return $returned_arr;
        }
    }
    

}
