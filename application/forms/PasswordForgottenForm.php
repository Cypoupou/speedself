<?php

/**
 * This class set a formular for forgotten password
 * @author POUDEVIGNE Cyprien
 *
 */
class Application_Form_PasswordForgottenForm extends Application_Form_CustomForm
{

    protected $usermailField; // input mail
    protected $button; // button to submit

    /**
     * Constructeur : les attributs à null
     */
    public function init()
    {
        $this->usermailField = null;
    }
    
    /*
    **
    * Crée un formulaire avec tous les champs à vide
    * @param string $url url de l'action du form
     */
    public function createForm($url = null)
    {
        if (! empty($url))
            $this->setAction($url);
        //creation des champs
        $this->setAttrib("id", "passwordforgottenForm");
        $this->usermailField = $this->createTextInput('usermail', 'Votre e-mail: ');
    
        // ajout des champs au form
        $this->addElements(array(
            $this->usermailField,
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