<?php

class Application_Model_DbTable_UserGroup extends Zend_Db_Table_Abstract {
    protected $_name = 'UserGroup';  // table name in the database
    protected $_primary = 'GroupId'; //primary key
    protected $_userGroupName = 'GroupName'; // Name of the access
    protected $_groupSignature = 'GroupSignature';

    /**
     * fetch one group info
     * @param ficheId
     * @Return a row
     */
    public function fetchGroupById($id){

        $select = $this->select();
        $select->where($this->_primary[1].' = ?', $id);
        $row = $this->fetchRow($select);

        if($row == null)
            return null;
        else
            return $row;

    }
    
    /**
     * Insert a group in the database
     * @param $groupname, $usergroupid
     * @return ok if correct, else return the exception message
     */
    public function createGroup($groupname)
    {
        try{
            $id = $this->insert(array(
                $this->_groupName    => $groupname,
            ));
            return $id;

        }catch(Zend_Exception $e){
            return "nok";
            //return $e->getMessage();
        }
    }

    /**
     * Update the group
     * @param $groupId, $groupname
     * @return ok if correct, else return the exception message
     */
    public function updateGroup($groupId, $groupname){
        try{
             $this->update(array(
                $this->_groupName => $groupname,

            ), array(
                $this->_primary.'= ?' => $groupId ));
            return "ok";
        }catch (Zend_Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * Update the signature
     * @param idGroup, signature
     * @return 0 if correct, else return the exception message
     */
    public function updateSignature($idGroup, $signature){
        try{
            $update = $this->update(array(
                $this->_groupSignature => $signature
            ), $this->_primary." LIKE '".$idGroup."'" );
            return "0";
            
        }catch (Zend_Exception $e){
            return $e->getMessage();
        }
    }


    /**
     * Delete a group
     * @param groupId
     * @return ok if correct, else return exception message
     */
    public function deleteGroup($groupId)
    {

        //Check if a user is associated with thsi group, if yes, we cant delete this group
        $user = new Application_Model_DbTable_User();
        $user = $user->fetchUserByGroup($groupId);

        if($user == null)
        {

            try{
                $where = array(
                    $this->_primary.'= ?' => $groupId
                );
                $this->delete($where);
                return "ok";

            }catch (Zend_Exception $e){
                return $e->getMessage();
            }
        }
        else
            return "Un utilisateur est encore présent dans ce groupe";
    }

    public function getAllGroup() {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('C'=>$this->_name));

        $res = $this->fetchAll($select);

        if($res == null || $res->count() == 0)
            return null;
        else {
            $returned_arr = array();
            foreach($res as $row) {
                $returned_arr[] = array(
                    'id'  => $row->GroupId,
                    'name' => $row->GroupName
                );
            }
            return $returned_arr;
        }
    }

   

}
