<?php

/**
 * This class set a formular to edit a user
 * @author POUDEVIGNE Cyprien
 *
 */
class Application_Form_EditUserForm extends Application_Form_CustomForm {

    protected $idUserField; // input firstname
    protected $firstNameField; // input firstname
    protected $lastNameField; // input lastname
    protected $emailField; // input email
    protected $telephoneField; // input for the tel

    /**
     * Constructeur : les attributs à null
     */

    public function init() {
        $this->idUserField = null;
        $this->firstNameField = null;
        $this->lastNameField = null;
        $this->emailField = null;
        $this->telephoneField = null;
    }

    /**
     * Crée un formulaire avec tous les champs à vide
     * @param string $url url de l'action du form
     */
    public function createForm() {
        //creation des champs
        $this->setAttrib("id", "edituserform");
        $this->idUserField = $this->createHiddenInput('idUserEdit');
        $this->firstNameField = $this->createTextInput('firstnameEdit', 'Prénom :');
        $this->lastNameField = $this->createTextInput('lastnameEdit', 'Nom :');
        $this->emailField = $this->createTextInput('emailEdit', 'Email d\'utilisateur :');
        $this->telephoneField = $this->createTextInput('telEdit', 'Numéro téléphone :');


        // ajout des champs au form
        $this->addElements(array(
            $this->idUserField,
            $this->firstNameField,
            $this->lastNameField,
            $this->emailField,
            $this->telephoneField,
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

    /**
     * Hydrate le formulaire grâce au tableau data
     * Cette fonction est appelé optionellement, après le createForm
     *
     * @param array $data
     *            array contenant les informations à préremplir dans le formulaire,
     *            il s'agit d'une array clé/valeur : key = name de l'élément,
     *            value = valeur à mettre dans ce champ
     * @see Application_Form_WriteMail::createForm($url)
     */
    public function hydrateForm(array $data) {
        foreach ($data as $key => $value) {
            // retrouve l'élément du formulaire par son nom
            $elem = $this->getElement($key);
            // si l'élément est trouvé on set sa valeur
            if (!empty($elem))
                $elem->setValue(trim($value));
        }
    }

}
