<?php

namespace Omnipay\PttAkilliEsnaf\Tests\Feature;

use Omnipay\PttAkilliEsnaf\Message\BinLookupRequest;
use Omnipay\PttAkilliEsnaf\Message\PaymentInquiryRequest;
use Omnipay\PttAkilliEsnaf\Message\PurchaseRequest;
use Omnipay\PttAkilliEsnaf\Message\VerifyEnrolmentRequest;
use Omnipay\PttAkilliEsnaf\Tests\TestCase;

class GatewayTest extends TestCase
{
    public function test_gateway_name()
    {
        $this->assertEquals('PttAkilliEsnaf', $this->gateway->getName());
    }

    public function test_gateway_default_parameters()
    {
        $defaults = $this->gateway->getDefaultParameters();

        $this->assertArrayHasKey('installment', $defaults);
        $this->assertArrayHasKey('secure', $defaults);
        $this->assertArrayHasKey('description', $defaults);
        $this->assertArrayHasKey('echo', $defaults);
        $this->assertArrayHasKey('extraParameters', $defaults);

        $this->assertEquals('1', $defaults['installment']);
        $this->assertTrue($defaults['secure']);
    }

    public function test_gateway_purchase_returns_purchase_request()
    {
        $request = $this->gateway->purchase([]);

        $this->assertInstanceOf(PurchaseRequest::class, $request);
    }

    public function test_gateway_verify_enrolment_returns_verify_enrolment_request()
    {
        $request = $this->gateway->verifyEnrolment([]);

        $this->assertInstanceOf(VerifyEnrolmentRequest::class, $request);
    }

    public function test_gateway_payment_inquiry_returns_payment_inquiry_request()
    {
        $request = $this->gateway->paymentInquiry([]);

        $this->assertInstanceOf(PaymentInquiryRequest::class, $request);
    }

    public function test_gateway_bin_lookup_returns_bin_lookup_request()
    {
        $request = $this->gateway->binLookup([]);

        $this->assertInstanceOf(BinLookupRequest::class, $request);
    }

    public function test_gateway_getters_and_setters()
    {
        $this->gateway->setClientId('test-client-id');
        $this->assertEquals('test-client-id', $this->gateway->getClientId());

        $this->gateway->setApiUser('test-api-user');
        $this->assertEquals('test-api-user', $this->gateway->getApiUser());

        $this->gateway->setApiPass('test-api-pass');
        $this->assertEquals('test-api-pass', $this->gateway->getApiPass());

        $this->gateway->setDescription('test description');
        $this->assertEquals('test description', $this->gateway->getDescription());

        $this->gateway->setEcho('test echo');
        $this->assertEquals('test echo', $this->gateway->getEcho());

        $this->gateway->setSecure(false);
        $this->assertFalse($this->gateway->getSecure());

        $this->gateway->setInstallment(3);
        $this->assertEquals(3, $this->gateway->getInstallment());

        $this->gateway->setExtraParameters('extra=1');
        $this->assertEquals('extra=1', $this->gateway->getExtraParameters());
    }
}
