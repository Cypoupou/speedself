<?php

class Application_Model_DbTable_Access extends Zend_Db_Table_Abstract {
    protected $_name = 'Access';  // table name in the database
    protected $_primary = 'AccessId'; //primary key
    protected $_accessName = 'AccessName'; // Name of the access
    
    /**
     * Get all the access of several messages
     * @return NULL|array: return the result or null if there is no result
     */
    public function getAllAccess()
    {
        $res = $this->fetchAll();
        
    if($res == null || $res->count() == 0)
            return null;
        else
        {
            $returned_arr = array();
            foreach($res as $row)
            {
                $returned_arr[] = array(
                    'id'    => $row->AccessId,
                    'name'  => $row->AccessName
                );
            }
            return $returned_arr;
        }
    }
}

