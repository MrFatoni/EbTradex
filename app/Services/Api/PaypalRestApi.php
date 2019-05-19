<?php

namespace App\Services\Api;

use PayPal\Api\Amount;
use PayPal\Api\Currency;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Payout;
use PayPal\Api\PayoutItem;
use PayPal\Api\PayoutSenderBatchHeader;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Rest\ApiContext;

class PaypalRestApi
{
    private $apiContext;

    public function __construct()
    {
        /** PayPal api context **/
        $paypalConfig = config('paypal');
        $this->apiContext = new ApiContext(
            new OAuthTokenCredential(
                $paypalConfig['client_id'],
                $paypalConfig['secret']
            )
        );
        $this->apiContext->setConfig($paypalConfig['settings']);
    }

    private function paypalAllowedCurrency()
    {
        return ['AUD', 'CAD', 'EUR', 'GBP', 'JPY', 'USD'];
    }

    public function payment($amount, $currency, $relatedTransaction = null)
    {
        if (!in_array($currency, $this->paypalAllowedCurrency())) {
            return false;
        }

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $amountConfig = new Amount();
        $amountConfig->setTotal($amount);
        $amountConfig->setCurrency($currency);

        $transaction = new Transaction();
        $transaction->setAmount($amountConfig);

        $redirectUrls = new RedirectUrls();
        $return_url = isset($relatedTransaction['return_url']) && !empty($relatedTransaction['return_url']) ? $relatedTransaction['return_url'] : $this->apiContext['return_url'];
        $cancel_url = isset($relatedTransaction['cancel_url']) && !empty($relatedTransaction['cancel_url']) ? $relatedTransaction['cancel_url'] : $this->apiContext['cancel_url'];
        $redirectUrls->setReturnUrl($return_url)
            ->setCancelUrl($cancel_url);

        $payment = new Payment();
        $paypalIntent = ["sale", "authorize", "order"];
        $intent = isset($relatedTransaction['intent']) && in_array($relatedTransaction['intent'], $paypalIntent) ? $relatedTransaction['intent'] : $this->apiContext['intent'];
        $payment->setIntent($intent)
            ->setPayer($payer)
            ->setTransactions(array($transaction))
            ->setRedirectUrls($redirectUrls);

        try {
            $payment->create($this->apiContext);
        } catch (PayPalConnectionException $exception) {
            return false;
        }

        return [
            'return_url' => $payment->getApprovalLink(),
            'payment_id' => $payment->getId(),
        ];
    }

    public function getPaymentStatus($paymentId, $payerId)
    {
        $payment = Payment::get($paymentId, $this->apiContext);
        $execution = new PaymentExecution();
        $execution->setPayerId($payerId);

        return $payment->execute($execution, $this->apiContext);
    }

    public function payout($receiver, $value, $currency = 'USD', $recipientType = 'Email')
    {
        $payouts = new Payout();
        $senderBatchHeader = new PayoutSenderBatchHeader();
        $senderBatchHeader->setSenderBatchId(uniqid())
            ->setEmailSubject("You have a Payout from " . company_name() . "!");

        $senderItem = new PayoutItem();
        $senderItem->setRecipientType($recipientType)
            ->setNote('You have received a payout! Thanks for being with ' . company_name() . ' !')
            ->setReceiver($receiver)
            ->setSenderItemId(uniqid())
            ->setAmount(new Currency('{
                        "value":"' . $value . '",
                        "currency":"' . $currency . '"
                    }'));
        $payouts->setSenderBatchHeader($senderBatchHeader)
            ->addItem($senderItem);

        try {
            $params = array('sync_mode' => 'false');
            $response = $payouts->create($params, $this->apiContext);

            return [
                'error' => 'ok',
                'result' => [
                    'txn_id' => $response->batch_header->payout_batch_id,
                    'status' => $response->batch_header->batch_status
                ]
            ];

        } catch (PayPalConnectionException $exception) {
            return [
                'error' => $exception->getMessage()
            ];
        }
    }
}