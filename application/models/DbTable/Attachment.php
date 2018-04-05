<?php
/**
 * Created by PhpStorm.
 * User: DDJK5031
 * Date: 03/12/2015
 * Time: 17:30
 */

class Application_Model_DbTable_Attachment extends Zend_Db_Table_Abstract {
    protected $_name = 'attachment';  // table name in the database
    protected $_primary = 'AttachmentId'; //primary key
    protected $_ficheId = 'IdFicheF'; // id of the fiche's attachement



    /**
     * Saves on the database the paths of the attachments previously put in the mail
     * @param number $idMsg id of the message the attachment is linked to
     */
    public function saveAttachmentsToDatabase($idFiche,$userId, $path){

        try{
                $this->insert(array(
                    'AttachmentPath'    => $path,
                    'AttachmentDate'    => date('Y-m-d H:i:s', time()),
                    'IdFicheF'        => $idFiche,
                    'IdUserf'     => $userId
                ));
        }catch(Zend_Exception $e){
            return -1;
        }

    }


    /**
     * Get all the attachement for one fiche
     * @return NULL|array: return the result or null if there is no result
     */
    public function getAllAttachmentForOneFiche($idFiche)
    {
        $select = $this->select();

        $select->setIntegrityCheck(false);
        $select->from(array('A'=>$this->_name));

        //join with the User
        $select->joinLeft(array(
            'U' =>'User'
        ), 'A.IdUserF = U.UserId');

        $select->where($this->_ficheId.' = ?', $idFiche);

        $res = $this->fetchAll($select);

        if($res == null || $res->count() == 0)
            return null;
        else
        {
            $returned_arr = array();
            foreach($res as $row)
            {
                //mise forme de la date de creation
                if($row->AttachmentDate != NULL) {
                    $AttachmentDate = strtotime($row->AttachmentDate);
                    $dateFiche = date('H:i d-m-Y', $AttachmentDate);
                }else {
                    $dateFiche = '';
                }
                
                $returned_arr[] = array(
                    'id' => $row->AttachmentId,
                    'path'    => $row->AttachmentPath,
                    'date'  => $dateFiche,
                    'userName' => $row->UserFirstName." ".$row->UserLastName,
                    'idAttachement' => $row->AttachmentId
                );
            }
            return $returned_arr;
        }
    }

    /**
     * get attachment by id
     * @return NULL|array: return the result or null if there is no result
     */
    public function getAttachmentById($idAttachment){
        $select = $this->select();
        $select->where($this->_primary[1].' = ?', $idAttachment);
        return  $row = $this->fetchRow($select);
    }

    /**
     * delete an attachment
     * @return 1 if ok, 0 or exception if not
     */
    public function deleteAttachment($idAttachment)
    {
        try{
            $attachment = $this->getAttachmentById($idAttachment);
            if($attachment != null)
            {
                unlink($attachment['AttachmentPath']);


            $where = array(
                $this->_primary[1].'= ?' => $idAttachment
            );
            $this->delete($where);

            return "1";
            }
            else
                return "0";

        }catch (Zend_Exception $e){
            return $e->getMessage();
        }

    }
        /**
     * delete an attachment and the directory of the fiche
     */
    public function deleteAttachmentByFicheId($idFiche)
    {

            $attachment = $this->getAllAttachmentForOneFiche($idFiche);
            if($attachment != null)
            {
                foreach($attachment as $row)
                {
                        unlink($row['path']);
                        $where = array(
                            $this->_primary[1].'= ?' => $row['id']
                        );
                        $this->delete($where);   
                }
            }
            if (is_dir('../public/attachment/'.$idFiche)){
            rmdir('../public/attachment/'.$idFiche);
            }
    }
    
    public function getPathById($idAttachment){
        $idAttachment = intval($idAttachment);
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array("A" => $this->_name));
        $select->where("A.AttachmentId = ?", $idAttachment);               
        $res = $this->fetchRow($select);
        
        if(empty($res))
            return null;
        else
            return $res->AttachmentPath;
    }
    
    public function updateAttachmentFicheId($idOld, $idNew) {
        $data = array(
            'IdFicheF'   => $idNew,
        );
        $res = $this->update($data, "IdFicheF = ".$idOld);
        
        return $res;
    }
    
    public function updateAttachmentPath($attachmentId, $newPath){
        $data = array(
            'AttachmentPath'   => $newPath,
        );
        $res = $this->update($data, "AttachmentId = ".$attachmentId);
        
        return $res;
        
    }
    
}
