<?php
/**
 * Created by PhpStorm.
 * User: DDJK5031
 * Date: 03/12/2015
 * Time: 15:31
 */

class Application_Model_DbTable_Menu extends Zend_Db_Table_Abstract {
    protected $_name = 'menu';  // table name in the database
    protected $_primary = 'MenuId'; //primary key
    protected $_stockIdF = 'StockIdF'; //stock id Foreign key
    protected $_menuDate = 'MenuDate'; //date

     
    public function getMenuByDate($date)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('M'=>$this->_name));

        //join with the user
        $select->joinLeft(array(
            'S' =>'Stock'
        ), 'M.StockIdF = S.StockId');
        
        $select->where($this->_menuDate.' = ?', $date);
        $select->where('StockNumber > 0');
        $res = $this->fetchAll($select);
        
        
        if($res == null || $res->count() == 0)
            return null;
        else
        {
            $returned_arr = array();
            foreach($res as $row)
            {
                $returned_arr[] = array(
                    'StockName'     => $row->StockName,
                    'StockPrice'    => $row->StockPrice,
                    'StockType'     => $row->StockType,
                    'MenuDate'      => $row->MenuDate,
                );
            }
            return $returned_arr;
        }
    }
    
    public function getMenuByDateAndType($date, $type)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->order('StockName');
        $select->from(array('M'=>$this->_name));

        //join with the user
        $select->joinLeft(array(
            'S' =>'Stock'
        ), 'M.StockIdF = S.StockId');
        
        $select->where($this->_menuDate.' = ?', $date);
        $select->where('StockType = ?', $type);
        $select->where('StockNumber > 0');
        $res = $this->fetchAll($select);
        
        
        if($res == null || $res->count() == 0)
            return null;
        else
        {
            $returned_arr = array();
            foreach($res as $row)
            {
                $returned_arr[] = array(
                    'StockId'       => $row->StockId,
                    'StockName'     => $row->StockName,
                    'StockPrice'    => $row->StockPrice,
                    'MenuDate'      => $row->MenuDate,
                );
            }
            return $returned_arr;
        }
    }
    
    public function deleteMenuByDate($date) {
        try{
            $where = array(
                $this->_menuDate.'= ?' => $date
            );
            $this->delete($where);  
            return "ok";
            
        }catch (Zend_Exception $e){
            return $e->getMessage();
        }
    }
    
    public function insertMenu($date, $stockId) {
        try{
            $insert = $this->insert(array(
                $this->_stockIdF    => $stockId ,
                $this->_menuDate    => $date ,
            ));
            return "ok";
    
        }catch(Zend_Exception $e){
            return $e->getMessage();
        }
    }
    
    public function getUsedStockByDateAndId($date, $id){
        $select = $this->select();
        $select->setIntegrityCheck(false);
        
        $select->where($this->_menuDate.' >= ?', $date);
        $select->where($this->_stockIdF. ' = ?', $id);
        $res = $this->fetchAll($select);
        
        if($res == null || $res->count() == 0){
            return 0;
        }else{
            return 1;
        }
    }
}


