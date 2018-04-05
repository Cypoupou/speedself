<?php
/**
 * Created by PhpStorm.
 * User: DDJK5031
 * Date: 03/12/2015
 * Time: 15:31
 */

class Application_Model_DbTable_Menu extends Zend_Db_Table_Abstract {
    protected $_name = 'Menu';  // table name in the database
    protected $_primary = 'MenuId'; //primary key
    protected $_stockIdF = 'StockIdF'; //stock id Foreign key
    protected $_menuDate = 'MenuDate'; //date

    
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
        $res = $this->fetchAll($select);
        
        
        if($res == null || $res->count() == 0)
            return null;
        else
        {
            $returned_arr = array();
            foreach($res as $row)
            {
                $returned_arr[$row->MenuId] = array(
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
        $select->from(array('M'=>$this->_name));

        //join with the user
        $select->joinLeft(array(
            'S' =>'Stock'
        ), 'M.StockIdF = S.StockId');
        
        $select->where($this->_menuDate.' = ?', $date);
        $select->where('StockType = ?', $type);
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
                    'MenuDate'      => $row->MenuDate,
                );
            }
            return $returned_arr;
        }
    }
    
}


