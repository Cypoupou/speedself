<?php

/**
 * This class set a formular to edit a stock
 * @author POUDEVIGNE Cyprien
 *
 */
class Application_Form_EditStockForm extends Application_Form_CustomForm
{
    
    protected $nameField; // input name
    
    protected $numberField; // input number
    
    protected $priceField; // input price
    
    protected $typeField; // select type
    
    /**
     * Constructeur : les attributs à null
     */
    public function init()
    {   
        $this->nameField = null;
        $this->numberField = null;
        $this->priceField = null;
        $this->typeField = null;
    }
    
    
     /**
     * Crée un formulaire avec tous les champs à vide
     * @param string $url url de l'action du form
     */
    public function createForm()
    {
        //creation des champs
        $this->setAttrib("id", "editstockform");
        $this->nameField = $this->createTextInput('StockName', 'Nom :');
        $this->numberField = $this->createTextInput('StockNumber', 'Quantité :');
        $this->priceField = $this->createTextInput('StockPrice', 'Prix :');
        $this->typeField = $this->createSelect('StockType', 'Type :');

        $this->typeField->addMultiOptions ( array (
            'Entrée' => htmlspecialchars_decode('Entrée'),
            'Plat' => htmlspecialchars_decode('Plat'),
            'Dessert' => htmlspecialchars_decode('Dessert'),  
        ) );
        
        // ajout des champs au form
        $this->addElements(array(
            $this->nameField,
            $this->numberField,
            $this->priceField,
            $this->typeField,
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
        return $elem;
    }
    
    /**
     * Add the options contained in a array to a select
     * @param string $name name of the select
     * @param array $options array of the options to put in, association table. The index must
     *          be 'id' (for the value) and 'name' (text)
     */
    public function addOptionsToSelect($elem, array $options){
    
        //if the element is not a select we throw an exception
        if (!get_class($elem)
            || get_class($elem) != get_class(new Zend_Form_Element_Select('test')))
        {
            throw new Exception("Wrong class received by the method");
        }
        else
        {
            // we add the options : name is the text shown in the select, id is the value
            foreach($options as $opt)
            {
                $elem->addMultiOptions ( array (
                    $opt['id'] => htmlspecialchars_decode($opt['name'])
                ) );

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

}
