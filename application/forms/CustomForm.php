<?php

/**
 * Cette classe contient un set de méthodes 
 * @author NXSR1654
 *
 */
abstract class Application_Form_CustomForm extends Zend_Form
{

    /**
     * Create an input type="text" with decorators
     *
     * @param string $name
     *            name of the input
     * @param string $label
     *            label of the input
     * @param string $className
     *            class of the input
     * @return Zend_Form_Element_Text the created element
     */
    public function createTextInput($name, $label, $className = null)
    {
        $elem = new Zend_Form_Element_Text($name);
        $elem->setLabel($label);
        // Set class attributes
        if ($className != null) {
            $elem->setAttrib('class', $className);
            $elem->setDecorators(array(
                'ViewHelper',
                'Description',
                'Errors',
                array(
                    array(
                        'data' => 'HtmlTag'
                    ),
                    array(
                        'tag' => 'td'
                    )
                ),
                array(
                    'Label',
                    array(
                        'tag' => 'td',
                        "class" => $className
                    )
                ),
                array(
                    array(
                        'row' => 'HtmlTag'
                    ),
                    array(
                        'tag' => 'tr'
                    )
                )
            ));
        } else {
            $elem->setDecorators(array(
                'ViewHelper',
                'Description',
                'Errors',
                array(
                    array(
                        'data' => 'HtmlTag'
                    ),
                    array(
                        'tag' => 'td'
                    )
                ),
                array(
                    'Label',
                    array(
                        'tag' => 'td'
                    )
                ),
                array(
                    array(
                        'row' => 'HtmlTag'
                    ),
                    array(
                        'tag' => 'tr'
                    )
                )
            ));
        }
        return $elem;
    }
    
    /**
     * Create an input type="file" with decorators
     *
     * @param string $name
     *            name of the input
     * @param string $label
     *            label of the input
     * @param string $className
     *            class of the input
     * @return Zend_Form_Element_Text the created element
     */
    public function createFileInput($name, $label, $className = null)
    {
        $elem = new Zend_Form_Element_File($name); 
        $elem->setIsArray(true);    // the field use the array notation ex: name="...[]"
        $elem->setLabel($label);
        // Set class attributes
        if(!empty($className))
            $elem->setAttrib('class', $className);
        // allows multiple files
        $elem->setAttrib("multiple", "multiple");
        $elem->setDecorators(array(
            'File',
            array(
                array(
                    'data' => 'HtmlTag'
                ),
                array(
                    'tag' => 'td'
                )
            ),
            array(
                'Label',
                array(
                    'tag' => 'td'
                )
            ),
            array(
                array(
                    'row' => 'HtmlTag'
                ),
                array(
                    'tag' => 'tr'
                )
            )
        ));
        return $elem;
    }

    /**
     * Create an input type="hidden" with decorators
     *
     * @param string $name
     *            name of the input
     * @return Zend_Form_Element_Text the created element
     */
    public function createHiddenInput($name)
    {
        $elem = new Zend_Form_Element_Hidden($name);

        $elem->setDecorators(array(
            'ViewHelper',
            'Description',
            'Errors',
            array(
                array(
                    'data' => 'HtmlTag'
                ),
                array(
                    'tag' => 'td'
                )
            ),
            array(
                array(
                    'row' => 'HtmlTag'
                ),
                array(
                    'tag' => 'tr',
                    'class' => 'hidden'
                )
            )
        ));
        
        return $elem;
    }

    /**
     * Create a select element with $name and $label with decorators
     *
     * @param string $name
     *            name of the element
     * @param string $label
     *            associated label
     * @param string $className
     *            name of the class of the object
     * @param boolean $noneOption
     *            true if there is
     * @return Zend_Form_Element_Select a select element
     */
    public function createSelect($name, $label, $className = null, $noneOption = false)
    {
        $elem = new Zend_Form_Element_Select($name);
        $elem->setLabel($label);
        
        // if the option "-" must appear
        if ($noneOption) {
            $elem->addMultiOptions(array(
                "-1" => "-"
            ));
        }
        
        // Set CSS attributes
        if ($className != null) {
            $elem->setAttrib('class', $className);
            $elem->setDecorators(array(
                'ViewHelper',
                'Description',
                'Errors',
                array(
                    array(
                        'data' => 'HtmlTag'
                    ),
                    array(
                        'tag' => 'td'
                    )
                ),
                array(
                    'Label',
                    array(
                        'tag' => 'td',
                        "class" => $className
                    )
                ),
                array(
                    array(
                        'row' => 'HtmlTag'
                    ),
                    array(
                        'tag' => 'tr'
                    )
                )
            ));
        } else {
            $elem->setDecorators(array(
                'ViewHelper',
                'Description',
                'Errors',
                array(
                    array(
                        'data' => 'HtmlTag'
                    ),
                    array(
                        'tag' => 'td'
                    )
                ),
                array(
                    'Label',
                    array(
                        'tag' => 'td'
                    )
                ),
                array(
                    array(
                        'row' => 'HtmlTag'
                    ),
                    array(
                        'tag' => 'tr'
                    )
                )
            ));
        }
        return $elem;
    }

