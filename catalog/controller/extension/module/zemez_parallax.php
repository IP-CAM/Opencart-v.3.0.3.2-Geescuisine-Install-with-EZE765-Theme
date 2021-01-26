<?php

class ControllerExtensionModuleZemezParallax extends Controller
{
	public function index($setting)
	{
		static $module = 0;

		$this->load->model('design/banner');
		$this->load->model('tool/image');

        $this->document->addScript('catalog/view/theme/' . $this->config->get('theme_' . $this->config->get('config_theme') . '_directory') . '/js/zemez_parallax/rd-parallax-min.js', 'footer');

		if (is_file(DIR_IMAGE . $setting['image'])) {
			$data['image'] = $this->model_tool_image->resize($setting['image'], $setting['width'], $setting['height']);
		} else {
			$data['image'] = '';
		}

		if ($setting['speed']) {
			$data['speed'] = $setting['speed'];
		} else {
			$data['speed'] = '0.2';
		}

		if ($setting['direction']) {
			$data['direction'] = 'normal';
		} else {
			$data['direction'] = 'inverse';
		}

		if ($setting['blur']) {
			$data['blur'] = 'true';
		} else {
			$data['blur'] = 'false';
		}

		$this->load->model('setting/module');
		$this->load->model('extension/module/zemez_parallax');
		$data['layers'] = array();

		if (isset($setting['layers']) && $setting['layers']) {
			$i=0;
			foreach ($setting['layers'] as $layer) {
				$data['layers'][$i] = array(
					'type' => $layer['type'] == 0 ? 'html' : 'media',
					'description' => html_entity_decode($layer['description'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8'),
					'speed' => $layer['speed'],
					'fade' => $layer['fade'] == 0 ? 'false' : 'true',
					'direction' => $layer['direction'] == 0 ? 'inverse' : 'normal'
					);

				if (isset($layer['image']) && $layer['image']) {
					$data['layers'][$i]['image']        = $this->model_tool_image->resize($layer['image'], $layer['width'], $layer['height']);
					$data['layers'][$i]['image_width']  = $layer['width'];
					$data['layers'][$i]['image_height'] = $layer['height'];
					$data['layers'][$i]['blur']         = $layer['blur'] == 0 ? 'false' : 'true';
				}

				if (isset($layer['module_id'])) {
					foreach ($layer['module_id'] as $module) {
						$code = $this->model_extension_module_zemez_parallax->getModuleCode($module);

						$setting_info = $this->model_setting_module->getModule($module);

						if ($setting_info && $setting_info['status']) {
							$data['layers'][$i]['modules'][] = $this->load->controller('extension/module/' . $code, $setting_info);
						}
					}
				}
				$i++;
			}
		}

		$data['module_counter'] = $module++;

		return $this->load->view('extension/module/zemez_parallax', $data);
	}
}