<?php

namespace Omnipay\Rabobank\Message\Response;

class WebhookResponse extends AbstractRabobankResponse
{

    public function getNotificationToken()
    {
        return (string) $this->data['authentication'];
    }
}
