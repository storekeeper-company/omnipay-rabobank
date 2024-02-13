<?php

namespace Omnipay\Rabobank\Message\Request;

use Omnipay\Rabobank\Exception\RequestCannotBeSentException;

class WebhookRequest extends AbstractRabobankRequest
{
    /**
     * @inheritDoc
     */
    public function getData()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function sendData($data)
    {
        throw new RequestCannotBeSentException('This request cannot be sent, only used internally');
    }
}
