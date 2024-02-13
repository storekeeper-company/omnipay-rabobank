<?php

namespace Omnipay\Rabobank\Message\Response;

use Omnipay\Rabobank\Message\Request\AbstractRabobankRequest;
use Omnipay\Rabobank\Order;

class WebhookResponse extends AbstractRabobankResponse
{
    public function getAuthenticationToten()
    {
        return (string) $this->data['authentication'];
    }
}
