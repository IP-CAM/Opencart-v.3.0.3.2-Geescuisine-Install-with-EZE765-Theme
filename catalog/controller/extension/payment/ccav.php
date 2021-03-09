<?php
class ControllerExtensionPaymentCcav extends Controller {
	public function index() {
		$data['button_confirm'] = $this->language->get('button_confirm');

		$data['text_loading'] = $this->language->get('text_loading');
		$this->load->model("extension/payment/ccav");
		$this->load->model('checkout/order');            
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		$redirect = $this->model_extension_payment_ccav->proceed($order_info);			
		$data['continue'] = $redirect;
		return $this->load->view('extension/payment/ccav', $data);
	}

	public function confirm() {
		if ($this->session->data['payment_method']['code'] == 'ccav') {
			$this->load->model('checkout/order');
			
			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('ccav_order_status_id'));
		}
	}
	public function proceed()
	{
		$this->load->model('checkout/order');
		$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], 1);
		if($this->request->get['r'])
			echo "<script>window.location='".htmlspecialchars_decode(urldecode($this->request->get['r']))."'</script>";
		else
			echo 'Incorrect URL';
	}

	public function response()
	{
		$encResponse=$this->request->post["encResp"];			//This is the response sent by the CCAvenue Server
		//$rcvdString=decrypt($encResponse,$workingKey);		//Crypto Decryption used as per the specified working key.
		
		//echo $encResponse;
		$this->load->model('extension/payment/ccav');
		$order_status = $this->model_extension_payment_ccav->response($encResponse);
		if($order_status=="Success")
		{
			$this->load->model('checkout/order');
			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payment_ccav_order_status_id'));
			//echo "<br>Thank you for shopping with us. Your credit card has been charged and your transaction is successful. We will be shipping your order to you soon.<script type='text/javascript'>Android.showSuccess('Payment Successfully Done');	</script>";
			$this->response->redirect($this->url->link('checkout/success'));
		}
		elseif($order_status=="Aborted")
		{
			//echo "<br>Thank you for shopping with us.We will keep you posted regarding the status of your order through e-mail. <script type='text/javascript'>Android.showError('Payment Processing Failed');	</script>";
			$this->session->data['error'] = "Sorry Transaction Failed Please try again";

				$this->response->redirect($this->url->link('checkout/checkout', '', true));
		}
		elseif($order_status=="Failure")
		{
			//echo "<br>Thank you for shopping with us.However,the transaction has been declined. <a href=\"/\">Home</a> <script type='text/javascript'>Android.showError('Payment Processing Failed');	</script>";
			$this->session->data['error'] = "Sorry Transaction Failed Please try again";

			$this->response->redirect($this->url->link('checkout/checkout', '', true));
		}
		else
		{
			$this->session->data['error'] = "Sorry Transaction Failed Please try again";

				$this->response->redirect($this->url->link('checkout/checkout', '', true));
			//echo "<br>Security Error. Illegal access detected <a href=\"/\">Home</a> <script type='text/javascript'>Android.showError('Payment Processing Failed');	</script> ";
		
		}
		
	}
}
