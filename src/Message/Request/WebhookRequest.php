<?php

namespace Omnipay\Rabobank\Message\Request;

use Omnipay\Rabobank\Message\Response\WebhookResponse;

class WebhookRequest extends AbstractRabobankRequest
{

    /**
     * @inheritDoc
     */
    public function getData()
    {
        $this->validate('authentication', 'expiry', 'eventName', 'poiId');
        return $this->getData();
    }

    /**
     * @inheritDoc
     */
    public function sendData($data)
    {
        return new WebhookResponse($this, $this->parameters->all());
    }
}
