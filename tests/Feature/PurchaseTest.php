<?php

namespace Omnipay\PttAkilliEsnaf\Tests\Feature;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\PttAkilliEsnaf\Constants\Currency;
use Omnipay\PttAkilliEsnaf\Exceptions\ThreedSessionIdException;
use Omnipay\PttAkilliEsnaf\Helpers\Helper;
use Omnipay\PttAkilliEsnaf\Message\PurchaseRequest;
use Omnipay\PttAkilliEsnaf\Message\PurchaseResponse;
use Omnipay\PttAkilliEsnaf\Models\ProcessCardFormModel;
use Omnipay\PttAkilliEsnaf\Models\ThreedStartModel;
use Omnipay\PttAkilliEsnaf\Tests\TestCase;

class PurchaseTest extends TestCase
{
    /**
     * Test that getData builds the correct ThreedStartModel and calls the ThreeD session API.
     *
     * PurchaseRequest::getData() internally calls httpClient->request() to obtain
     * a ThreeDSessionId. We mock the HTTP client to return a successful response.
     *
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     * @throws \Omnipay\Common\Exception\InvalidCreditCardException
     * @throws \JsonException
     */
    public function test_purchase_request()
    {
        $this->setMockHttpResponse('ThreedStartResponseSuccess.txt');

        $options = file_get_contents(__DIR__ . '/../Mock/PurchaseRequest.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize($options);

        $data = $request->getData();

        $this->assertInstanceOf(ThreedStartModel::class, $data);

        $this->assertEquals('10000000', $data->ClientId);
        $this->assertEquals('apiuser@test.com', $data->ApiUser);
        $this->assertEquals('https://example.com/payment/callback', $data->CallbackUrl);
        $this->assertEquals('PTT-ORD-20240101001', $data->OrderId);
        $this->assertEquals(15075, $data->Amount); // 150.75 * 100
        $this->assertEquals(Currency::TRY, $data->Currency);
        $this->assertEquals(0, $data->InstallmentCount); // installment 1 becomes 0
        $this->assertEquals('Test odeme', $data->Description);
        $this->assertEquals('a1b2c3d4-e5f6-7890-abcd-ef1234567890', $data->ThreeDSessionId);

        // Hash should not be empty
        $this->assertNotEmpty($data->Hash);

        // Rnd and TimeSpan should be populated
        $this->assertNotEmpty($data->Rnd);
        $this->assertNotEmpty($data->TimeSpan);
    }

    /**
     * Test that getData correctly generates hash via Helper::generateHash.
     */
    public function test_purchase_request_hash_generation()
    {
        $this->setMockHttpResponse('ThreedStartResponseSuccess.txt');

        $options = file_get_contents(__DIR__ . '/../Mock/PurchaseRequest.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

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
     * Test that getData throws InvalidRequestException when required fields are missing.
     */
    public function test_purchase_request_validation_error()
    {
        $options = file_get_contents(__DIR__ . '/../Mock/PurchaseRequest-ValidationError.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize($options);

        $this->expectException(InvalidRequestException::class);

        $request->getData();
    }

    /**
     * Test that getData throws ThreedSessionIdException when the 3D API returns an error.
     */
    public function test_purchase_request_threed_session_id_error()
    {
        $this->setMockHttpResponse('ThreedStartResponseError.txt');

        $options = file_get_contents(__DIR__ . '/../Mock/PurchaseRequest.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize($options);

        $this->expectException(ThreedSessionIdException::class);

        $request->getData();
    }

    /**
     * Test that sendData returns a PurchaseResponse with ProcessCardFormModel data.
     */
    public function test_purchase_response()
    {
        $this->setMockHttpResponse('ThreedStartResponseSuccess.txt');

        $options = file_get_contents(__DIR__ . '/../Mock/PurchaseRequest.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

        /** @var PurchaseResponse $response */
        $response = $request->initialize($options)->send();

        $this->assertInstanceOf(PurchaseResponse::class, $response);

        $this->assertFalse($response->isSuccessful());

        $this->assertTrue($response->isRedirect());

        $redirectData = $response->getRedirectData();

        $this->assertIsArray($redirectData);

        $this->assertArrayHasKey('ThreeDSessionId', $redirectData);
        $this->assertArrayHasKey('CardHolderName', $redirectData);
        $this->assertArrayHasKey('CardNo', $redirectData);
        $this->assertArrayHasKey('ExpireDate', $redirectData);
        $this->assertArrayHasKey('Cvv', $redirectData);

        $this->assertEquals('a1b2c3d4-e5f6-7890-abcd-ef1234567890', $redirectData['ThreeDSessionId']);
        $this->assertEquals('Ahmet Yilmaz', $redirectData['CardHolderName']);
        $this->assertEquals('5456165456165454', $redirectData['CardNo']);
        $this->assertEquals('123', $redirectData['Cvv']);
    }

    /**
     * Test redirect URL uses the test endpoint when testMode is enabled.
     */
    public function test_purchase_response_redirect_url_test_mode()
    {
        $this->setMockHttpResponse('ThreedStartResponseSuccess.txt');

        $options = file_get_contents(__DIR__ . '/../Mock/PurchaseRequest.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

        /** @var PurchaseResponse $response */
        $response = $request->initialize($options)->send();

        $this->assertEquals(
            'https://prepaeo.ptt.gov.tr/api/Payment/ProcessCardForm',
            $response->getRedirectUrl()
        );
    }

    /**
     * Test redirect method is POST.
     */
    public function test_purchase_response_redirect_method()
    {
        $this->setMockHttpResponse('ThreedStartResponseSuccess.txt');

        $options = file_get_contents(__DIR__ . '/../Mock/PurchaseRequest.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

        /** @var PurchaseResponse $response */
        $response = $request->initialize($options)->send();

        $this->assertEquals('POST', $response->getRedirectMethod());
    }

    /**
     * Test that installment count greater than 1 but <= 12 is kept.
     */
    public function test_purchase_request_installment_count()
    {
        $this->setMockHttpResponse('ThreedStartResponseSuccess.txt');

        $options = file_get_contents(__DIR__ . '/../Mock/PurchaseRequest.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $options['installment'] = 6;

        $request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize($options);

        $data = $request->getData();

        $this->assertEquals(6, $data->InstallmentCount);
    }

    /**
     * Test that installment count above 12 is capped to 12.
     */
    public function test_purchase_request_installment_count_max()
    {
        $this->setMockHttpResponse('ThreedStartResponseSuccess.txt');

        $options = file_get_contents(__DIR__ . '/../Mock/PurchaseRequest.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $options['installment'] = 24;

        $request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize($options);

        $data = $request->getData();

        $this->assertEquals(12, $data->InstallmentCount);
    }
}
