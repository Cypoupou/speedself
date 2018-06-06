<?php

/**
 * This class set a formular to create a user
 * @author POUDEVIGNE Cyprien
 *
 */
class Application_Form_SignInForm extends Application_Form_CustomForm {

    protected $firstNameField; // input firstname
    protected $lastNameField; // input lastname
    protected $emailField; // input email
    protected $passwordField; // input password
    protected $passwordBisField; // input password bis
    protected $telephoneField; // input for the tel

    /**
    *** Constructeur : les attributs à null
    **/
    public function init() {
        $this->firstNameField = null;
        $this->lastNameField = null;
        $this->emailField = null;
        $this->telephoneField = null;
        $this->passwordField = null;
        $this->passwordBisField = null;
    }

    /**
    *** Crée un formulaire avec tous les champs à vide
    *** @param string $url url de l'action du form
    **/
    public function createForm() {
        //creation des champs
        $this->setAttrib("id", "signinForm");
        $this->firstNameField = $this->createTextInput('firstname', 'Prénom :');
        $this->lastNameField = $this->createTextInput('lastname', 'Nom :');
        $this->emailField = $this->createTextInput('email', 'Email :');
        $this->telephoneField = $this->createTextInput('tel', 'Numéro téléphone :');
        $this->passwordField = $this->createPasswordInput('password', 'Mot de passe :');
        $this->passwordBisField = $this->createPasswordInput('passwordbis', 'Vérification mot de passe :');

        // ajout des champs au form
        $this->addElements(array(
            $this->firstNameField,
            $this->lastNameField,
            $this->emailField,
            $this->telephoneField,
            $this->passwordField,
            $this->passwordBisField,
            
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

    /**
     * (non-PHPdoc)
     * Supprime l'autocomplete des champs
     *
     * @see Application_Form_CustomForm::createTextInput()
     */
    public function createTextInput($name, $label, $className = null) {
        $elem = parent::createTextInput($name, $label, $className);
        // empêche l'autocomplétion des champs
        $elem->setAttrib('autocomplete', 'off');
        return $elem;
    }

    /**
     * (non-PHPdoc)
     * Supprime l'autocomplete des champs
     *
     * @see Application_Form_CustomForm::createPasswordInput()
     */
    public function createPasswordInput($name, $label, $className = null) {
        $elem = parent::createPasswordInput($name, $label, $className);
        // empêche l'autocomplétion des champs
        $elem->setAttrib('autocomplete', 'off');
        return $elem;
    }

    /**
     * Add the options contained in a array to a select
     * @param string $name name of the select
     * @param array $options array of the options to put in, association table. The index must
     *          be 'id' (for the value) and 'name' (text)
     */
    public function addOptionsToSelect($elem, array $options) {

        //if the element is not a select we throw an exception
        if (!get_class($elem) || get_class($elem) != get_class(new Zend_Form_Element_Select('test'))) {
            throw new Exception("Wrong class received by the method");
        }else {
            // we add the options : name is the text shown in the select, id is the value
            foreach ($options as $opt) {
                $elem->addMultiOptions(array(
                    $opt['id'] => htmlspecialchars_decode($opt['name'])
                ));
            }
        }
    }

}
