<?php

namespace Omnipay\Rabobank\Message\Response;

use Omnipay\Rabobank\Message\Request\AbstractRabobankRequest;
use Omnipay\Rabobank\Message\Request\WebhookRequest;
use Omnipay\Rabobank\Order;

class WebhookResponse extends AbstractRabobankResponse
{

    public function getAuthenticationToken()
    {
        return (string) $this->data['authentication'];
    }
}
