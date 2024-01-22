<?php

namespace Omnipay\Rabobank\Message\Response;

use Omnipay\Rabobank\Order;
use Omnipay\Rabobank\PaymentBrand;

class PaymentBrandsResponse extends AbstractRabobankResponse
{


    /**
     * @return PaymentBrand[]
     */
    public function getPaymentBrands()
    {
        $brands = [];
        foreach ((array)$this->data['paymentBrands'] as $brandResult) {
            $brand = new PaymentBrand();

            foreach ($brandResult as $field => $value) {
                $brand->{$field} = $value;
            }

            $brands[] = $brand;
        }

        return $brands;
    }

    public function getActivePaymentBrands()
    {
        return array_filter(
            $this->getPaymentBrands(),
            function ($brand) {
                return $brand->isActive();
            }
        );
    }
}
