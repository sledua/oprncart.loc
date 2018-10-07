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
        $this->load->model($this->route);
        $this->load->language($this->route);
        $this->d_shopunity = (file_exists(DIR_SYSTEM.'library/d_shopunity/extension/d_shopunity.json'));
        $this->d_twig_manager = (file_exists(DIR_SYSTEM.'library/d_shopunity/extension/d_twig_manager.json'));
        $this->d_event_manager = (file_exists(DIR_SYSTEM.'library/d_shopunity/extension/d_event_manager.json'));
        $this->extension = json_decode(file_get_contents(DIR_SYSTEM.'library/d_shopunity/extension/'.$this->codename.'.json'), true);
        $this->store_id = (isset($this->request->get['store_id'])) ? $this->request->get['store_id'] : 0;

    }

    public function index(){

        // if($this->d_shopunity){
        //     $this->load->model('extension/d_shopunity/mbooth');
        //     $this->model_extension_d_shopunity_mbooth->validateDependencies($this->codename);
        // }

        if($this->d_twig_manager){
            $this->load->model('extension/module/d_twig_manager');
            $this->model_extension_module_d_twig_manager->installCompatibility();
        }

        if($this->d_event_manager){
            $this->load->model('extension/module/d_event_manager');
            $this->model_extension_module_d_event_manager->installCompatibility();
        }

        $this->load->controller('extension/'.$this->codename.'/setting');
    }

    public function install() {
        if($this->d_shopunity){
            $this->load->model('extension/d_shopunity/mbooth');
            $this->model_extension_d_shopunity_mbooth->installDependencies($this->codename);
        }

        $this->load->model('extension/module/sawmill');
        $this->model_extension_module_sawmill->installDatabase();

    }

    public function uninstall() {
        if($this->d_event_manager){
            $this->load->model('extension/module/d_event_manager');
            $this->model_extension_module_d_event_manager->deleteEvent($this->codename);
        }

        $this->load->model('extension/module/sawmill');
        $this->model_extension_module_sawmill->uninstallDatabase(); 
    }

    public function model_catalog_product_addProduct_after(&$route, &$data, &$output) {
        $this->{'model_extension_module_'.$this->codename}->editProductType($data['sawmill_type_id'], $output);
    }

    public function model_catalog_product_editProduct_after(&$route, &$data, &$output) {
        $this->{'model_extension_module_'.$this->codename}->editProductType($data[1]['sawmill_type_id'], $data[0]);
    }
    public function view_catalog_product_after(&$route, &$data, &$output) {
        $html_dom = new d_simple_html_dom();
		$html_dom->load($output, $lowercase = true, $stripRN = false, $defaultBRText = DEFAULT_BR_TEXT);

        $this->load->model('extension/d_opencart_patch/url');

        $data['autocomplete_link'] = $this->model_extension_d_opencart_patch_url->ajax('extension/'.$this->codename.'/type/autocomplete');

        $data['entry_sawmill_type'] = $this->language->get('entry_sawmill_type');

        if(!empty($this->request->get['product_id'])) {
            $product_type= $this->{'model_extension_module_'.$this->codename}->getProductType($this->request->get['product_id']);
            if($product_type) {
                $data['sawmill_type'] = $product_type['name'];
                $data['sawmill_type_id'] = $product_type['type_id'];
            }
        }

		if($html_dom->find('#tab-links')) {
			$html_dom->find('#tab-links', 0)->innertext  = $this->load->view('extension/'.$this->codename.'/product_edit', $data).$html_dom->find('#tab-links', 0)->innertext;
		}

		$output = (string)$html_dom;
    }
}
?>