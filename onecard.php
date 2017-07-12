<?php



	hbm_create('OneCard',array(
            'description'=>'OneCard Module for HostBill by ArabHosters',
            'version'=>'1.0',
            'currencies'=>array('USD','EGP','SAR')
        ));
    



	hbm_add_config_option('Merchant ID');
	hbm_add_config_option('TRANS KEY');
	hbm_add_config_option('KEYWORD');
hbm_add_config_option('Return URL');	

 hbm_on_action('payment.displayform', function($details){
        $onecard_url = 'http://onecard.n2vsb.com/customer/integratedPayment.html';

        //This will create url to callback route
        $callback_url = hbm_client_url('callback');

	 $merchant_id=hbm_get_config_option('Merchant ID');
	$TRANS_key=hbm_get_config_option('TRANS KEY');
	
	$timeIn = time();
	
	$token = md5($merchant_id.$details['invoice']['id'].$details['invoice']['amount'].$details['invoice']['currency'].$timeIn.$TRANS_key);


        $form = '<form name="onecard" action="'.$onecard_url.'" method="post" />
<input type="hidden" value="utf-8" name="charset">
<input type="hidden" id="OneCard_MerchID" name="OneCard_MerchID" value="'.$merchant_id.'" />
<input type="hidden" id="OneCard_TransID" name="OneCard_TransID" value="'.$details['invoice']['id'].'" />
<input type="hidden" id="OneCard_Amount" name="OneCard_Amount" value="'.$details['invoice']['amount'].'" />
<input type="hidden" id="OneCard_Currency" name="OneCard_Currency" value="'.$details['invoice']['currency'].'" />
<input type="hidden" id="OneCard_Timein" name="OneCard_Timein" value="'.$timeIn.'" />
<input type="hidden" id="OneCard_MProd" name="OneCard_MProd" value="'.$details['invoice']['description'].'" />
<input type="hidden" id="OneCard_ReturnURL" name="OneCard_ReturnURL" value = "'.$callback_url.'" />
<input type="hidden" id="OneCard_Field1" name="OneCard_Field1" value="'.$details['invoice']['id'].'" />
<input type="hidden" id="OneCard_Field1" name="OneCard_Field2" value="'.$details['invoice']['description'].'" />
<input type="hidden" id="OneCard_HashKey" name="OneCard_HashKey" value="'.$token.'" />
<input id="Submit" type="submit" value="ادفع الآن" />
</form>';
        return $form;
    });



hbm_client_route('callback',function($request) {
		//verify that request is valid and comes from gateway
		//log callback in gateway log:

		  $merchant_id=hbm_get_config_option('Merchant ID');
		$KEYWORD=hbm_get_config_option('KEYWORD');
		$return_url=hbm_get_config_option('Return URL');
		$token = md5($merchant_id.$_POST['OneCard_TransID'].$_POST['OneCard_Amount'].$_POST['OneCard_Currency'].$_POST['OneCard_RTime'].$KEYWORD.$_POST['OneCard_Code']);

		if($token == $_POST['OneCard_RHashKey'])
		{
			hbm_log_callback($_POST,'Successfull');

			$fee = $_POST['OneCard_Amount'] * 0.07;

			hbm_add_transaction( $_POST['OneCard_Field1'],$_POST['OneCard_Amount'],array(
                                'description' => $_POST['OneCard_Field2'],
				'fee' => $fee
                            ));

 echo '<META HTTP-EQUIV="Refresh" Content="0; URL='.$return_url.$_POST['OneCard_TransID'].'"/>';  


		}

	});







?>
