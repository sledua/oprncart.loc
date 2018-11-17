<?php
/*
 *  location: admin/controller
 */

class ControllerExtensionSawmillSetting extends Controller {

    private $codename = 'sawmill';
    private $route = 'extension/sawmill/setting';
    private $config_file = 'sawmill';
    private $extension = array();
    private $store_id = 0;
    private $error = array();


    public function __construct($registry) {
        parent::__construct($registry);
        $this->load->language($this->route);
        $this->load->language('extension/module/'.$this->codename);
        $this->load->model('extension/module/'.$this->codename);
        
        $this->d_shopunity = (file_exists(DIR_SYSTEM.'library/d_shopunity/extension/d_shopunity.json'));
        $this->d_opencart_patch = (file_exists(DIR_SYSTEM.'library/d_shopunity/extension/d_opencart_patch.json'));
        $this->d_twig_manager = (file_exists(DIR_SYSTEM.'library/d_shopunity/extension/d_twig_manager.json'));
        $this->d_event_manager = (file_exists(DIR_SYSTEM.'library/d_shopunity/extension/d_event_manager.json'));
        $this->d_admin_style = (file_exists(DIR_SYSTEM.'library/d_shopunity/extension/d_admin_style.json'));
        $this->extension = json_decode(file_get_contents(DIR_SYSTEM.'library/d_shopunity/extension/'.$this->codename.'.json'), true);
        $this->store_id = (isset($this->request->get['store_id'])) ? $this->request->get['store_id'] : 0;

    }

    public function index(){

        if ($this->d_admin_style){
            $this->load->model('extension/d_admin_style/style');
            $this->model_extension_d_admin_style_style->getStyles('light');
        }

        $this->load->model('setting/setting');
        $this->load->model('extension/d_opencart_patch/load');
        $this->load->model('extension/d_opencart_patch/user');
        $this->load->model('extension/d_opencart_patch/url');
        $this->load->model('extension/d_opencart_patch/cache');

        $this->model_extension_d_opencart_patch_cache->clearTwig();

        //save
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            $this->uninstallEvents();

            if($this->request->post['module_'.$this->codename.'_status']) {
                $this->installEvents();
            }

            $this->model_setting_setting->editSetting('module_'.$this->codename, $this->request->post, $this->store_id);
            $this->session->data['success'] = $this->language->get('success_modifed');

            $this->response->redirect($this->model_extension_d_opencart_patch_url->getExtensionLink('module'));
        }

