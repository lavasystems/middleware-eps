<?php
require_once ('merchant.php');
$merchant = new Merchant();

$merchant_list = $merchant->get_merchant_list();
echo json_encode($merchant_list,true);
