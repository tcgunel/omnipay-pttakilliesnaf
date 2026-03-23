<?php

namespace Omnipay\PttAkilliEsnaf\Tests\Feature;

use Omnipay\Common\Exception\InvalidCreditCardException;
use Omnipay\PttAkilliEsnaf\Helpers\Helper;
use Omnipay\PttAkilliEsnaf\Message\BinLookupRequest;
use Omnipay\PttAkilliEsnaf\Message\BinLookupResponse;
use Omnipay\PttAkilliEsnaf\Models\BinLookupRequestModel;
use Omnipay\PttAkilliEsnaf\Models\BinLookupResponseModel;
use Omnipay\PttAkilliEsnaf\Tests\TestCase;

class BinLookupTest extends TestCase
{
    /**
     * Test that getData builds the correct BinLookupRequestModel.
     *
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     * @throws InvalidCreditCardException
     * @throws \JsonException
     */
    public function test_bin_lookup_request()
    {
        $options = file_get_contents(__DIR__ . '/../Mock/BinLookupRequest.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new BinLookupRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize($options);

        $data = $request->getData();

        $this->assertInstanceOf(BinLookupRequestModel::class, $data);

        $this->assertEquals('10000000', $data->ClientId);
        $this->assertEquals('apiuser@test.com', $data->ApiUser);
        $this->assertEquals('545616', $data->Bin); // First 6 digits of card number

        // Hash should not be empty
        $this->assertNotEmpty($data->Hash);

        // Rnd and TimeSpan should be populated
        $this->assertNotEmpty($data->Rnd);
        $this->assertNotEmpty($data->TimeSpan);
    }

    /**
     * Test that the BIN is correctly truncated to 6 digits.
     */
    public function test_bin_lookup_request_bin_truncation()
    {
        $options = file_get_contents(__DIR__ . '/../Mock/BinLookupRequest.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new BinLookupRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize($options);

        $data = $request->getData();

        // Card number is 5456165456165454 => BIN should be 545616
        $this->assertEquals('545616', $data->Bin);
        $this->assertEquals(6, strlen($data->Bin));
    }

    /**
     * Test that the hash is correctly generated.
     */
    public function test_bin_lookup_hash_generation()
    {
        $options = file_get_contents(__DIR__ . '/../Mock/BinLookupRequest.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new BinLookupRequest($this->getHttpClient(), $this->getHttpRequest());

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
     * Test that getData throws InvalidCreditCardException for invalid card number.
     */
    public function test_bin_lookup_request_validation_error()
    {
        $options = file_get_contents(__DIR__ . '/../Mock/BinLookupRequest-ValidationError.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new BinLookupRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize($options);

        $this->expectException(InvalidCreditCardException::class);

        $request->getData();
    }

    /**
     * Test that BinLookupResponse isSuccessful returns true for Code=0.
     */
    public function test_bin_lookup_response_success()
    {
        $httpResponse = $this->getMockHttpResponse('BinLookupResponseSuccess.txt');

        $response = new BinLookupResponse($this->getMockRequest(), $httpResponse);

        $this->assertTrue($response->isSuccessful());

        $data = $response->getData();

        $this->assertInstanceOf(BinLookupResponseModel::class, $data);

        $this->assertEquals(0, $data->Code);
        $this->assertEquals('Success', $data->Message);
        $this->assertEquals(545616, $data->CardPrefix);
        $this->assertEquals(111, $data->BankId);
        $this->assertEquals('0111', $data->BankCode);
        $this->assertEquals('QNB Finansbank', $data->BankName);
        $this->assertEquals('CardFinans', $data->CardName);
        $this->assertEquals('Credit', $data->CardClass);
        $this->assertEquals('Visa', $data->CardType);
        $this->assertEquals('TR', $data->Country);
    }

    /**
     * Test commission packages are correctly parsed from the response.
     */
    public function test_bin_lookup_response_commission_packages()
    {
        $httpResponse = $this->getMockHttpResponse('BinLookupResponseSuccess.txt');

        $response = new BinLookupResponse($this->getMockRequest(), $httpResponse);

        $data = $response->getData();

        $this->assertNotEmpty($data->CommissionPackages);
        $this->assertIsArray($data->CommissionPackages);

        // First entry is the default (Rate 0, Installment 1)
        $this->assertEquals(0, $data->CommissionPackages[0]['Rate']);
        $this->assertEquals(1, $data->CommissionPackages[0]['Installment']);

        // Check installment rates extracted from T2, T3, T6
        $this->assertEquals(2, $data->CommissionPackages[1]['Installment']);
        $this->assertEquals(1.5, $data->CommissionPackages[1]['Rate']);

        $this->assertEquals(3, $data->CommissionPackages[2]['Installment']);
        $this->assertEquals(2.5, $data->CommissionPackages[2]['Rate']);

        $this->assertEquals(6, $data->CommissionPackages[3]['Installment']);
        $this->assertEquals(4.0, $data->CommissionPackages[3]['Rate']);
    }

    /**
     * Test that BinLookupResponse isSuccessful returns false for error responses.
     */
    public function test_bin_lookup_response_error()
    {
        $httpResponse = $this->getMockHttpResponse('BinLookupResponseError.txt');

        $response = new BinLookupResponse($this->getMockRequest(), $httpResponse);

        $this->assertFalse($response->isSuccessful());

        $this->assertEquals('BIN bulunamadi', $response->getMessage());
    }

    /**
     * Test sendData makes an HTTP request and returns BinLookupResponse.
     */
    public function test_bin_lookup_send_data()
    {
        $this->setMockHttpResponse('BinLookupResponseSuccess.txt');

        $options = file_get_contents(__DIR__ . '/../Mock/BinLookupRequest.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new BinLookupRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize($options);

        $data = $request->getData();

        $response = $request->sendData($data);

        $this->assertInstanceOf(BinLookupResponse::class, $response);

        $this->assertTrue($response->isSuccessful());
    }

    /**
     * Test that test mode switches to the preprod endpoint.
     */
    public function test_bin_lookup_test_mode_endpoint()
    {
        $options = file_get_contents(__DIR__ . '/../Mock/BinLookupRequest.json');

        $options = json_decode($options, true, 512, JSON_THROW_ON_ERROR);

        $request = new BinLookupRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->initialize($options);

        $request->getData();

        $this->assertEquals(
            'https://prepaeo.ptt.gov.tr/api/Payment/',
            $request->getEndpoint()
        );
    }
}
