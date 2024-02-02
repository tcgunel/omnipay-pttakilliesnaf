<?php

namespace Omnipay\PttAkilliEsnaf\Message;

use Omnipay\Common\Exception\InvalidCreditCardException;
use Omnipay\PttAkilliEsnaf\Helpers\Helper;
use Omnipay\PttAkilliEsnaf\Models\PaymentInquiryModel;
use Omnipay\PttAkilliEsnaf\Models\RequestHeadersModel;
use Omnipay\PttAkilliEsnaf\Models\ThreedStartModel;
use Omnipay\PttAkilliEsnaf\Traits\PurchaseGettersSetters;

class PaymentInquiryRequest extends RemoteAbstractRequest
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

        $payment_inquiry_model = new PaymentInquiryModel([
            'ClientId'         => $this->getClientId(),
            'ApiUser'          => $this->getApiUser(),
            'Rnd'              => str_shuffle(mt_rand(10000000, 99999999)),
            'TimeSpan'         => date('YmdHis'),
            'Hash'             => '',
            'OrderId'          => $this->getTransactionId()
        ]);

        $payment_inquiry_model->setHash(
            Helper::generateHash(
                $this->getApiPass(),
                $payment_inquiry_model->ClientId,
                $payment_inquiry_model->ApiUser,
                $payment_inquiry_model->Rnd,
                $payment_inquiry_model->TimeSpan,
            )
        );

        return $payment_inquiry_model;
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
            'transactionId',
        );
    }

    /**
     * @throws \JsonException
     */
    protected function createResponse($data): PaymentInquiryResponse
    {
        return $this->response = new PaymentInquiryResponse($this, $data);
    }

    public function sendData($data)
    {
        $httpResponse = $this->httpClient->request(
            'POST',
            $this->getEndpoint() . 'inquiry',
            [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            json_encode($data)
        );

        return $this->createResponse($httpResponse);
    }
}
