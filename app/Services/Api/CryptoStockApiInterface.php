<?php

namespace App\Services\Api;

interface CryptoStockApiInterface
{
    public function generateAddress();

    public function getTxnInfoByAddress($address);

    public function getTxnInfoByTxnId($txid);

    public function getTxnList($limit);

    public function sendToAddress($address, $amount);

    public function validateIPN($post_data, $server_data);
}