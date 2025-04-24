<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$vnp_TmnCode    = "FC0EMZOC";                         //Mã định danh merchant kết nối (Terminal Id)
$vnp_HashSecret = "AKNEMUXTLPHYQKWTE6FEK49ROGTNVPT4"; //Secret key
$vnp_Url        = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
// $vnp_Returnurl  = "http://minhhieu130903.com/?quanly=camon";
$vnp_Returnurl = "http://localhost/orderfood.git/user/index.php";
$vnp_apiUrl    = "https://sandbox.vnpayment.vn/merchant_webapi/api/transaction";
$apiUrl        = "https://sandbox.vnpayment.vn/merchant_webapi/api/transaction";
//Config input format
//Expire
$startTime = date("YmdHis");
$expire    = date('YmdHis', strtotime('+15 minutes', strtotime($startTime)));
//http: //minhhieu13092003.com/vnpay_create_payment.php?vnp_Amount=0&vnp_Command=pay&vnp_CreateDate=20250204140939&vnp_CurrCode=VND&vnp_ExpireDate=20250204142439&vnp_IpAddr=127.0.0.1&vnp_Locale=&vnp_OrderInfo=Thanh+toan+GD%3A3526&vnp_OrderType=other&vnp_ReturnUrl=http%3A%2F%2Fminhhieu13092003.com%2F&vnp_TmnCode=EFGJ1I4U&vnp_TxnRef=3526&vnp_Version=2.1.0&vnp_SecureHash=d82aaea454e264b01caedda3b35cc2d4b19de007fb63589e47c7d3b607ea9922f37c4cd26335096989d531a2b8fa7220d77d235921d880a51c4bf1b5e5538cdc
