<?php

class Application_Model_DbTable_User extends Zend_Db_Table_Abstract {
    protected $_name = 'user';  // table name in the database
    protected $_primary = 'UserId'; //primary key
    protected $_password = 'UserPassword'; //Password user
    protected $_lastName = 'UserLastName'; // last name of the user
    protected $_firstName = 'UserFirstName'; // first name of the user
    protected $_email = 'UserEmail'; // email of the user
    protected $_tel = 'UserTel'; // signature of the user
    protected $_idaccess = 'IdAccessF'; // access of the user
    protected $_idgroup = 'UserGroupIdF'; // group of the user
    protected $_salt = 'UserSalt'; // Salt of the user
    
    
    public function init(){
        
    }
    
    /**
     * fetch one user
     * @param IdUser
     * @Return a row
     */
    public function fetchOne($idUser){
    

        $select = $this->select();
        $select->where($this->_primary[1].' = ?', $idUser);
        $row = $this->fetchRow($select);
        return $row;
    
    }
    
    /**
     * fetch one user
     * @param EmailUser
     * @Return a row
     */
    public function fetchOneWithMail($emailUser){
    
    
        $select = $this->select();
        $select->where($this->_email.' = ?', $emailUser);
        $row = $this->fetchRow($select);
        return $row;
    
    }
    
    /**
     * Check if the User write the right password when he tries to change it
     * @param IdUser, PasswordUser, SaltUser
     * @return boolean
     */
    public function getCheckPwd($idUser, $pwd, $saltUser){
        
        $pwd = md5($pwd.$saltUser);
        // get the row set
        $select = $this->select();
        $select->where($this->_password.' = ?', $pwd);
        $select->where($this->_primary[1].' = ?', $idUser);
        $row = $this->fetchRow($select);
        if ($row == null)
            return false;
        else
            return true;
        
    }
    /**
     * Update the user
     * @param idUser, firstname, lastname, email, signature, accees
     * @return ok if correct, else return the exception message
     */
    public function updateUser($idUser, $firstname, $lastname, $email, $tel, $group){
        try{
            $update = $this->update(array(
                $this->_lastName    => $lastname,
                $this->_firstName   => $firstname,
                $this->_email       => $email,
                $this->_idgroup     => $group ,
                $this->_tel         => $tel,
            ), array(
                $this->_primary.'= ?' => $idUser ));
            return "ok";
        }catch (Zend_Exception $e){
            return $e->getMessage();
        }
    }
    
    /**
     * Update the password
     * @param idUser, password, saltUser
     * @return 0 if correct, else return the exception message
     */
    public function updatePwd($idUser, $pwd, $saltUser){
        
        $pass = md5($pwd.$saltUser);
        
        try{
            $update = $this->update(array(
                $this->_password => $pass
            ), $this->_primary[1]." LIKE '".$idUser."'" );
            return "0";
            
        }catch (Zend_Exception $e){
            return $e->getMessage();
        }
        
    }
    

    /**
     * Delete a user
     * @param idUser
     * @return ok if correct, else return exception message
     */
    public function deleteuser($idUser)
    {
        
        try{
            $where = array(
                $this->_primary.'= ?' => $idUser
            );
            $this->delete($where);  
            return "ok";
            
        }catch (Zend_Exception $e){
            return $e->getMessage();
        }
    }
    
    /**
     * Insert a user in the database
     * @param $username, $firstname, $lastname, $email, $password, $signature, $access
     * @return ok if correct, else return the exception message
     */
    public function createUser($iduser, $firstname, $lastname, $email, $password, $tel, $group, $access, $salt)
    {
        try{
            $insert = $this->insert(array(
                $this->_primary     => $iduser ,
                $this->_password    => $password ,
                $this->_firstName   => $firstname ,
                $this->_lastName    => $lastname ,
                $this->_email       => $email ,
                $this->_idgroup     => $group ,
                $this->_tel         => $tel,
                $this->_idaccess    => $access ,
                $this->_salt        => $salt,
            ));
            return "ok";
    
        }catch(Zend_Exception $e){
            return $e->getMessage();
        }
     }
     
     /**
     * Fetch the ids and the names of all the users
     * @return NULL|array null if there is no user, else return an array, each row contains an array
     *          key ('id' and 'name') /value 
     */
    public function fetchNames()
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
                    'id'    => $row->UserId,
                    'lastName'  => $row->UserLastName,
                    'firstName' => $row->UserFirstName
                );
            }
            return $returned_arr;
        }
    }

    /**
     * Fetch the ids and the names of all the users of a precise group
     * @return NULL|array null if there is no user, else return an array, each row contains an array
     *          key ('id' and 'name') /value
     */
    public function fetchUserByGroup($idgroup)
    {
        $select = $this->select();
        $select->where($this->_idgroup.' = ?', $idgroup);
        $res = $this->fetchAll($select);

        if($res == null || $res->count() == 0)
            return null;
        else
        {
            $returned_arr = array();
            foreach($res as $row)
            {
                $returned_arr[] = array(
                    'id'    => $row->UserId,
                    'name'  => $row->UserFirstName.' '.$row->UserLastName,
                    'lastName'  => $row->UserLastName,
                    'firstName' => $row->UserFirstName
                );
            }
            return $returned_arr;
        }
    }
    
    public function fetchUserByGroupName($groupName)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('C'=>$this->_name));

        //join with the group
        $select->joinLeft(array(
            'Gr' =>'Usergroup'
        ), 'C.UserGroupIdF = Gr.GroupId');
        $select->where('Gr.GroupName = ?', $groupName);
        $res = $this->fetchAll($select);

        if($res == null || $res->count() == 0)
            return null;
        else
        {
            $returned_arr = array();
            foreach($res as $row)
            {
                $returned_arr[] = array(
                    'id'    => $row->UserId,
                    'name'  => $row->UserFirstName.' '.$row->UserLastName,
                    'lastName'  => $row->UserLastName,
                    'firstName' => $row->UserFirstName
                );
            }
            return $returned_arr;
        }
    }
    
    public function getAllUser() {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('C'=>$this->_name));
        //join with the group
        $select->joinLeft(array(
            'Gr' =>'Usergroup'
        ), 'C.UserGroupIdF = Gr.GroupId');

        $res = $this->fetchAll($select);

        if($res == null || $res->count() == 0)
            return null;
        else {
            $returned_arr = array();
            foreach($res as $row) {
                $returned_arr[] = array(
                    'id'  => $row->UserId,
                    'name' => $row->UserFirstName.' '.$row->UserLastName,
                    'lastName'  => $row->UserLastName,
                    'firstName' => $row->UserFirstName,
                    'groupName' => $row->GroupName
                );
            }
            return $returned_arr;
        }
    }
    
    /*
    SELECT userGroupIdf
    FROM User
    WHERE UserId =  $idUser (example:'DPKP6259')
    */
    public function getUserGroup($idUser){

        $select = $this->select();
        $select->from(array('U' => $this->_name), array('UserGroupIdf'));
        $select->where('U.UserId = ?', $idUser);

        $row = $this->fetchRow($select);

        if($row == null)
            return null;
        else
            return $row['UserGroupIdf'];
    }

}

