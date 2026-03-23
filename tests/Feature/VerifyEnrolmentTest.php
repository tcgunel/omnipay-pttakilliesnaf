<?php

namespace Omnipay\PttAkilliEsnaf\Tests\Feature;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\PttAkilliEsnaf\Exceptions\HashValidationException;
use Omnipay\PttAkilliEsnaf\Message\VerifyEnrolmentRequest;
use Omnipay\PttAkilliEsnaf\Message\VerifyEnrolmentResponse;
use Omnipay\PttAkilliEsnaf\Models\VerifyEnrolmentRequestModel;
use Omnipay\PttAkilliEsnaf\Tests\TestCase;

class VerifyEnrolmentTest extends TestCase
{
    /**
     * Test that getData builds the correct VerifyEnrolmentRequestModel.
     *
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     * @throws \Omnipay\Common\Exception\InvalidCreditCardException
     * @throws \JsonException
     */
    public function test_verify_enrolment_request()
    {
        $options = file_get_contents(__DIR__ . '/../Mock/VerifyEnrolmentRequest.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new VerifyEnrolmentRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize($options);

        $data = $request->getData();

        $expected = new VerifyEnrolmentRequestModel([
            'OrderId' => 'PTT-ORD-20240101001',
            'ClientId' => '10000000',
            'ApiUser' => 'apiuser@test.com',
            'ApiPass' => 'testApiPass123',
            'MdStatus' => '1',
            'BankResponseCode' => '00',
            'BankResponseMessage' => 'Onaylandi',
            'RequestStatus' => '1',
            'HashParameters' => 'ClientId,OrderId',
            'Hash' => '',
        ]);

        self::assertEquals($expected, $data);
    }

    /**
     * Test that getData throws InvalidRequestException when required fields are missing.
     */
    public function test_verify_enrolment_request_validation_error()
    {
        $options = file_get_contents(__DIR__ . '/../Mock/VerifyEnrolmentRequest-ValidationError.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new VerifyEnrolmentRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize($options);

        $this->expectException(InvalidRequestException::class);

        $request->getData();
    }

    /**
     * Test that VerifyEnrolmentResponse isSuccessful returns true for BankResponseCode=00 with valid hash.
     */
    public function test_verify_enrolment_response_success()
    {
        $apiPass = 'testApiPass123';
        $clientId = '10000000';
        $orderId = 'PTT-ORD-20240101001';

        // Build the hash matching the HashParameters (ClientId,OrderId)
        $hashString = $apiPass . $clientId . $orderId;
        $hash = base64_encode(hash('sha512', $hashString, true));

        $model = new VerifyEnrolmentRequestModel([
            'OrderId' => $orderId,
            'ClientId' => $clientId,
            'ApiUser' => 'apiuser@test.com',
            'ApiPass' => $apiPass,
            'MdStatus' => '1',
            'BankResponseCode' => '00',
            'BankResponseMessage' => 'Onaylandi',
            'RequestStatus' => '1',
            'HashParameters' => 'ClientId,OrderId',
            'Hash' => $hash,
        ]);

        $response = new VerifyEnrolmentResponse($this->getMockRequest(), $model);

        $this->assertTrue($response->isSuccessful());

        $this->assertSame('Onaylandi', $response->getMessage());
    }

    /**
     * Test that VerifyEnrolmentResponse isSuccessful returns false for non-00 BankResponseCode.
     */
    public function test_verify_enrolment_response_bank_error()
    {
        $model = new VerifyEnrolmentRequestModel([
            'OrderId' => 'PTT-ORD-20240101001',
            'ClientId' => '10000000',
            'ApiUser' => 'apiuser@test.com',
            'ApiPass' => 'testApiPass123',
            'MdStatus' => '0',
            'BankResponseCode' => '99',
            'BankResponseMessage' => 'Islem reddedildi',
            'RequestStatus' => '0',
            'HashParameters' => 'ClientId,OrderId',
            'Hash' => '',
        ]);

        $response = new VerifyEnrolmentResponse($this->getMockRequest(), $model);

        $this->assertFalse($response->isSuccessful());

        $this->assertSame('Islem reddedildi', $response->getMessage());
    }

    /**
     * Test that VerifyEnrolmentResponse throws HashValidationException for invalid hash.
     */
    public function test_verify_enrolment_response_hash_validation_error()
    {
        $model = new VerifyEnrolmentRequestModel([
            'OrderId' => 'PTT-ORD-20240101001',
            'ClientId' => '10000000',
            'ApiUser' => 'apiuser@test.com',
            'ApiPass' => 'testApiPass123',
            'MdStatus' => '1',
            'BankResponseCode' => '00',
            'BankResponseMessage' => 'Onaylandi',
            'RequestStatus' => '1',
            'HashParameters' => 'ClientId,OrderId',
            'Hash' => 'invalidhash123',
        ]);

        $this->expectException(HashValidationException::class);

        $response = new VerifyEnrolmentResponse($this->getMockRequest(), $model);

        $response->isSuccessful();
    }

    /**
     * Test that sendData returns a VerifyEnrolmentResponse (no HTTP call, just passes data through).
     */
    public function test_verify_enrolment_send_data()
    {
        $options = file_get_contents(__DIR__ . '/../Mock/VerifyEnrolmentRequest.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new VerifyEnrolmentRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize($options);

        $data = $request->getData();

        $response = $request->sendData($data);

        $this->assertInstanceOf(VerifyEnrolmentResponse::class, $response);
    }
}
