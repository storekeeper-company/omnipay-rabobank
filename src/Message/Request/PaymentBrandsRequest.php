<?php

namespace Omnipay\Rabobank\Message\Request;

use Omnipay\Rabobank\Message\Response\StatusResponse;

class PaymentBrandsRequest extends AbstractRabobankRequest
{
    public function sendData($data)
    {
        $headers = [];
        $headers['Authorization'] = 'Bearer '.$this->getAccessToken();
        $response = $this->sendRequest(
            self::GET,
            'order/server/api/payment-brands',
            $data,
            $headers
        );

        return $this->response = new StatusResponse($this, $response);
    }

    public function getData()
    {
        return [];
    }
}
