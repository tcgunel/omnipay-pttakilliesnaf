<?php

namespace Omnipay\PttAkilliEsnaf\Message;

use Omnipay\PttAkilliEsnaf\Exceptions\ThreedSessionIdException;
use Omnipay\PttAkilliEsnaf\Helpers\Helper;
use Omnipay\PttAkilliEsnaf\Models\ProcessCardFormModel;
use Omnipay\PttAkilliEsnaf\Models\ThreedStartModel;
use Omnipay\PttAkilliEsnaf\Models\ThreedStartResponseModel;
use Omnipay\PttAkilliEsnaf\Traits\PurchaseGettersSetters;

class PurchaseRequest extends RemoteAbstractRequest
{
    use PurchaseGettersSetters;

    private string $endpoint = 'https://aeo.ptt.gov.tr/api/Payment/';

    protected function validateAll()
    {
        $this->validate(
            'clientId',
            'apiUser',
            'apiPass',
            'returnUrl',
            'transactionId',
            'amount',
            'currency',
            'installment',

            'card',
        );
    }

    /**
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     * @throws \Omnipay\Common\Exception\InvalidCreditCardException
     */
    public function getData()
    {

        date_default_timezone_set('Europe/Istanbul');

        $this->validateAll();

        if ($this->getTestMode()) {

            $this->endpoint = 'https://prepaeo.ptt.gov.tr/api/Payment/';

        }

        $threed_start_model = new ThreedStartModel([
            'ClientId'         => $this->getClientId(),
            'ApiUser'          => $this->getApiUser(),
            'Rnd'              => str_shuffle(mt_rand(10000000, 99999999)),
            'TimeSpan'         => date('YmdHis'),
            'Hash'             => '',
            'CallbackUrl'      => $this->getReturnUrl(),
            'OrderId'          => $this->getTransactionId(),
            'Amount'           => $this->getAmount(),
            'Currency'         => $this->getCurrency(),
            'InstallmentCount' => $this->getInstallment(),
            'Description'      => $this->getDescription(),
            'Echo'             => $this->getEcho(),
            'ExtraParameters'  => $this->getExtraParameters(),
            'ThreeDSessionId'  => '',
        ]);

        $threed_start_model->setHash(
            Helper::generateHash(
                $this->getApiPass(),
                $threed_start_model->ClientId,
                $threed_start_model->ApiUser,
                $threed_start_model->Rnd,
                $threed_start_model->TimeSpan,
            )
        );

        $threed_start_model->setThreeDSessionId($this->getThreedSessionId($threed_start_model)->ThreeDSessionId);

        return $threed_start_model;
    }

    protected function createResponse($data)
    {
        return $this->response = new PurchaseResponse($this, $data);
    }

    /**
     * @param ThreedStartModel $data
     */
    public function sendData($data)
    {
        $process_card_form_model = new ProcessCardFormModel([
            'ThreeDSessionId' => $data->ThreeDSessionId,
            'CardHolderName' => $this->getCard()->getName(),
            'CardNo'         => $this->getCard()->getNumber(),
            'ExpireDate'     => $this->getCard()->getExpiryDate('m/y'),
            'Cvv'            => $this->getCard()->getCvv(),
        ]);

        return $this->createResponse($process_card_form_model);
    }

    private function getThreedSessionId(ThreedStartModel $threedStartModel): ThreedStartResponseModel
    {
        $httpResponse = $this->httpClient->request(
            'POST',
            $this->getEndpoint() . 'threeDPayment',
            [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ],
            json_encode($threedStartModel)
        );

        $response = json_decode($httpResponse->getBody(), true, 512, JSON_THROW_ON_ERROR);

        if ($httpResponse->getStatusCode() !== 200 || $response['Code'] !== 0) {

            throw new ThreedSessionIdException($response['Message'], $httpResponse->getStatusCode());

        }

        $response = new ThreedStartResponseModel($response);

        return $response;
    }
}
