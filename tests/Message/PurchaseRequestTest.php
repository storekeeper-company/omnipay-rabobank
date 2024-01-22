<?php

namespace Omnipay\Rabobank\Message;

use Omnipay\Common\CreditCard;
use Omnipay\Rabobank\Exception\DescriptionToLongException;
use Omnipay\Rabobank\Exception\InvalidLanguageCodeException;
use Omnipay\Rabobank\Exception\InvalidSignatureException;
use Omnipay\Rabobank\Gateway;
use Omnipay\Rabobank\Message\Request\PurchaseRequest;
use Omnipay\Rabobank\Message\Response\PurchaseResponse;
use Omnipay\Tests\TestCase;

class PurchaseRequestTest extends TestCase
{
    /**
     * @var Gateway
     */
    protected $gateway;

    /**
     * @var PurchaseRequest
     */
    protected $request;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gateway = new Gateway();
        $this->gateway->setSigningKey(base64_encode('secret'));

        $this->request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest(), $this->gateway);
        $this->request->initialize(
            array(
                'refreshToken' => 'secret',
                'amount' => '10.00',
                'currency' => 'EUR',
                'returnUrl' => 'https://www.example.com/return',
                'orderId' => '1'
            )
        );
        $this->request->setAccessToken('secret');
    }

    public function testGetData(): void
    {
        $this->request->setPaymentMethod('IDEAL');
        $this->request->setOrderId('6');
        $this->request->setLanguageCode('EN');

        $card = new CreditCard([
            'firstName' => 'John',
            'lastName' => 'Doe',
            'address1' => 'Main street 123',
            'postcode' => '1234AA',
            'city' => 'Anytown',
            'country' => 'NL'
        ]);
        $this->request->setCard($card);

        $data = $this->request->getData();

        $this->assertRegExp('/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(Z|(\+|-)\d{2}(:?\d{2})?)$/', $data['timestamp']);
        $this->assertEquals('6', $data['merchantOrderId']);
        $this->assertEquals(array(
            'amount' => 1000,
            'currency' => 'EUR'
        ), $data['amount']);
        $this->assertEquals('EN', $data['language']);
        $this->assertEquals('', $data['description']);
        $this->assertEquals('https://www.example.com/return', $data['merchantReturnURL']);
        $this->assertEquals(array(
            'firstName' => 'John',
            'middleName' => '',
            'lastName' => 'Doe',
            'street' => 'Main street 123',
            'postalCode' => '1234AA',
            'city' => 'Anytown',
            'countryCode' => 'NL',
        ), $data['shippingDetail']);
        $this->assertEquals('IDEAL', $data['paymentBrand']);
        $this->assertEquals('FORCE_ONCE', $data['paymentBrandForce']);
    }

    public function testBaseUrl(): void
    {
        $this->gateway->setTestMode(false);
        $this->assertEquals('https://betalen.rabobank.nl/omnikassa-api/', $this->request->getBaseUrl());

        $this->gateway->setTestMode(true);
        $this->assertEquals('https://betalen.rabobank.nl/omnikassa-api-sandbox/', $this->request->getBaseUrl());

    }

    public function testDescription(): void
    {
        $this->request->setDescription('a description');
        $data = $this->request->getData();

        $this->assertEquals('a description', $data['description']);
    }

    public function testDescriptionToLong(): void
    {
        $this->expectException(DescriptionToLongException::class);
        $this->expectExceptionMessage('Description can only be 35 characters long');

        $this->request->setDescription('a very long description that is longer then 35 characters that is not allowed');
    }

    public function testLanguageCode(): void
    {
        $this->request->setLanguageCode('EN');
        $data = $this->request->getData();

        $this->assertEquals('EN', $data['language']);
    }

    public function testLanguageCodeInvalid(): void
    {
        $this->expectException(InvalidLanguageCodeException::class);
        $this->expectExceptionMessage('Language code must be a valid ISO 639-1 language code');

        $this->request->setLanguageCode('ENG');
    }

    public function testSendSuccess(): void
    {
        $this->setMockHttpResponse('PurchaseSuccess.txt');

        $this->request->setPaymentMethod('IDEAL');
        $this->request->setOrderId('6');

        /** @var PurchaseResponse $response */
        $response = $this->request->send();

        $this->assertInstanceOf(PurchaseResponse::class, $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertEquals('https://www.example.com/redirect', $response->getRedirectUrl());
    }

    public function testSendInvalidSignature(): void
    {
        $this->setMockHttpResponse('PurchaseInvalidSignature.txt');

        $this->request->setPaymentMethod('IDEAL');
        $this->request->setOrderId('6');

        $this->expectException(InvalidSignatureException::class);
        $this->expectExceptionMessage('Signature returned from server is invalid');

        $this->request->send();
    }
}
