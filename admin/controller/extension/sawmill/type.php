<?php
class ControllerExtensionSawmillType extends Controller {
    public $codename = 'sawmill';
    public $route = 'extension/sawmill/type';
    public $extension = '';
    private $error = array();
    private $input = array();
    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->load->language($this->route);
        $this->load->language('extension/module/sawmill');
        $this->load->model('extension/module/sawmill');
        $this->load->model('extension/d_opencart_patch/url');
        $this->d_shopunity = (file_exists(DIR_SYSTEM.'library/d_shopunity/extension/d_shopunity.json'));
        $this->extension = json_decode(file_get_contents(DIR_SYSTEM.'library/d_shopunity/extension/'.$this->codename.'.json'), true);
        $this->d_admin_style = (file_exists(DIR_SYSTEM.'library/d_shopunity/extension/d_admin_style.json'));
        
        $this->store_id = (isset($this->request->get['store_id'])) ? $this->request->get['store_id'] : 0;
    }
    public function index(){
        $this->getList();
    }
    public function add() {
        $this->document->setTitle($this->language->get('heading_title_main'));
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm(true)) {
            $this->{'model_extension_module_'.$this->codename.''}->addType($this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $url = '';
            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }
            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }
            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }
            $this->response->redirect($this->model_extension_d_opencart_patch_url->link($this->route, $url));
        }
        $this->getForm();
    }
    public function edit() {
        $this->document->setTitle($this->language->get('heading_title_main'));
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->{'model_extension_module_'.$this->codename}->editType($this->request->get['type_id'], $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $url = '';
            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }
            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }
            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }
            $this->response->redirect($this->model_extension_d_opencart_patch_url->link($this->route, $url));
        }
        $this->getForm();
    }
    public function delete() {
        $this->document->setTitle($this->language->get('heading_title_main'));
        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $type_id) {
                $this->{'model_extension_module_'.$this->codename}->deleteType($type_id);
            }
            $this->session->data['success'] = $this->language->get('text_success');
            $url = '';
            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }
            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }
            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }
            $this->response->redirect($this->model_extension_d_opencart_patch_url->link($this->route, $url));
        }
        $this->getForm();
    }
    public function getList() {
        $this->load->model('extension/d_opencart_patch/user');
        $this->load->model('extension/d_opencart_patch/load');
        $this->document->setTitle($this->language->get('heading_title_main'));
        if($this->d_admin_style){
            $this->load->model('extension/d_admin_style/style');
            $this->model_extension_d_admin_style_style->getAdminStyle('light');
        }
        $this->load->model('setting/setting');
        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'name';
        }
        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }
        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }
        $url = '';
        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }
        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }
        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->model_extension_d_opencart_patch_url->link('common/home'),
            'separator' => false
            );
        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_module'),
            'href'      => $this->model_extension_d_opencart_patch_url->link('marketplace/extension', 'type=module'),
            'separator' => ' :: '
            );
        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title_main'),
            'href'      => $this->model_extension_d_opencart_patch_url->link('extension/module/d_visual_designer'),
            'separator' => ' :: '
            );
        $data['add'] = $this->model_extension_d_opencart_patch_url->link($this->route.'/add', $url);
        $data['delete'] = $this->model_extension_d_opencart_patch_url->link($this->route.'/delete', $url);
        $data['cancel'] = $this->model_extension_d_opencart_patch_url->getExtensionLink('module');

        $data['href_types'] = $this->model_extension_d_opencart_patch_url->link('extension/'.$this->codename.'/type');
        $data['href_setting'] = $this->model_extension_d_opencart_patch_url->link('extension/'.$this->codename.'/setting');

        $data['heading_title'] = $this->language->get('heading_title_main');
        $data['version'] = $this->extension['version'];
        $data['route'] = $this->route;
        $data['token'] =  $this->model_extension_d_opencart_patch_user->getToken();
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');
        $data['text_list_type'] = $this->language->get('text_list_type');
        $data['text_types'] = $this->language->get('text_types');
        $data['text_setting'] = $this->language->get('text_setting');
        
        $data['column_name'] = $this->language->get('column_name');
        $data['column_action'] = $this->language->get('column_action');
        $data['button_add'] = $this->language->get('button_add');
        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['button_cancel'] = $this->language->get('button_cancel');
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }
        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }
        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array)$this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }
        $data['types'] = array();
        $filter_data = array(
            'sort'              => $sort,
            'order'             => $order,
            'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit'           => $this->config->get('config_limit_admin')
            );
        $type_total = $this->{'model_extension_module_'.$this->codename}->getTotalTypes($filter_data);
        
        $results = $this->{'model_extension_module_'.$this->codename}->getTypes($filter_data);
        
        
        foreach ($results as $result) {
            $data['types'][] = array(
                'type_id' => $result['type_id'],
                'name'   => $result['name'],
                'description'   => $result['description'],
                'edit'       => $this->model_extension_d_opencart_patch_url->link($this->route.'/edit','type_id=' . $result['type_id'] . $url)
                );
        }
        $url = '';
        if ($order == 'ASC') {
            $url .= '&order=' .  'DESC';
        } else {
            $url .= '&order=' .  'ASC';
        }
        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }
        $data['sort_name'] = $this->model_extension_d_opencart_patch_url->link($this->route,'sort=name' . $url);
        $url = '';
        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }
        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }
        $pagination = new Pagination();
        $pagination->total = $type_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->model_extension_d_opencart_patch_url->link($this->route, $url . '&page={page}');
        $data['pagination'] = $pagination->render();
        $data['results'] = sprintf($this->language->get('text_pagination'), ($type_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($type_total - $this->config->get('config_limit_admin'))) ? $type_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $type_total, ceil($type_total / $this->config->get('config_limit_admin')));
        $data['sort'] = $sort;
        $data['order'] = $order;
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->model_extension_d_opencart_patch_load->view('extension/'.$this->codename.'/type_list', $data));
    }
    public function getForm() {
        $this->load->model('extension/d_opencart_patch/user');
        $this->load->model('extension/d_opencart_patch/load');
        $this->document->setTitle($this->language->get('heading_title_main'));
        if($this->d_admin_style){
            $this->load->model('extension/d_admin_style/style');
            $this->model_extension_d_admin_style_style->getAdminStyle('light');
        }
        if(VERSION>='2.3.0.0'){
            $this->document->addScript('view/javascript/summernote/summernote.js');
            $this->document->addScript('view/javascript/summernote/opencart.js');
            $this->document->addStyle('view/javascript/summernote/summernote.css');
        }
        $this->load->model('setting/setting');
        $data['heading_title'] = $this->language->get('heading_title_main');
        $data['version'] = $this->extension['version'];
        $data['route'] = $this->route;
        $data['token'] =  $this->model_extension_d_opencart_patch_user->getToken();
        $data['text_form'] = !isset($this->request->get['type_id']) ? $this->language->get('text_add_type') : $this->language->get('text_edit_type');
        $data['text_types'] = $this->language->get('text_types');
        $data['text_routes'] = $this->language->get('text_routes');
        $data['text_setting'] = $this->language->get('text_setting');
        $data['text_instructions'] = $this->language->get('text_instructions');
        $data['text_file_manager'] = $this->language->get('text_file_manager');
        $data['entry_name'] = $this->language->get('entry_name');
        $data['entry_description'] = $this->language->get('entry_description');
        
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }
        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = '';
        }
        $url = '';
        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }
        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }
        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->model_extension_d_opencart_patch_url->link('common/home'),
            'separator' => false
            );
        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_module'),
            'href'      => $this->model_extension_d_opencart_patch_url->link('marketplace/extension', 'type=module'),
            'separator' => ' :: '
            );
        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title_main'),
            'href'      => $this->model_extension_d_opencart_patch_url->link('module/d_visual_designer'),
            'separator' => ' :: '
            );
        if (!isset($this->request->get['type_id'])) {
            $data['action'] = $this->model_extension_d_opencart_patch_url->link($this->route.'/add', $url);
        } else {
            $data['action'] = $this->model_extension_d_opencart_patch_url->link($this->route.'/edit', 'type_id=' . $this->request->get['type_id'] . $url);
        }
        $data['cancel'] = $this->model_extension_d_opencart_patch_url->link($this->route, $url);
        
        $data['config'] = false;
        
        if (!empty($this->request->get['type_id'])) {
            $type_info = $this->{'model_extension_module_'.$this->codename}->getType($this->request->get['type_id']);
        }
        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } elseif (!empty($type_info)) {
            $data['name'] = $type_info['name'];
        } else {
            $data['name'] = '';
        }
        
        if (isset($this->request->post['description'])) {
            $data['description'] = $this->request->post['description'];
        } elseif (!empty($type_info)) {
            $data['description'] = $type_info['description'];
        } else {
            $data['description'] = '';
        }
        
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->model_extension_d_opencart_patch_load->view('extension/'.$this->codename.'/type_form', $data));
    }
    protected function validateForm($new = false) {
        if (!$this->user->hasPermission('modify', $this->route)) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        
        if ((utf8_strlen($this->request->post['name']) < 1) || (utf8_strlen($this->request->post['name']) > 255)) {
            $this->error['name'] = $this->language->get('error_name');
        }
        
        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }
        return !$this->error;
    }
    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', $this->route)) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        return !$this->error;
    }

    public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('extension/module/'.$this->codename);

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->{'model_extension_module_'.$this->codename}->getTypes($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'type_id' => $result['type_id'],
					'name'            => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}