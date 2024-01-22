<?php

namespace Omnipay\Rabobank;

class PaymentBrand
{
    const STATUS_ACTIVE = 'Active';
    const STATUS_INACTIVE = 'Inactive';

    const NAME_IDEAL = 'IDEAL';
    const NAME_AFTERPAY = 'AFTERPAY';
    const NAME_PAYPAL = 'PAYPAL';
    const NAME_MASTERCARD = 'MASTERCARD';
    const NAME_VISA = 'VISA';
    const NAME_BANCONTACT = 'BANCONTACT';
    const NAME_MAESTRO = 'MAESTRO';
    const NAME_V_PAY = 'V_PAY';
    const NAME_SOFORT = 'SOFORT';
    /**
     * Brand name like IDEAL
     *
     * @var string
     */
    public $name;
    /**
     * Active status
     *
     * @var string
     */
    public $status;
    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }
}