        //notification
        foreach($this->error as $key => $error){
            $data['error'][$key] = $error;
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        //styles and scripts
        $this->document->addScript('view/javascript/d_bootstrap_switch/js/bootstrap-switch.min.js');
        $this->document->addStyle('view/javascript/d_bootstrap_switch/css/bootstrap-switch.css');

        //title
        $this->document->setTitle($this->language->get('heading_title_main'));
        $data['heading_title'] = $this->language->get('heading_title_main');
        $data['text_edit'] = $this->language->get('text_edit');
        
        //vars
        $data['codename'] = $this->codename;
        $data['route'] = $this->route;
        $data['version'] = $this->extension['version'];
        $data['token'] =  $this->model_extension_d_opencart_patch_user->getToken();
        $data['d_shopunity'] = $this->d_shopunity;
        $data['store_id'] = $this->store_id;
        $data['setup'] = false;

        //text
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_powered_by'] = $this->language->get('text_powered_by');
        $data['text_pro'] = $this->language->get('text_pro');
        $data['text_module'] = $this->language->get('text_module');

        //entry
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_material_type'] = $this->language->get('entry_material_type');
        $data['entry_edge_type'] = $this->language->get('entry_edge_type');

        //tab
        $data['text_setting'] = $this->language->get('text_setting');
        $data['text_types'] = $this->language->get('text_types');

        //button
        $data['button_save'] = $this->language->get('button_save');
        $data['button_save_and_stay'] = $this->language->get('button_save_and_stay');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_clear'] = $this->language->get('button_clear');
        $data['button_add'] = $this->language->get('button_add');
        $data['button_remove'] = $this->language->get('button_remove');

        //welcome
        $data['text_welcome'] = $this->language->get('text_welcome');

        //action
        $data['module_link'] = $this->model_extension_d_opencart_patch_url->link($this->route);
        $data['action'] = $this->model_extension_d_opencart_patch_url->link($this->route);
        $data['cancel'] = $this->model_extension_d_opencart_patch_url->getExtensionLink('module');
        $data['setup_link'] = $this->model_extension_d_opencart_patch_url->ajax($this->route.'/setup');

        $data['href_setting'] = $this->model_extension_d_opencart_patch_url->link('extension/'.$this->codename.'/setting');
        $data['href_types'] = $this->model_extension_d_opencart_patch_url->link('extension/'.$this->codename.'/type');

        $data['autocomplete_link'] = $this->model_extension_d_opencart_patch_url->ajax('extension/'.$this->codename.'/type/autocomplete');

        //stores
        $this->load->model('extension/d_opencart_patch/store');
        $data['stores'] = $this->model_extension_d_opencart_patch_store->getAllStores();

        //status
        if (isset($this->request->post['module_'.$this->codename.'_status'])) {
            $data[$this->codename.'_status'] = $this->request->post['module_'.$this->codename.'_status'];
        } else {
            $data[$this->codename.'_status'] = $this->config->get('module_'.$this->codename.'_status');
        }

        $data['setting'] = $this->getSetting();

        if(!empty($data['setting']['material_type_id'])) {
            $type_info= $this->{'model_extension_module_'.$this->codename}->getType($data['setting']['material_type_id']);
            $data['material_type'] = $type_info['name'];
        } else {
            $data['material_type'] = '';
        }

        if(!empty($data['setting']['edge_type_id'])) {
            $type_info= $this->{'model_extension_module_'.$this->codename}->getType($data['setting']['edge_type_id']);
            $data['edge_type'] = $type_info['name'];
        } else {
            $data['edge_type'] = '';
        }

        $data['setup'] = $data[$this->codename.'_status'];

        //setting

        // Breadcrumbs
        $data['breadcrumbs'] = array(); 
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->model_extension_d_opencart_patch_url->link('common/home')
            );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_module'),
            'href'      => $this->model_extension_d_opencart_patch_url->getExtensionLink('module')
            );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_main'),
            'href' => $this->model_extension_d_opencart_patch_url->link($this->route)
            );

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->model_extension_d_opencart_patch_load->view($this->route, $data));
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


    private function validate($permission = 'modify') {

        $this->language->load($this->route);
        
        if (!$this->user->hasPermission($permission, $this->route)) {
            $this->error['warning'] = $this->language->get('error_permission');
            return false;
        }

        return true;
    }

    public function setup(){
        $this->load->language($this->route);
        $this->load->model('setting/setting');
        $this->load->model('extension/d_opencart_patch/url');
        
        $setting['module_'.$this->codename.'_status'] = 1;
        $this->installEvents();
        $this->model_setting_setting->editSetting('module_'.$this->codename, $setting, $this->store_id);
        $this->session->data['success'] = $this->language->get('success_setup');
        $this->response->redirect($this->model_extension_d_opencart_patch_url->link($this->route));
    }

    public function uninstallEvents() {
         if($this->d_event_manager){
            $this->load->model('extension/module/d_event_manager');
            $this->model_extension_module_d_event_manager->deleteEvent($this->codename);
        }
    }

    public function installEvents() {
         if($this->d_event_manager){
            $this->load->model('extension/module/d_event_manager');
            $this->model_extension_module_d_event_manager->addEvent($this->codename, 
                'admin/view/catalog/product_form/after', 
                'extension/module/sawmill/view_catalog_product_after');
            $this->model_extension_module_d_event_manager->addEvent($this->codename, 
                'admin/model/catalog/product/addProduct/after', 
                'extension/module/sawmill/model_catalog_product_addProduct_after');
            $this->model_extension_module_d_event_manager->addEvent($this->codename, 
                'admin/model/catalog/product/editProduct/after', 
                'extension/module/sawmill/model_catalog_product_editProduct_after');
        }
    }

}
?>