    /**
     * Create a text area element
     *
     * @param unknown $name
     *            name of the element
     * @param unknown $label
     *            label of the element
     * @param string $className
     *            class of the element
     * @return Zend_Form_Element_Textarea the created element
     */
    public function createTextarea($name, $label, $className = null)
    {
        $elem = new Zend_Form_Element_Textarea($name);
        $elem->setLabel($label);
        
        // Set CSS attributes
        if ($className != null) {
            $elem->setAttrib('class', $className);
            $elem->setDecorators(array(
                'ViewHelper',
                'Description',
                'Errors',
                array(
                    array(
                        'data' => 'HtmlTag'
                    ),
                    array(
                        'tag' => 'td'
                    )
                ),
                array(
                    'Label',
                    array(
                        'tag' => 'td',
                        "class" => $className
                    )
                ),
                array(
                    array(
                        'row' => 'HtmlTag'
                    ),
                    array(
                        'tag' => 'tr'
                    )
                )
            ));
        } else {
            $elem->setDecorators(array(
                'ViewHelper',
                'Description',
                'Errors',
                array(
                    array(
                        'data' => 'HtmlTag'
                    ),
                    array(
                        'tag' => 'td'
                    )
                ),
                array(
                    'Label',
                    array(
                        'tag' => 'td'
                    )
                ),
                array(
                    array(
                        'row' => 'HtmlTag'
                    ),
                    array(
                        'tag' => 'tr'
                    )
                )
            ));
        }
        return $elem;
    }


    /**
     * Create a submit button
     *
     * @param string $name
     *            name/value of the button
     * @param string $id
     *            id of the element
     * @return Zend_Form_Element_Button the created element
     */
    public function createInputSubmit($name, $id = null, $className = null)
    {
        $elem = new Zend_Form_Element_Submit($name);
        if (! empty($id))
            $elem->setAttrib("id", $id);
        if(!empty($className))
            $elem->setAttrib("class", $elem->getAttrib("class"));
        $elem->setAttrib("tabIndex", "-1");
        $elem->setDecorators(array(
            'ViewHelper',
            'Description',
            'Errors',
            array(
                array(
                    'data' => 'HtmlTag'
                ),
                array(
                    'tag' => 'td'
                )
            ),
            array(
                array(
                    'row' => 'HtmlTag'
                ),
                array(
                    'tag' => 'tr'
                )
            )
        ));
        return $elem;
    }
    /**
     * Create a  button
     *
     * @param string $name
     *            name/value of the button
     * @param string $id
     *            id of the element
     * @return Zend_Form_Element_Button the created element
     */
    public function createInputButton($name, $id = null, $className = null)
    {
        $elem = new Zend_Form_Element_Button($name);
        if (! empty($id))
            $elem->setAttrib("id", $id);
        if(!empty($className))
            $elem->setAttrib("class", $className);
        $elem->setAttrib("tabIndex", "-1");
        $elem->setDecorators(array(
            'ViewHelper',
            'Description',
            'Errors',
            array(
                array(
                    'data' => 'HtmlTag'
                ),
                array(
                    'tag' => 'td'
                )
            ),
            array(
                array(
                    'row' => 'HtmlTag'
                ),
                array(
                    'tag' => 'tr'
                )
            )
        ));
        return $elem;
    }

    public function createCheckbox($name, $label, $className = null, $checked = false)
    {
        $elem = new Zend_Form_Element_Checkbox($name);
        
        $elem->setChecked($checked);
        $elem->setLabel($label);

        if (empty($className)) {
            $elem->setDecorators(array(
                'ViewHelper',
                'Description',
                'Errors',
                array(
                    array(
                        'data' => 'HtmlTag'
                    ),
                    array(
                        'tag' => 'td'
                    )
                ),
                array(
                    'Label',
                    array(
                        'tag' => 'td',

                    )
                ),
                array(
                    array(
                        'row' => 'HtmlTag'
                    ),
                    array(
                        'tag' => 'tr'
                    )
                )
            ));
        } else {
            $elem->setAttrib("class", $className);
            $elem->setDecorators(array(
                'ViewHelper',
                'Description',
                'Errors',
                array(
                    'Label',
                    array(
                        'tag' => 'td'
                    )
                ),
                array(
                    array(
                        'data' => 'HtmlTag'
                    ),
                    array(
                        'tag' => 'td'
                    )
                ),

                array(
                    array(
                        'row' => 'HtmlTag'
                    ),
                    array(
                        'tag' => 'tr'
                    )
                )
            ));
        }
        return $elem;
    }
    
    /**
     * Create an input type="password" with decorators
     *
     * @param string $name
     *            name of the input
     * @param string $label
     *            label of the input
     * @param string $className
     *            class of the input
     * @return Zend_Form_Element_Password
     */
    public function createPasswordInput($name, $label, $className = null)
    {
        $elem = new Zend_Form_Element_Password($name);
        $elem->setLabel($label);
        // Set class attributes
        if ($className != null) {
            $elem->setAttrib('class', $className);
            $elem->setDecorators(array(
                'ViewHelper',
                'Description',
                'Errors',
                array(
                    array(
                        'data' => 'HtmlTag'
                    ),
                    array(
                        'tag' => 'td'
                    )
                ),
                array(
                    'Label',
                    array(
                        'tag' => 'td',
                        "class" => $className
                    )
                ),
                array(
                    array(
                        'row' => 'HtmlTag'
                    ),
                    array(
                        'tag' => 'tr'
                    )
                )
            ));
        } else {
            $elem->setDecorators(array(
                'ViewHelper',
                'Description',
                'Errors',
                array(
                    array(
                        'data' => 'HtmlTag'
                    ),
                    array(
                        'tag' => 'td'
                    )
                ),
                array(
                    'Label',
                    array(
                        'tag' => 'td'
                    )
                ),
                array(
                    array(
                        'row' => 'HtmlTag'
                    ),
                    array(
                        'tag' => 'tr'
                    )
                )
            ));
        }
        return $elem;
    }
}