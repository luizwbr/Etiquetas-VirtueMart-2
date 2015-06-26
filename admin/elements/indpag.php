<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.form.formfield');
 
// The class name must always be the same as the filename (in camel case)
class JFormFieldIndpag extends JFormField {
 
    //The field class must know its own type through the variable $type.
    protected $type = 'indpag';

	/*
    public function getLabel() {
        // code that returns HTML that will be shown as the label
    } */

    public function getInput() {
        // code that returns HTML that will be shown as the form field
		// carrega configuração e linguagens
		if(!class_exists('VmConfig'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'config.php');
		$config = VmConfig::loadConfig();

		$db = JFactory::getDBO();
        $lang = 'pt_br';
        $query = "SELECT
                  	m.payment_name, 
                  	p.payment_element,
                  	p.virtuemart_paymentmethod_id as payment_id
                  FROM `#__virtuemart_paymentmethods` p
                  INNER JOIN `#__virtuemart_paymentmethods_".$lang."` m
                  ON m.virtuemart_paymentmethod_id = p.virtuemart_paymentmethod_id
                  WHERE published = 1";
		$db->setQuery($query);
		$lista_pagamento = $db->loadObjectList();

		$valores_pagamento = $this->value;           

		$html = "<br style='clear:both'/>";
		$html .= "<div style='background: #efefef; padding: 10px'>";
		$html .= "O indicador de pagamento aqui valerá para cada NF-e gerada no sistema.";
		$html .= "<br style='clear:both'/>";
		$html .= "<br style='clear:both'/>";
		foreach ($lista_pagamento as $value) {

			// lista os options
			$sel = "selected='selected'";

			$valor_selecionado = $valores_pagamento[$value->payment_id];

			$lista_option = '<select name="'.$this->name.'['.$value->payment_id.']">';
			$lista_option .= '<option value="0" '.($valor_selecionado==='0'?$sel:'').'>0 - Pagamento à Vista</option>';
			$lista_option .= '<option value="1" '.($valor_selecionado==='1'?$sel:'').'>1 - Pagamento à prazo</option>';
			$lista_option .= '<option value="2" '.($valor_selecionado==='2'?$sel:'').'>2 - outros</option>';
			$lista_option .= '</select>';

			$html .= "<div style='float:left; width: 180px; margin-bottom: 5px'><b>".$value->payment_name."</b> <br/>(".$value->payment_element."): </div><div style='float:left'>".$lista_option."</div><br style='clear:both'/>";

		}
		$html .= "</div><br style='clear:both'/>";
		return $html;
    }
}