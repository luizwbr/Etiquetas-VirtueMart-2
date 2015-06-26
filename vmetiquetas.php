<?php
/**
 * $Id: vmetiquetas.php 1.0.0 2013-06-24 05:39:14 Luiz Weber $
 * @package     Joomla! 
 * @subpackage  vmetiquetas
 * @version     1.0.0
 * @description VM Template Override
 * @copyright     Copyright © 2013 - Weber TI All rights reserved.
 * @license       GNU General Public License v2.0
 * @author        Luiz Felipe Weber
 * @author mail virtuemartpro@gmail.com
 * @website       http://virtuemartpro.com.br
 * 
 *
 * The events triggered in Joomla!
 * -------------------------------
 * onAfterInitialise()
 * onAfterRoute()
 * onAfterDispatch()
 * onAfterRender()
 *
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * Example System Plugin
 *
 * @package     Joomla! 
 * @subpackage  Webservice VM Teste
 * @class       plgSystemWebservicevmteste
 * @since       1.5
 */
 
class plgSystemVmetiquetas extends JPlugin {
    /**
     * Constructor
     *
     * For php4 compatability we must not use the __constructor as a constructor for plugins
     * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
     * This causes problems with cross-referencing necessary for the observer design pattern.
     *
     * @access        protected
     * @param   object  $subject The object to observe
     */
    function plgSystemVmetiquetas( &$subject, $config ) {
       parent::__construct( $subject, $config );
    }    

    function onAfterRoute() {        
        jimport( 'joomla.application.component.view' );

        $app            = JFactory::getApplication();
        $doc            = JFactory::getDocument();
        $option         = JRequest::getVar('option');
        $view           = JRequest::getVar('view'); 
        $layout         = JRequest::getVar('layout');
        $task           = JRequest::getVar('task');
        
        if(!$app->isAdmin()) {

            if (!class_exists( 'ShopFunctions' )) require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'shopfunctions.php');
            if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');            
            if (!class_exists( 'VmModel' )) require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'vmmodel.php');            
            if (!class_exists('VmPdf')) require(JPATH_VM_SITE.DS.'helpers'.DS.'vmpdf.php');
             // index.php?option=com_virtuemart&view=invoice&layout=deliverynote&format=pdf&tmpl=component
            // &virtuemart_order_id=7&order_number=7&order_pass=p_7031e&create_invoice=1

            // ini_set('display_errors',true);
            // error_reporting(E_ALL);

