<?php

namespace Omnipay\PttAkilliEsnaf\Tests\Feature;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\PttAkilliEsnaf\Helpers\Helper;
use Omnipay\PttAkilliEsnaf\Message\PaymentInquiryRequest;
use Omnipay\PttAkilliEsnaf\Message\PaymentInquiryResponse;
use Omnipay\PttAkilliEsnaf\Models\PaymentInquiryModel;
use Omnipay\PttAkilliEsnaf\Tests\TestCase;

class PaymentInquiryTest extends TestCase
{
    /**
     * Test that getData builds the correct PaymentInquiryModel.
     *
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     * @throws \Omnipay\Common\Exception\InvalidCreditCardException
     * @throws \JsonException
     */
    public function test_payment_inquiry_request()
    {
        $options = file_get_contents(__DIR__ . '/../Mock/PaymentInquiryRequest.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new PaymentInquiryRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize($options);

        $data = $request->getData();

        $this->assertInstanceOf(PaymentInquiryModel::class, $data);

        $this->assertEquals('10000000', $data->ClientId);
        $this->assertEquals('apiuser@test.com', $data->ApiUser);
        $this->assertEquals('PTT-ORD-20240101001', $data->OrderId);

        // Hash should not be empty
        $this->assertNotEmpty($data->Hash);

        // Rnd and TimeSpan should be populated
        $this->assertNotEmpty($data->Rnd);
        $this->assertNotEmpty($data->TimeSpan);
    }

    /**
     * Test that the hash is correctly generated.
     */
    public function test_payment_inquiry_hash_generation()
    {
        $options = file_get_contents(__DIR__ . '/../Mock/PaymentInquiryRequest.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new PaymentInquiryRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize($options);

        $data = $request->getData();

        $expectedHash = Helper::generateHash(
            'testApiPass123',
            $data->ClientId,
            $data->ApiUser,
            $data->Rnd,
            $data->TimeSpan
        );

        $this->assertEquals($expectedHash, $data->Hash);
    }

    /**
     * Test that getData throws InvalidRequestException when transactionId is missing.
     */
    public function test_payment_inquiry_request_validation_error()
    {
        $options = file_get_contents(__DIR__ . '/../Mock/PaymentInquiryRequest-ValidationError.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new PaymentInquiryRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize($options);

        $this->expectException(InvalidRequestException::class);

        $request->getData();
    }

    /**
     * Test that PaymentInquiryResponse isSuccessful returns true for BankResponseCode=00.
     */
    public function test_payment_inquiry_response_success()
    {
        $httpResponse = $this->getMockHttpResponse('PaymentInquiryResponseSuccess.txt');

        $response = new PaymentInquiryResponse($this->getMockRequest(), $httpResponse);

        $this->assertTrue($response->isSuccessful());

        $data = $response->getData();

        $this->assertEquals('00', $data['BankResponseCode']);
        $this->assertEquals('Onaylandi', $data['BankResponseMessage']);
        $this->assertEquals('PTT-ORD-20240101001', $data['OrderId']);
    }

    /**
     * Test that PaymentInquiryResponse isSuccessful returns false for error responses.
     */
    public function test_payment_inquiry_response_error()
    {
        $httpResponse = $this->getMockHttpResponse('PaymentInquiryResponseError.txt');

        $response = new PaymentInquiryResponse($this->getMockRequest(), $httpResponse);

        $this->assertFalse($response->isSuccessful());

        $data = $response->getData();

        $this->assertEquals('99', $data['BankResponseCode']);
        $this->assertEquals('Islem bulunamadi', $data['BankResponseMessage']);
    }

    /**
     * Test sendData makes an HTTP request and returns PaymentInquiryResponse.
     */
    public function test_payment_inquiry_send_data()
    {
        $this->setMockHttpResponse('PaymentInquiryResponseSuccess.txt');

        $options = file_get_contents(__DIR__ . '/../Mock/PaymentInquiryRequest.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new PaymentInquiryRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize($options);

        $data = $request->getData();

        $response = $request->sendData($data);

        $this->assertInstanceOf(PaymentInquiryResponse::class, $response);

        $this->assertTrue($response->isSuccessful());
    }

    /**
     * Test that test mode switches to the preprod endpoint.
     */
    public function test_payment_inquiry_test_mode_endpoint()
    {
        $options = file_get_contents(__DIR__ . '/../Mock/PaymentInquiryRequest.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new PaymentInquiryRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize($options);

        $request->getData();

        $this->assertEquals(
            'https://prepaeo.ptt.gov.tr/api/Payment/',
            $request->getEndpoint()
        );
    }
}
