<?php

class ControllerExtensionModuleVantage extends Controller {
	
	public function index($setting) {
		
		$this->language->load('extension/module/vantage');

		$data['heading_title'] = $this->language->get('heading_title');

		//Load Styles & Scripts
		$this->document->addStyle('catalog/view/theme/raspilok/stylesheet/stylesheet.css');
		// $this->document->addScript('catalog/view/javascript/path/to/library.js');

		$data['field'] = $setting['field'];

		return $this->load->view('extension/module/vantage', $data);

	}
}
