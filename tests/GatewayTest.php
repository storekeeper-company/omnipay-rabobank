<?php

namespace Omnipay\Rabobank;

use Omnipay\Rabobank\Message\Request\PurchaseRequest;
use Omnipay\Rabobank\Message\Request\StatusRequest;
use Omnipay\Rabobank\Message\Response\WebhookResponse;
use Omnipay\Tests\GatewayTestCase;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

class GatewayTest extends GatewayTestCase
{
    /**
     * @var Gateway
     */
    protected $gateway;

    public function setUp(): void
    {
        parent::setUp();

        $this->gateway = new Gateway();
        $this->gateway->setSigningKey(base64_encode('secret'));
    }

    public function testPurchase()
    {
        /** @var PurchaseRequest $request */
        $request = $this->gateway->purchase(array('amount' => '10.00', 'currency' => 'EUR'));

        $this->assertInstanceOf(PurchaseRequest::class, $request);
        $this->assertSame(1000, $request->getAmountInteger());
        $this->assertSame('EUR', $request->getCurrency());
    }

    public function testWebhookResponse()
    {
        $authToken = 'AUTH_NOTIFICATION_JWT_TOKEN';
        $webhook_data = [
            'authentication' => $authToken,
            'expiry' => date('c'),
            'eventName' => 'merchant.order.status.changed',
            'poiId' => 1,
        ];
        $webhook_data['signature'] = $this->gateway->generateSignature($webhook_data);

        $psrRequest = new HttpRequest([], [], [], [], [], [], json_encode($webhook_data));

        /** @var WebhookResponse $request */
        $response = $this->gateway->webhookResponse($psrRequest);

        $this->assertInstanceOf(WebhookResponse::class, $response);
        $this->assertSame($authToken, $response->getNotificationToken());
    }

    public function testStatus()
    {
        /** @var StatusRequest $request */
        $request = $this->gateway->status(array('notificationToken' => 'secret'));

        $this->assertInstanceOf(StatusRequest::class, $request);
        $this->assertSame('secret', $request->getNotificationToken());
    }

    public function testGenerateSignature()
    {
        $data = [
            date('c'),
            '6',
            'EUR',
            1000,
            'EN',
            '',
            'https://www.example.com/return',
            'IDEAL',
            'FORCE_ONCE'
        ];

        $expected = hash_hmac('sha512', implode(',', $data), 'secret');
        $this->assertSame($expected, $this->gateway->generateSignature($data));

        $data = [
            true,
            false,
            0,
            1,
            .1
        ];

        $signatureData = [
            'true',
            'false',
            '0',
            '1',
            '0.1'
        ];

        $expected = hash_hmac('sha512', implode(',', $signatureData), 'secret');
        $this->assertSame($expected, $this->gateway->generateSignature($data));
    }
}
