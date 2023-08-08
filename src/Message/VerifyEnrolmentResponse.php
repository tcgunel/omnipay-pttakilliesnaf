<?php

namespace Omnipay\PttAkilliEsnaf\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\PttAkilliEsnaf\Exceptions\HashValidationException;
use Omnipay\PttAkilliEsnaf\Models\VerifyEnrolmentRequestModel;

class VerifyEnrolmentResponse extends AbstractResponse
{
    /** @var VerifyEnrolmentRequestModel $data */
    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, $data);

        $this->data = $data;
    }

    public function isSuccessful(): bool
    {
        return $this->data->BankResponseCode === '00' && $this->hashValidation();
    }

    public function getMessage()
    {
        return $this->data->BankResponseMessage;
    }

    private function hashValidation(): bool
    {
        $keys = explode(',', $this->data->HashParameters);

        $hashString = $this->data->ApiPass;

        foreach ($keys as $key) {
            $hashString .= $this->data->$key;
        }

        if ($this->data->Hash !== base64_encode(hash('sha512', $hashString, true))){

            throw new HashValidationException('Hash validation error.', 401);

        }

        return true;
    }
}
