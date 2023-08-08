<?php

namespace Omnipay\PttAkilliEsnaf\Message;

use Omnipay\PttAkilliEsnaf\Models\VerifyEnrolmentRequestModel;
use Omnipay\PttAkilliEsnaf\Traits\PurchaseGettersSetters;

class VerifyEnrolmentRequest extends RemoteAbstractRequest
{
    use PurchaseGettersSetters;

    protected function validateAll()
    {
        $this->validate(
            'clientId',
            'apiUser',
            'apiPass',
            'transactionId',
            'mdStatus',
            'bankResponseCode',
            'bankResponseMessage',
            'requestStatus',
            'hashParameters',
        );
    }

    /**
     * @return VerifyEnrolmentRequestModel
     *
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     * @throws \Omnipay\Common\Exception\InvalidCreditCardException
     */
    public function getData()
    {
        $this->validateAll();

        return new VerifyEnrolmentRequestModel([
            'OrderId'             => $this->getTransactionId(),
            'ClientId'            => $this->getClientId(),
            'ApiUser'             => $this->getApiUser(),
            'ApiPass'             => $this->getApiPass(),
            'MdStatus'            => $this->getMdStatus(),
            'BankResponseCode'    => $this->getBankResponseCode(),
            'BankResponseMessage' => $this->getBankResponseMessage(),
            'RequestStatus'       => $this->getRequestStatus(),
            'HashParameters'      => $this->getHashParameters(),
            'Hash'                => $this->getHash(),
        ]);
    }

    protected function createResponse($data)
    {
        return $this->response = new VerifyEnrolmentResponse($this, $data);
    }

    /**
     * @param PurchaseRequestModel $data
     * @return VerifyEnrolmentResponse
     */
    public function sendData($data)
    {
        return $this->createResponse($data);
    }
}
