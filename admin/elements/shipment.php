<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.form.formfield');
 
// The class name must always be the same as the filename (in camel case)
class JFormFieldShipment extends JFormField {
 
    //The field class must know its own type through the variable $type.
    protected $type = 'shipment';

    public function getInput() {
        // code that returns HTML that will be shown as the form field

    	if(!class_exists('VmConfig'))require(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
		$config = VmConfig::loadConfig();

		$db = JFactory::getDBO();
        $lang = VMLANG;        
        $query = "SELECT
                  	m.shipment_name, 
                  	s.shipment_element, 
                  	s.virtuemart_shipmentmethod_id as shipment_id
                  FROM `#__virtuemart_shipmentmethods` s
                  INNER JOIN `#__virtuemart_shipmentmethods_".$lang."` m
                  ON m.virtuemart_shipmentmethod_id = s.virtuemart_shipmentmethod_id
                  WHERE published = 1";

		$db->setQuery($query);
		$lista_entrega = $db->loadObjectList();

		$valores_entrega = $this->value;           

		$html = "<br style='clear:both'/>";
		$html .= "<div style='background: #efefef; padding: 10px'>";
		//$html .= "Digite a URL para enviar no rastreador dos Correios.";
		$html .= "Digite aqui a url para cada método de frete. ";
		$html .= "<br style='clear:both'/>";
		$html .= "<br style='clear:both'/>";

		foreach ($lista_entrega as $value) {
			if (isset($valores_entrega[$value->shipment_id])) {
				$nome_selecionado = $valores_entrega[$value->shipment_id]['url'];
			} else {
				$nome_selecionado = '';
			}

			$html .= "<div style='float:left; width: 180px; margin-bottom: 5px'><b>".$value->shipment_name."</b> <br/>(".$value->shipment_element."): </div>".
			"<span style='float:left; margin-top:5px; margin-right: 5px'>URL: </span><input type='text' name='".$this->name."[".$value->shipment_id."][url]' value='".$nome_selecionado."' size='50'/>".
			"<br style='clear:both'/>";

		}
		$html .= "</div><br style='clear:both'/>";
		return $html;
    }
}