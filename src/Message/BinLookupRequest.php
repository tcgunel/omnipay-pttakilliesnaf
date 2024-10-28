<?php

namespace Omnipay\PttAkilliEsnaf\Message;

use Omnipay\Common\Exception\InvalidCreditCardException;
use Omnipay\PttAkilliEsnaf\Helpers\Helper;
use Omnipay\PttAkilliEsnaf\Models\BinLookupRequestModel;
use Omnipay\PttAkilliEsnaf\Traits\PurchaseGettersSetters;

class BinLookupRequest extends RemoteAbstractRequest
{
    use PurchaseGettersSetters;

    private string $endpoint = 'https://aeo.ptt.gov.tr/api/Payment/';

    /**
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     * @throws InvalidCreditCardException
     */
    public function getData()
    {
        date_default_timezone_set('Europe/Istanbul');

        $this->validateAll();

        if ($this->getTestMode()) {

            $this->endpoint = 'https://prepaeo.ptt.gov.tr/api/Payment/';

        }

        $model = new BinLookupRequestModel([
            'ClientId' => $this->getClientId(),
            'ApiUser' => $this->getApiUser(),
            'Rnd' => str_shuffle(mt_rand(10000000, 99999999)),
            'TimeSpan' => date('YmdHis'),
            'Hash' => '',
            'Bin' => $this->getCard()->getNumber(),
        ]);

        $model->setHash(
            Helper::generateHash(
                $this->getApiPass(),
                $model->ClientId,
                $model->ApiUser,
                $model->Rnd,
                $model->TimeSpan,
            )
        );

        return $model;
    }

    /**
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    protected function validateAll(): void
    {
        $this->validate(
            'clientId',
            'apiUser',
            'apiPass',
        );

        if (! is_null($this->getCard()->getNumber()) && ! preg_match('/^\d{8,19}$/', $this->getCard()->getNumber())) {
            throw new InvalidCreditCardException('Card number should have at least 6 to maximum of 19 digits');
        }
    }

    /**
     * @throws \JsonException
     */
    protected function createResponse($data): BinLookupResponse
    {
        return $this->response = new BinLookupResponse($this, $data);
    }

    public function sendData($data)
    {
        $httpResponse = $this->httpClient->request(
            'POST',
            $this->getEndpoint().'GetCommissionAndInstallmentInfo',
            [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            json_encode($data)
        );

        return $this->createResponse($httpResponse);
    }
}
