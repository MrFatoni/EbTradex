<?php

namespace App\Services\Api;

abstract class Bitcoind implements CryptoStockApiInterface
{
    public function generateAddress()
    {
        try{
            $response = $this->bitcoind->getnewaddress("");

            if(!empty( $response ) && is_null( $response->error() ))
            {
                return [
                    'error' => 'ok',
                    'result' => [
                        'address' => $response->result()
                    ],
                ];
            }

            return ['error' => 'Failed to generate address.'];
        }
        catch (\Exception $exception)
        {
            return ['error' => $exception->getMessage()];
        }
    }

    public function getTxnInfoByAddress($address)
    {
        //
    }

    public function getTxnInfoByTxnId($txid)
    {
        try{
            $response = $this->bitcoind->gettransaction($txid);

            if( !empty( $response ) && is_null( $response->error()) )
            {

                $result = $response->result();

                if ( $result['confirmations'] >= 1 )
                {
                    $txnStatus = 'completed';
                } else {
                    $txnStatus = 'pending';
                }

                return [
                    'error' => 'ok',
                    'result' => [
                        'txn_status' => $txnStatus,
                        'payment_method' => API_PAYPAL,
                        'ipn_type' => $result['details'][0]['category'] == 'send' ? 'withdrawal' : 'deposit',
                        'address' => $result['details'][0]['address'],
                        'txn_id' => $result['txid'],
                        'id' => $result['txid'],
                        'currency' => strtoupper($this->currency),
                        'amount' => $result['details'][0]['category'] == 'send' ? bcmul($result['amount'], "-1") : $result['amount'],
                        'fee' => $result['details'][0]['category'] == 'send' ? bcmul($result['fee'], "-1") : 0,
                    ]
                ];
            }

            return ['error' => 'No transaction found.'];
        }
        catch (\Exception $exception)
        {
            return ['error' => $exception->getMessage()];
        }
    }

    public function getTxnList($limit = 25)
    {
        //
    }

    public function sendToAddress($address, $amount)
    {
        try{
//            if( !$estimatedFee = $this->getEstimatedFee() )
//            {
//                return ['error' => 'Failed to get estimated.'];
//            }
            $estimatedFee = $this->networkFee;
            $spendableAmount = (float) bcsub($amount, $estimatedFee);

            if( bccomp( $this->getBalance(), $amount ) == -1 )
            {
                logs()->info('start withdrawal');
                logs()->info('logs: amount: ' . $amount);
                logs()->info('logs: spendable amount: ' . $spendableAmount);
                logs()->info('logs: fee: ' . $estimatedFee);
                logs()->info('logs: server balance: ' . $this->getBalance());
                logs()->info('end withdrawal');

                return ['error' => 'Insufficient balance to send.'];
            }

            $response = $this->bitcoind->sendtoaddress( $address, $spendableAmount);

            if(!empty( $response ) && is_null( $response->error() ))
            {
                return [
                    'error' => 'ok',
                    'result' => [
                        'txn_id' => $response->result()
                    ],
                ];
            }

            return ['error' => 'Failed to send.'];
        }
        catch (\Exception $exception)
        {
            return [ 'error' => $exception->getMessage() ];
        }
    }

    public function getEstimatedFee($block = 6)
    {
        try{
            $response = $this->bitcoind->estimatesmartfee($block);

            if( !empty( $response ) && is_null( $response->error() ))
            {
                return  bcmul($response->result()['feerate'], "1");
            }

            return false;
        }
        catch (\Exception $exception)
        {
            return false;
        }
    }

    public function getBalance()
    {
        try{
            $response = $this->bitcoind->getbalance("*");

            if(!empty( $response ) && is_null( $response->error() ))
            {
                return bcmul($response->result(), "1");
            }

            return 0;
        }
        catch (\Exception $exception)
        {
            return 0;
        }
    }

    public function validateIPN($post_data, $server_data)
    {
        if ( !isset($post_data['txn_id']) )
        {
            return ['error' => __('Invalid bitcoin ipn request.') ];
        }

        return $this->getTxnInfoByTxnId($post_data['txn_id']);
    }
}