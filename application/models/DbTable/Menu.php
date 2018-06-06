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

<<<<<<< HEAD
     
=======
    
    /**
     * Get one menu with her id
     * @return NULL|array: return the result or null if there is no result
     */
    public function getMenuById($idMenu)
    {

        $select = $this->select();
        $select->where($this->_primary[1].' = ?', $idMenu);
        $row = $this->fetchRow($select);

        if($row == null)
            return null;
        else
            return $row;

    }
    
>>>>>>> 4aa32df1b73ab9b8f0b37ade4b6f9096072ebc8a
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
<<<<<<< HEAD
        $select->where('StockNumber > 0');
=======
>>>>>>> 4aa32df1b73ab9b8f0b37ade4b6f9096072ebc8a
        $res = $this->fetchAll($select);
        
        
        if($res == null || $res->count() == 0)
            return null;
        else
        {
            $returned_arr = array();
            foreach($res as $row)
            {
<<<<<<< HEAD
                $returned_arr[] = array(
=======
                $returned_arr[$row->MenuId] = array(
>>>>>>> 4aa32df1b73ab9b8f0b37ade4b6f9096072ebc8a
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
<<<<<<< HEAD
        $select->order('StockName');
=======
>>>>>>> 4aa32df1b73ab9b8f0b37ade4b6f9096072ebc8a
        $select->from(array('M'=>$this->_name));

        //join with the user
        $select->joinLeft(array(
            'S' =>'Stock'
        ), 'M.StockIdF = S.StockId');
        
        $select->where($this->_menuDate.' = ?', $date);
        $select->where('StockType = ?', $type);
<<<<<<< HEAD
        $select->where('StockNumber > 0');
=======
>>>>>>> 4aa32df1b73ab9b8f0b37ade4b6f9096072ebc8a
        $res = $this->fetchAll($select);
        
        
        if($res == null || $res->count() == 0)
            return null;
        else
        {
            $returned_arr = array();
            foreach($res as $row)
            {
                $returned_arr[] = array(
<<<<<<< HEAD
                    'StockId'       => $row->StockId,
=======
>>>>>>> 4aa32df1b73ab9b8f0b37ade4b6f9096072ebc8a
                    'StockName'     => $row->StockName,
                    'StockPrice'    => $row->StockPrice,
                    'MenuDate'      => $row->MenuDate,
                );
            }
            return $returned_arr;
        }
    }
    
<<<<<<< HEAD
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
=======
>>>>>>> 4aa32df1b73ab9b8f0b37ade4b6f9096072ebc8a
}


