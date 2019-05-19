<?php
/*
	CoinPayments.net API Class - v1.1
	Copyright 2014-2018 CoinPayments.net. All rights reserved.
	License: GPLv2 - http://www.gnu.org/licenses/gpl-2.0.txt
*/

namespace App\Services\Api;

use App\Exceptions\JobException;

class CoinPaymentApi implements CryptoStockApiInterface
{
    private $privateKey;
    private $publicKey;
    private $merchantID;
    private $ipnSecret;
    private $ipnUrl;
    private $ch;
    private $currency;

    public function __construct($currency)
    {
        $configuration = config('coinpayment');
        $this->privateKey = $configuration['private_key'];
        $this->publicKey = $configuration['public_key'];
        $this->merchantID = $configuration['merchant_id'];
        $this->ipnSecret = $configuration['ipn_secret'];
        $this->ipnUrl = $configuration['ipn_url'];
        $this->ch = $configuration['ch'];
        $this->currency = $currency;
    }

    private function is_setup()
    {
        return !empty($this->privateKey) && !empty($this->publicKey) && !empty($this->merchantID) && !empty($this->ipnSecret) && !empty($this->ipnUrl);
    }

    private function api_call($cmd, $req = array())
    {
        if (!$this->is_setup()) {
            return array('error' => 'You have not called the Setup function with your private and public keys!');
        }

        // Set the API command and required fields
        $req['version'] = 1;
        $req['cmd'] = $cmd;
        $req['key'] = $this->publicKey;
        $req['format'] = 'json'; //supported values are json and xml

        // Generate the query string
        $post_data = http_build_query($req, '', '&');

        // Calculate the HMAC signature on the POST data
        $hmac = hash_hmac('sha512', $post_data, $this->privateKey);

        // Create cURL handle and initialize (if needed)
        if ($this->ch === null) {
            $this->ch = curl_init('https://www.coinpayments.net/api.php');
            curl_setopt($this->ch, CURLOPT_FAILONERROR, TRUE);
            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
        }
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, array('HMAC: ' . $hmac));
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $post_data);

        $data = curl_exec($this->ch);
        if ($data !== FALSE) {
            if (PHP_INT_SIZE < 8 && version_compare(PHP_VERSION, '5.4.0') >= 0) {
                // We are on 32-bit PHP, so use the bigint as string option. If you are using any API calls with Satoshis it is highly NOT recommended to use 32-bit PHP
                $dec = json_decode($data, TRUE, 512, JSON_BIGINT_AS_STRING);
            } else {
                $dec = json_decode($data, TRUE);
            }
            if ($dec !== NULL && count($dec)) {
                return $dec;
            } else {
                // If you are using PHP 5.5.0 or higher you can use json_last_error_msg() for a better error message
                return array('error' => 'Unable to parse JSON result (' . json_last_error() . ')');
            }
        } else {
            return array('error' => 'cURL error: ' . curl_error($this->ch));
        }
    }

    public function generateAddress()
    {
        $req = array(
            'currency' => $this->currency,
            'ipn_url' => !empty($ipn_url) ? $ipn_url : $this->ipnUrl,
        );

        return $this->api_call('get_callback_address', $req);
    }

    public function getTxnInfoByAddress($address)
    {
        //
    }

    public function getTxnInfoByTxnId($txid)
    {
        return $this->api_call('get_tx_info', array('txid' => $txid));
    }

    public function getTxnList($limit = 25)
    {
        return $this->api_call('get_tx_ids', array('limit' => $limit));
    }

    public function sendToAddress($address, $amount)
    {
        $req = array(
            'amount' => $amount,
            'currency' => $this->currency,
            'address' => $address,
            'auto_confirm' => 1,
            'ipn_url' => $this->ipnUrl,
        );
        $response = $this->api_call('create_withdrawal', $req);

        if ($response['error'] == 'ok') {
            return [
                'error' => $response['error'],
                'result' => [
                    'txn_id' => $response['result']['id']
                ],
            ];
        }

        return $response;
    }

    // send the validateIPN to $request->all(), $request->server()
    public function validateIPN($post_data, $server_data)
    {
        try {
            if (!isset($post_data['ipn_mode'], $post_data['merchant'], $post_data['status'], $post_data['status_text'])) {
                throw new JobException("Insufficient POST data provided.");
            }

            if ($post_data['ipn_mode'] == 'httpauth') {
                if ($server_data['PHP_AUTH_USER'] !== $this->merchantID) {
                    throw new JobException("Invalid merchant ID provided.");
                }
                if ($server_data['PHP_AUTH_PW'] !== $this->ipnSecret) {
                    throw new JobException("Invalid IPN secret provided.");
                }
            } elseif ($post_data['ipn_mode'] == 'hmac') {
                $hmac = hash_hmac("sha512", file_get_contents('php://input'), $this->ipnSecret);
                if ($hmac !== $server_data['HTTP_HMAC']) {
                    throw new JobException("Invalid HMAC provided.");
                }
                if ($post_data['merchant'] !== $this->merchantID) {
                    throw new JobException("Invalid merchant ID provided.");
                }
            } else {
                throw new JobException("Invalid IPN mode provided.");
            }
            $order_status = $post_data['status'];
            // define status according to status
            if (($order_status >= 100 || $order_status == 2)) {
                $txnStatus = 'completed';
            } elseif ($order_status < 0) {
                $txnStatus = 'failed';
            } else {
                $txnStatus = 'pending';
            }

            $post_data['txn_status'] = $txnStatus;
            $post_data['payment_method'] = API_COINPAYMENT;
            $post_data['fee'] = isset( $post_data['fee'] ) ? $post_data['fee'] : 0;

            return [
                'error' => 'ok',
                'result' => $post_data
            ];

        } catch (\Exception $exception) {
            return ['error' => $exception->getMessage()];
        }
    }
}