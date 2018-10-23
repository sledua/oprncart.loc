<?php
/*
*  location: admin/controller
*/

class ControllerExtensionDExportImportExcel extends Controller {

    private $codename = 'd_export_import';
    private $route = 'extension/d_export_import/excel';
    private $error = array();
    
    private $extension = array();
    
    public function __construct($registry) {
        parent::__construct($registry);

        $this->load->model('extension/module/'.$this->codename);
        $this->load->language($this->route);
        $this->load->language('extension/module/'.$this->codename);
        
        $this->d_admin_style = (file_exists(DIR_SYSTEM.'library/d_shopunity/extension/d_admin_style.json'));
        $this->d_shopunity = (file_exists(DIR_SYSTEM.'library/d_shopunity/extension/d_shopunity.json'));
        $this->extension = json_decode(file_get_contents(DIR_SYSTEM.'library/d_shopunity/extension/'.$this->codename.'.json'), true);
        $this->store_id = (isset($this->request->get['store_id'])) ? $this->request->get['store_id'] : 0;

    }

    public function index(){

        $this->load->model('extension/d_opencart_patch/url');
        $this->load->model('extension/d_opencart_patch/load');
        $this->load->model('extension/d_opencart_patch/user');
        $this->load->model('extension/d_opencart_patch/setting');
        $this->load->model('extension/d_opencart_patch/store');

        $json = array();

        $this->document->addScript("view/javascript/d_export_import/library/jquery.serializejson.min.js");
        
        $this->document->addScript('view/javascript/d_riot/riot+compiler.min.js');

        $this->document->addScript("view/javascript/d_export_import/d_export_import.js");
        if($this->d_shopunity) {
            $this->document->addScript('view/javascript/d_shopunity/d_shopunity_widget.js');
        }

        if($this->d_admin_style) {
            $this->load->model('extension/d_admin_style/style');
            $this->model_extension_d_admin_style_style->getAdminStyle('light');
        }

        // styles and scripts
        $this->document->addStyle('view/stylesheet/shopunity/bootstrap.css');

        // Add more styles, links or scripts to the project is necessary
        $url_params = array();
        $url = '';

        if(isset($this->response->get['store_id'])){
            $url_params['store_id'] = $this->store_id;
        }

        $url = ((!empty($url_params)) ? '&' : '' ) . http_build_query($url_params);

        $this->document->setTitle($this->language->get('heading_title_main'));
        $data['heading_title'] = $this->language->get('heading_title_main');
        $data['text_edit'] = $this->language->get('text_edit');

        $data['codename'] = $this->codename;
        $data['route'] = $this->route;
        $data['version'] = $this->extension['version'];
        $data['token'] = $this->model_extension_d_opencart_patch_user->getToken();
        $data['d_shopunity'] = $this->d_shopunity;

        $json['translate'] = array();

        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_default'] = $this->language->get('text_default');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');
        $data['text_export_progress'] = $this->language->get('text_export_progress');
        $json['translate']['text_memory_usage'] = $this->language->get('text_memory_usage');
        $json['translate']['text_progress'] = $this->language->get('text_progress');
        $json['translate']['text_current_item'] = $this->language->get('text_current_item');
        $json['translate']['text_progress_sheets'] = $this->language->get('text_progress_sheets');
        $json['translate']['text_progress_files'] = $this->language->get('text_progress_files');
        $json['translate']['text_left_time'] = $this->language->get('text_left_time');
        $json['translate']['text_success_title'] = $this->language->get('text_success_title');
        $json['translate']['text_error_title'] = $this->language->get('text_error_title');
        $json['translate']['text_please_wait'] = $this->language->get('text_pleas_wait');
        $json['translate']['text_filter_title'] = $this->language->get('text_filter_title');
        $json['translate']['text_not_selected'] = $this->language->get('text_not_selected');

        $data['entry_source'] = $this->language->get('entry_source');
        $data['entry_language'] = $this->language->get('entry_language');
        
        $data['column_name'] = $this->language->get('column_name');
        $data['column_description'] = $this->language->get('column_description');
        $data['column_action'] = $this->language->get('column_action');

        $data['button_export'] = $this->language->get('button_export');
        $data['button_import'] = $this->language->get('button_import');
        $data['button_filter'] = $this->language->get('button_filter');
        $json['translate']['button_close'] = $this->language->get('button_close');
        $data['button_cancel'] = $this->language->get('button_cancel');

