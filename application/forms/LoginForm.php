<?php

/**
 * This class set a formular to log in the application
 * @author POUDEVIGNE Cyprien
 *
 */
class Application_Form_LoginForm extends Application_Form_CustomForm
{

    protected $usernameField; // input login

    protected $passwordField; // input password
    
    protected $remembermeField; // checkbox

    protected $button; // button to submit

    /**
     * Constructeur : les attributs à null
     */
    public function init()
    {
        $this->usernameField = null;
        $this->passwordField = null;
        $this->remembermeField = null;
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
        $this->setAttrib("id", "loginForm");
        $this->usernameField = $this->createTextInput('username', 'Nom d\'utilisateur :');
        $this->passwordField = $this->createPasswordInput('password', 'Mot de passe :');
        $this->remembermeField = $this->createCheckbox('rememberme', 'Rester connecté :');
    
        // ajout des champs au form
        $this->addElements(array(
            $this->usernameField,
            $this->passwordField,
            $this->remembermeField,
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