<?php

namespace Omnipay\Rabobank\Message\Request;

use Omnipay\Rabobank\Message\Response\WebhookResponse;

class WebhookRequest extends AbstractRabobankRequest
{
    protected $webhookData = [];
    public function initialize(array $parameters = array())
    {
        $this->webhookData = $parameters;
        return parent::initialize($parameters);
    }

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
        return new WebhookResponse($this, $this->webhookData);
    }
}