        $data['module_link'] = $this->model_extension_d_opencart_patch_url->link($this->route);
        $data['action'] = $this->model_extension_d_opencart_patch_url->link($this->route.'/export');


        $data['cancel'] = $this->model_extension_d_opencart_patch_url->link('marketplace/extension', 'type=module');

        $modules = $this->{'model_extension_module_'.$this->codename}->getModules();

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        $data['modules'] = array();
        $json['filters'] = array();

        $json['conditions'] = array(
            '=' => $this->language->get('text_equal'),
            '<>' => $this->language->get('text_not_equal'),
            '>=' => $this->language->get('text_greater_or_equal'),
            '<=' => $this->language->get('text_less_or_equal'),
            '>' => $this->language->get('text_greater'),
            '<' => $this->language->get('text_less'),
            'LIKE' => $this->language->get('text_like')
            );

        foreach ($modules as $value) {
            $module_setting = $this->{'model_extension_module_'.$this->codename}->getModuleSetting($value);
            $this->load->language('extension/'.$this->codename.'_module/'.$value);
            
            $json['filters'][$value] = $this->{'model_extension_module_'.$this->codename}->getModuleFilters($value);

            $data['modules'][$value] = array(
                'title' => $this->language->get('text_title'),
                'description' => $this->language->get('text_description')
                );
        }

        $json['selected_filters'] = array();

        //get store
        $data['store_id'] = $this->store_id;
        $data['stores'] = $this->model_extension_d_opencart_patch_store->getAllStores();

        $this->load->model('setting/store');

        // Breadcrumbs
        $data['breadcrumbs'] = array(); 
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->model_extension_d_opencart_patch_url->link('common/home')
            );
        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_module'),
            'href'      => $this->model_extension_d_opencart_patch_url->link('marketplace/extension', 'type=module')
            );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_main'),
            'href' => $this->model_extension_d_opencart_patch_url->link($this->route)
            );

        foreach($this->error as $key => $error){
            $data['error'][$key] = $error;
        }

        $data['tabs'] = $this->{'model_extension_module_'.$this->codename}->getTabs('excel');

        $json['token'] = $this->model_extension_d_opencart_patch_user->getToken();

        if($this->request->server['HTTPS']){
            $json['server'] = HTTPS_SERVER;
        }
        else{
            $json['server'] = HTTP_SERVER;
        }

        $data['json'] = $json;


        $data['riot_tags'] = $this->{'model_extension_module_'.$this->codename}->getRiotTags();

        $data['notify'] = $this->{'model_extension_module_'.$this->codename}->checkCompleteVersion();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->model_extension_d_opencart_patch_load->view($this->route, $data));
    }

    public function export(){
        $json = array();
        $this->load->model('extension/'.$this->codename.'/export');
        if(isset($this->request->post['source'])){
            $source = $this->request->post['source'];
        }

        if(isset($this->request->post['language_id'])){
            $language_id = $this->request->post['language_id'];
        }

        if(isset($this->request->post['filters'])){
            $filters = $this->request->post['filters'];
        }
        else{
            $filters = array();
        }

        if(isset($source) && isset($language_id)){
            $json = $this->{'model_extension_'.$this->codename.'_export'}->export($source, $language_id, $filters);
        }
        else{
            $json['error'] = 'error';
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function download(){
        $json = array();
        $this->load->model('extension/'.$this->codename.'/export');
        if(isset($this->request->get['source'])){
            $source = $this->request->get['source'];
        }

        if(isset($source)){
            $this->{'model_extension_'.$this->codename.'_export'}->save($source);
        }
        else{
            $json['error'] = 'error';
        }
    }
    public function import(){

        $json = array();
        
        $this->load->model('extension/'.$this->codename.'/import');

        if(isset($this->request->post['recipient'])){
            $recipient = $this->request->post['recipient'];
        }
        else{
            $json['error'] = 'error';
        }

        if(isset($this->request->post['language_id'])){
            $language_id = $this->request->post['language_id'];
        }
        else{
            $json['error'] = 'error';
        }

        if(!isset($json['error'])){
            $result = $this->{'model_extension_'.$this->codename.'_import'}->prepare_upload_file();
            if(!$result){
                $json['error'] = 'error';
            }
        }
        
        if(!isset($json['error'])){
            $json = $this->{'model_extension_'.$this->codename.'_import'}->import($recipient, $language_id);
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }
}