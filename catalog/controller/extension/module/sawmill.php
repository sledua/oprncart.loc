<?php
/*
 *  location: admin/controller
 */

class ControllerExtensionModuleSawmill extends Controller {

	private $codename = 'sawmill';
	private $route = 'extension/module/sawmill';
	private $config_file = 'sawmill';
	private $extension = array();
	private $store_id = 0;
	private $error = array();

	public function __construct($registry) {
		parent::__construct($registry);

        $this->load->model('extension/d_opencart_patch/load');
        $this->load->model($this->route);
        $this->load->language($this->route);
	}

	public function index() {

        $this->document->addStyle('catalog/view/theme/default/stylesheet/sawmill/sawmill.css');
        $this->document->addScript('catalog/view/javascript/d_underscore/underscore-min.js');
        $this->document->addScript('catalog/view/javascript/d_vue/vue.min.js');
        $this->document->addScript('catalog/view/javascript/d_vue_i18n/vue-i18n.min.js');
        $this->document->addScript('catalog/view/javascript/d_vuex/vuex.min.js');
        $this->document->addScript('catalog/view/javascript/sawmill/lib/VueOptions.js');
        $this->document->addScript('catalog/view/javascript/sawmill/dist/sawmill.js');

		$data['text_button'] = $this->language->get('text_button');

        $data['vueTemplates'] = $this->{'model_extension_module_'.$this->codename}->getVueTemplates();


        $data['json'] = array();

        $data['json']['config'] = array();
        
        $data['json']['products'] = array();
        $data['json']['edge_products'] = array();

        $setting = $data['json']['setting'] = $this->getSetting();
        
        $this->load->model('catalog/product');
        $this->load->model('tool/image');

        if($this->cart->getProducts()){
            foreach($this->cart->getProducts() as $product){
                $product_type = $this->{'model_extension_module_'.$this->codename}->getProductType($product['product_id']);
                $product_info = $this->model_catalog_product->getProduct($product['product_id']);
                if($product_type && $product_type['type_id'] == $setting['material_type_id']) {
                   
                    if ($product['image']) {
                        $image = $this->model_tool_image->resize($product['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_height'));
                    } else {
                        $image = '';
                    }

                    if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                        $unit_price = $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'));
                        
                        $price = $this->currency->format($unit_price, $this->session->data['currency']);
                        $total = $this->currency->format($unit_price * $product['quantity'], $this->session->data['currency']);
                    } else {
                        $price = false;
                        $total = false;
                    }

                    $data['json']['products'][] = array(
                        'product_id' => $product_info['product_id'],
                        'name' => html_entity_decode($product_info['name'], ENT_QUOTES, 'UTF-8'),
                        'quantity' => $product['quantity'],
                        'price' => $price,
                        'total' => $total,
                        'image' => $image
                    );
                }
            }
        }

        $results =  $this->{'model_extension_module_'.$this->codename}->getProductsByType( $setting['edge_type_id']);

        if(!empty($results)) {
            foreach ($results as $product) {
                $product_info = $this->model_catalog_product->getProduct($product['product_id']);


				if ($product_info['image']) {
					$image = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_height'));
				} else {
					$image = '';
				}

                if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                    $price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                } else {
                    $price = false;
                }

                if ((float)$product_info['special']) {
                    $special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                } else {
                    $special = false;
                }

                if ($this->config->get('config_tax')) {
                    $tax = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
                } else {
                    $tax = false;
                }

                $data['json']['edge_products'][] = array(
                    'product_id' => $product_info['product_id'],
                    'model' => $product_info['model'],
                    'name' => html_entity_decode($product_info['name'], ENT_QUOTES, 'UTF-8'),
                    'tax' => $tax,
                    'special' => $special,
                    'price' => $price,
                    'image' => $image
                );
            }
        }

        $data['language_id'] = $this->config->get('config_language_id');

		$data['local'] = $this->prepareLocal();
		$data['options'] = $this->prepareOptions();

		return $this->model_extension_d_opencart_patch_load->view($this->route, $data);
	}

	public function prepareLocal(){
		$local = array();

		$local['common']['heading_title'] = $this->language->get('heading_title_main');
		$local['common']['text_edit'] = $this->language->get('text_edit');
		$local['common']['text_material_title'] = $this->language->get('text_material_title');
		$local['common']['text_product_framing'] = $this->language->get('text_product_framing');
        $local['common']['text_edge_recommended'] = $this->language->get('text_edge_recommended');

        $local['common']['column_image'] = $this->language->get('column_image');
        $local['common']['column_name'] = $this->language->get('column_name');
        $local['common']['column_model'] = $this->language->get('column_model');
        $local['common']['column_price'] = $this->language->get('column_price');

        $local['common']['text_step_detail'] = $this->language->get('text_step_detail');
        $local['common']['text_step_additional'] = $this->language->get('text_step_additional');
        $local['common']['text_step_calculate'] = $this->language->get('text_step_calculate');
        $local['common']['text_step_order'] = $this->language->get('text_step_order');
        
		$local['common']['button_cutting_begin'] = $this->language->get('button_cutting_begin');
		$local['common']['button_next'] = $this->language->get('button_next');

		return array($this->config->get('config_language_id') => $local);
	}

	public function prepareOptions(){
		$option = array();

		return $option;
	}

	public function getSetting(){
		$key = 'module_'.$this->codename.'_setting';

		if ($this->config_file) {
			$this->config->load($this->config_file);
		}

		$result = ($this->config->get($key)) ? $this->config->get($key) : array();

		if (!isset($this->request->post['config'])) {

			$this->load->model('setting/setting');
			if (isset($this->request->post[$key])) {
				$setting = $this->request->post;

			} elseif ($this->model_setting_setting->getSetting('module_'.$this->codename, $this->store_id)) {
				$setting = $this->model_setting_setting->getSetting('module_'.$this->codename, $this->store_id);

			}

			if (isset($setting[$key])) {
				foreach ($setting[$key] as $key => $value) {
					$result[$key] = $value;
				}
			}
		}
		return $result;
	}

}