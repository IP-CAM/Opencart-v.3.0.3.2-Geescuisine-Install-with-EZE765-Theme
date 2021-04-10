<?php

class ControllerExtensionModuleZemezSingleProduct extends Controller
{
    public function index($setting)
    {
        static $module = 0;
        $this->load->language('extension/module/zemez_single_product');
        $data['thumb_width'] = $setting['thumb_width'];
        $data['thumb_height'] = $setting['thumb_height'];
        $this->load->model('catalog/product');
        $this->load->model('tool/image');


        if ($setting['product'][0]) {
            $product_info = $this->model_catalog_product->getProduct($setting['product'][0]);
            if ($product_info['image']) {
                $image = $this->model_tool_image->resize($product_info['image'], $setting['width'], $setting['height']);
                $image_thumb = $this->model_tool_image->resize($product_info['image'], $setting['thumb_width'], $setting['thumb_height']);
            } else {
                $image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
                $image_thumb = $this->model_tool_image->resize('placeholder.png', $setting['thumb_width'], $setting['thumb_height']);
            }

            if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                $price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
            } else {
                $price = false;
            }


            if ($product_info['minimum']) {
                $data['minimum'] = $product_info['minimum'];
            } else {
                $data['minimum'] = 1;
            }
            $add_imgs = $this->model_catalog_product->getProductImages($setting['product'][0]);
            $add_imgs_1 = [];
            foreach ($add_imgs as $img) {
                array_push(
                    $add_imgs_1, [
                        $this->model_tool_image->resize($img['image'], $setting['thumb_width'], $setting['thumb_height']),
                        $this->model_tool_image->resize($img['image'], $setting['width'], $setting['height'])
                    ]
                );
            }


            $data['product'] = [
                'image'             => $image,
                'image_width'       => $setting['thumb_width'],
                'image_height'      => $setting['thumb_height'],
                'image_thumb'       => $image_thumb,
                'name'              => $product_info['name'],
                'description'       => utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, 300) . '..',
                'product_id'        => $product_info['product_id'],
                'price'             => $price,
                'href'              => $this->url->link('product/product', 'product_id=' . $product_info['product_id']),
                'add_imgs'          => $add_imgs_1,
                'allow'             => $product_info['minimum'],
                'text_availability' => $product_info['quantity'],
                'author'            => $product_info['manufacturer'],
                'manufacturers'     => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id'])
            ];
        }
        $data['module'] = $module++;
        $this->document->addScript('catalog/view/javascript/jquery/swiper/js/swiper.jquery.js');
        return $this->load->view('extension/module/zemez_single_product', $data);
    }
}