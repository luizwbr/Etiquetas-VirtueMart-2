<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.form.formfield');
 
// The class name must always be the same as the filename (in camel case)
class JFormFieldAsset extends JFormField {
 
    //The field class must know its own type through the variable $type.
    protected $type = 'asset';

	/*
    public function getLabel() {
        // code that returns HTML that will be shown as the label
    } */

    public function getInput() {
      	$doc = JFactory::getDocument();
        $doc->addScript(JURI::root().$this->element['path'].'script.js');
        $doc->addStyleSheet(JURI::root().$this->element['path'].'style.css');        
        return null;
    }

}