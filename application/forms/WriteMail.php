<?php

/**
 * Cette classe prépare un formulaire pour rédiger une réponse à une conversation
 * @author CHERRUAU Jessica
 *
 */
class Application_Form_WriteMail extends Application_Form_CustomForm
{

    protected $idField; // input caché avec l'id de la conversation
    
    protected $subjectField; // subject

    protected $toField; // input avec l'adresse du destinataire

    protected $ccField; // input avec les adresses des personnes en copie
    
    protected $cciField; // input avec les adresses des personnes en copie
    
    protected $attachmentField; // input type=file pour ajouter les pieces jointes

    protected $contentField; // textarea contenant le ckEditor (contenu du mail)

    protected $button; // button pour submit

    /**
     * Constructeur : les attributs à null
     */
    public function init()
    {
        $this->idField = null;
        $this->toField = null;
        $this->ccField = null;
        $this->cciField = null;
        $this->attachmentField = null;
        $this->subjectField = null;
        $this->contentField = null;
        $this->button = null;
    }

    /**
     * Crée un formulaire avec tous les champs à vide
     * @param string $url url de l'action du form
     */
    public function createForm($url = null)
    {
        if (! empty($url))
            $this->setAction($url);
        
        $this->setMethod('post');
        $this->setAttrib('id', 'formMail');
        
        //creation des champs
        $this->idField = $this->createHiddenInput('id');
        $this->toField = $this->createHiddenInput('to');
        //we want to have cc and bcc fields on the same line
        $this->ccField = $this->createHiddenInput('cc');
        $this->cciField = $this->createHiddenInput('cci');
        $this->attachmentField = $this->createFileInput('files', "Pièces jointes");
        $this->subjectField = $this->createTextInput('subject', 'Objet :');
        $this->contentField = $this->createTextarea("content", "", "ckeditor");
        
        // ajout des champs au form
        $this->addElements(array(
            $this->idField,
            $this->button,
            $this->toField,
            $this->ccField,
            $this->cciField,
            $this->attachmentField,
            $this->subjectField,
            $this->contentField
            
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
    public function createTextInput($name, $label, $className = null)
    {
        $elem = parent::createTextInput($name, $label, $className);
        // empêche l'autocomplétion des champs
        $elem->setAttrib('autocomplete', 'off');
        $elem->setAttrib('size', '120');
        return $elem;
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
    public function hydrateForm(array $data)
    {
        foreach ($data as $key => $value) {
            // retrouve l'élément du formulaire par son nom
            $elem = $this->getElement($key);
            // si l'élément est trouvé on set sa valeur
            if (! empty($elem))
                $elem->setValue(trim($value));
        }
    }
    
    public function createFileInput($name, $label, $className = null)
    {
        $elem = parent::createFileInput($name, $label, $className);
        $elem->setAttrib("multiple", null);
        return $elem;
    }
}