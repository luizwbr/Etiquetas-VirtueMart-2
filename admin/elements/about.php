<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.form.formfield');
 
// The class name must always be the same as the filename (in camel case)
class JFormFieldAbout extends JFormField {
 
    //The field class must know its own type through the variable $type.
    protected $type = 'about';

    public function getInput() {
    	$doc = JFactory::getDocument();
		$html = '<div style="float:left">
				<img src="'.JURI::root().$this->element['path'].DS.'logo_etiquetas.jpg" border="0"/><br />
				<h1> Plugin Etiquetas VirtueMart 2 - Weber TI</h1>
				<div>Solicitações, atualizações e notícias sobre o projeto: <a href="http://loja.weber.eti.br">Loja Weber</a> </div>
				<div>Suporte: <a href="mailto:weber@weber.eti.br">weber@weber.eti.br</a> </div>
		</div>';
		return $html;        
    }
}