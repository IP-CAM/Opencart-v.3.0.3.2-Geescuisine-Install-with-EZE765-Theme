<?php
class ControllerExtensionModuleZemezDealBanner extends Controller {
	public function index($setting) {
		static $module = 0;
		$this->load->model('tool/image');

		$this->document->addScript('catalog/view/theme/' . $this->config->get('theme_' . $this->config->get('config_theme') . '_directory') . '/js/zemezdealbanner/jquery.countdown.min.js');

		$data['date']   = $setting['date'];

		if (strip_tags(html_entity_decode($setting['description'][$this->config->get('config_language_id')]['description']))) {
			$data['description'] = html_entity_decode($setting['description'][$this->config->get('config_language_id')]['description']);
		} else {
			$data['description'] = '';
		}

		$data['days_title']    = $setting['description'][$this->config->get('config_language_id')]['days_title'] ? $setting['description'][$this->config->get('config_language_id')]['days_title'] : 'Days';
		$data['hours_title']   = $setting['description'][$this->config->get('config_language_id')]['hours_title'] ? $setting['description'][$this->config->get('config_language_id')]['hours_title'] : 'Hours';
		$data['minutes_title'] = $setting['description'][$this->config->get('config_language_id')]['minutes_title'] ? $setting['description'][$this->config->get('config_language_id')]['minutes_title'] : 'Minutes';
		$data['seconds_title'] = $setting['description'][$this->config->get('config_language_id')]['seconds_title'] ? $setting['description'][$this->config->get('config_language_id')]['seconds_title'] : 'Seconds';


		if (is_file(DIR_IMAGE . $setting['image']) && $setting['image']) {
			$data['image']        = $this->model_tool_image->resize($setting['image'], $setting['image_width'], $setting['image_height']);
			$data['image_width']  = $setting['image_width'];
			$data['image_height'] = $setting['image_height'];
		} else {
			$data['image'] = '';
		}

		$data['module'] = $module++;

		return $this->load->view('extension/module/zemez_deal_banner', $data);
	}
}
