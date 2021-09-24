<?php
require ('stringer.php');

class Payment
{
    private $config;
    private $response;

    public function __construct()
    {
        $config_filename = ROOT_DIR.'/config.json';

        if (!file_exists($config_filename)) {
            throw new Exception("Can't find ".$config_filename);
        }
        $this->config = json_decode(file_get_contents($config_filename), true);
    }

    # process online payment
    public function process($data)
    {
        if(isset($data))
        {
            $merchant_code = $data['merchant_code'];
            $payment_mode = $data['payment_mode'];
            $transaction_id = $data['transaction_id'];
            $amount = $data['amount'];

            if($payment_mode == 'fpx' || $payment_mode == 'fpx1'){
                $payment_method = 'FPX';
            } else {
                $payment_method = 'Kad Kredit/Debit';
            }

            $encrypt = new StringerController();

            $checksum_data = [
                'TRANS_ID' => $transaction_id,
                'PAYMENT_MODE' => $payment_mode,
                'AMOUNT' => $amount,
                'MERCHANT_CODE' => $merchant_code
            ];

            $checksum = $encrypt->getChecksum($checksum_data);

            $fpx_data = array(
                'TRANS_ID' => $transaction_id,
                'AMOUNT' => $amount,
                'PAYEE_NAME' => $data['nama'],
                'PAYEE_EMAIL' => $data['email'],
                'EMAIL' => $data['email'],
                'PAYMENT_MODE' => $payment_mode,
                'BANK_CODE' => $data['bank_code'],
                'BE_MESSAGE' => $data['be_message'],
                'MERCHANT_CODE' => $merchant_code,
                'CHECKSUM' => trim($checksum),
                'nama' => $data['nama'],
                'nric' => $data['nric'],
                'telefon' => $data['telefon'],
                'catatan' => $data['catatan'],
            );

            # pass to FPX controller
            echo "<form id=\"myForm\" action=\"".$this->config['fpx']['url']."\" method=\"post\">";
            foreach ($fpx_data as $a => $b) {
                echo '<input type="hidden" name="'.htmlentities($a).'" value="'.filter_var($b, FILTER_SANITIZE_STRING).'">';
            }
            echo "</form>";
            echo "<script type=\"text/javascript\">
                document.getElementById('myForm').submit();
            </script>";

        } else {

            $this->response['status'] = 'failed';
            $this->response['message'] = 'Failed to process payment for this transaction';

            return json_encode($this->response);
        }
    }

    public function response()
    {
        $input = $_POST;

        $this->response['status'] = 'success';
        $this->response['data'] = $input;
        $this->response['message'] = 'Transaction has been completed';

        return json_encode($this->response);
    }
}
