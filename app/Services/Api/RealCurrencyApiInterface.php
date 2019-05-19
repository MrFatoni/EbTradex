<?php

namespace App\Services\Api;

interface RealCurrencyApiInterface
{
    public function allowedCurrency();

    public function payment($amount, $currency, $relatedTransaction = null);

    public function getPaymentStatus($paymentId, $payerId);

    public function payout($receiver, $value, $currency = 'USD', $recipientType = 'Email');
}