            //if ($option == 'com_virtuemart' and $view == 'invoice' and $layout == 'deliverynote' and $create_invoice == '1') {
            if ($option == 'com_virtuemart' and $view == 'invoice' and $layout == 'deliverynote' ) {

                $virtuemart_order_id    = JRequest::getVar('virtuemart_order_id');
                $order_number           = JRequest::getVar('order_number');
                $order_pass             = JRequest::getVar('order_pass');

                // recupera a configuração do vm do template
                $config = VmConfig::loadConfig();                
                $doc->setBuffer('','component');

                $url = 'http://www2.correios.com.br/enderecador/encomendas/act/gerarEtiqueta.cfm?etq=2';

                $orders = VmModel::getModel('orders');
                $order = $orders->getOrder($virtuemart_order_id);
                $orderDetails = $order['details'];

                $vendor = VmModel::getModel('vendor');
                $userId = $vendor->getUserIdByVendorId($orderDetails['BT']->virtuemart_vendor_id);

                $usermodel = VmModel::getModel('user');
                $virtuemart_userinfo_id = $usermodel->getBTuserinfo_id($userId);
                $vendorAddress = $usermodel->getUserAddressList($userId, 'BT', $virtuemart_userinfo_id);   

                $cep_vendedor   = str_replace(array('-',' ','.'),array('','',''),$vendorAddress[0]->zip);
                $cep_cliente    = str_replace(array('-',' ','.'),array('','',''),$orderDetails['BT']->zip);

                $estado_vendedor = ShopFunctions::getStateByID($vendorAddress[0]->virtuemart_state_id, "state_2_code");
                $estado_cliente  = ShopFunctions::getStateByID($orderDetails['BT']->virtuemart_state_id, "state_2_code");

				$campo_numero  		= $this->params->get('campo_numero','');
				$campo_bairro  		= $this->params->get('campo_bairro','');
				$campo_complemento  = $this->params->get('campo_complemento','');



                $data = array(
                    'controle' => '',
                    'to' => '2',
                    'tipoImpressao' => '1',
                    'tipo_cep_1' => '2',
                    'cep_1' => utf8_decode($cep_vendedor),
                    'cep_teste_1' => utf8_decode($cep_vendedor),
                    'nome_1' => utf8_decode($vendorAddress[0]->first_name.' '.$vendorAddress[0]->last_name),
                    'empresa_1' => utf8_decode($vendorAddress[0]->company),
                    'endereco_1' => utf8_decode($vendorAddress[0]->address_1),
                    'numero_1' => (isset($vendorAddress[0]->$campo_numero)?utf8_decode($vendorAddress[0]->$campo_numero):''),
                    'complemento_1' => (isset($vendorAddress[0]->$campo_complemento)?utf8_decode($vendorAddress[0]->$campo_complemento):''),
                    'bairro_1' => (isset($vendorAddress[0]->$campo_bairro)?utf8_decode($vendorAddress[0]->$campo_bairro):''),
                    'cidade_1' => utf8_decode($vendorAddress[0]->city),
                    'uf_1' => utf8_decode($estado_vendedor),
                    'selUf_1' => utf8_decode($estado_vendedor),
                    'telefone_1' => utf8_decode($vendorAddress[0]->phone_1),
                    'desTipo_cep_1' => '2',
                    'desCep_teste_1' => utf8_decode($cep_cliente),
                    'desCep_1' => utf8_decode($cep_cliente),
                    'desNome_1' => utf8_decode($orderDetails['BT']->first_name.' '.$orderDetails['BT']->last_name),
                    'desEmpresa_1' => utf8_decode($orderDetails['BT']->company),
                    'desEndereco_1' => utf8_decode($orderDetails['BT']->address_1),
                    'desNumero_1' => (isset($orderDetails['BT']->$campo_numero)?utf8_decode($orderDetails['BT']->$campo_numero):''),
                    'desComplemento_1' => (isset($orderDetails['BT']->$campo_complemento)?utf8_decode($orderDetails['BT']->$campo_complemento):''),
                    'desBairro_1' => (isset($orderDetails['BT']->$campo_bairro)?utf8_decode($orderDetails['BT']->$campo_bairro):''),
                    'desCidade_1' => utf8_decode($orderDetails['BT']->city),
                    'desUf_1' => utf8_decode($estado_cliente),
                    'selDesUf_1' => utf8_decode($estado_cliente),
                    'desTelefone_1' => utf8_decode($orderDetails['BT']->phone_1),
                    'desDC_1' => 'Pedido N. '.$orderDetails['BT']->order_number,
                    'desCep_2' => '',
                    'tipo_cep_2' => '',
                    'cep_teste_2' => '',
                    'nome_2' => '',
                    'empresa_2' => '',
                    'endereco_2' => '',
                    'numero_2' => '',
                    'complemento_2' => '',
                    'bairro_2' => '',
                    'cidade_2' => '',
                    'uf_2' => '',
                    'selUf_2' => '',
                    'telefone_2' => '',
                    'desTipo_cep_2' => '',
                    'desCep_teste_2' => '',
                    'cep_2' => '',
                    'desNome_2' => '',
                    'desEmpresa_2' => '',
                    'desEndereco_2' => '',
                    'desNumero_2' => '',
                    'desComplemento_2' => '',
                    'desBairro_2' => '',
                    'desCidade_2' => '',
                    'desUf_2' => '',
                    'selDesUf_2' => '',
                    'desTelefone_2' => '',
                    'desDC_2' => '',
                );

                echo "<form action='".$url."' method='post' name='form_etiqueta'>";

                foreach ($data as $key => $value) {
                    echo '<input type="hidden" name="'.$key.'" value="'.$value.'" />';
                }
                echo "</form>
                <script language='javascript'>document.form_etiqueta.submit();</script>
                ";
                /*
                if (function_exists('curl_exec')) { 
                    $ch         = curl_init();
                    $timeout    = 0; // set to zero for no timeout
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                    $conteudo_etiqueta   = curl_exec($ch);       
                } else {
                    $conteudo_etiqueta = file_get_contents($url);        
                }

                $conteudo_etiqueta = str_replace(
                    array(
                        'src="',
                        ' onLoad="window.focus(); abrirAjuda();"',                        
                    ),
                    array(
                        'src="http://www2.correios.com.br/enderecador/encomendas/act/',
                        ''                        
                    ),
                    $conteudo_etiqueta
                );

                $conteudo_etiqueta = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $conteudo_etiqueta);
                $conteudo_etiqueta = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $conteudo_etiqueta);
                ob_start();
                ob_end_clean();                
                echo ($conteudo_etiqueta);
                */
                JFactory::getApplication()->close();

            }        
        }
    }
}

?>