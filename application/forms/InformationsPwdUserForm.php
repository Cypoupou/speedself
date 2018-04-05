<?php

/**
 * This class set a formular to modifiy the password of a user
 * @author POUDEVIGNE Cyprien
 *
 */
class Application_Form_InformationsPwdUserForm extends Application_Form_CustomForm
{

    protected $actualPwdField; // input with the actual password

    protected $newPwdField; // input with the new password

    protected $newPwdBisField; // input with the confirmation of the new password


    /**
     * Constructeur : les attributs à null
     */
    public function init()
    {
        $this->actualPwdField = null;
        $this->newPwdField = null;
        $this->newPwdBisField = null;
    }

    /**
     * Crée un formulaire avec tous les champs à vide
     * @param string $url url de l'action du form
     */
    public function createForm($url = null)
    {
        if (! empty($url))
            $this->setAction($url);
        //creation des champs
        $this->setAttrib("id", "modifyPwdForm");
        $this->actualPwdField = $this->createPasswordInput('actualPwd', 'Mot de passe actuel :');
        $this->newPwdField = $this->createPasswordInput('newPwd', 'Nouveau mot de passe :');
        $this->newPwdBisField = $this->createPasswordInput('newPwdBis', 'Confirmer le nouveau mot de passe:');
        
        // ajout des champs au form
        $this->addElements(array(
            $this->actualPwdField,
            $this->newPwdField,
            $this->newPwdBisField
        ));
        
        // decorators
        $this->setDecorators(array(
            'FormElements',
            array(
                array(
                    'data' => 'HtmlTag'
                ),
                array(
                    'tag' => 'table'
                )
            ),
            'Form'
        ));
    }



}