<?php


namespace App\Services\Api;


use Denpa\Bitcoin\Client as BitcoinClient;

class BitcoinApi extends Bitcoind
{

    protected $bitcoind;
    protected $currency;
    protected $networkFee;

    public function __construct($currency)
    {
        $this->currency = $currency;
        $configuration = config(strtolower($currency));

        if( empty($configuration) )
        {
            return ['error' => __('Configuration not found for this :currency.', ['currency' => $currency])];
        }

        $this->networkFee = bcmul($configuration['network_fee'], "1");
        $configuration = array_except($configuration, 'network_fee');

        if( empty($configuration['user']) || empty($configuration['password']) )
        {
            return ['error' => __('Deposit / Withdrawal is currently disabled for this stock item.')];
        }

        $this->bitcoind = new BitcoinClient($configuration);
    }
}