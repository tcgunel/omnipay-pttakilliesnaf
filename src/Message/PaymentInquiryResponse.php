<?php

namespace Omnipay\PttAkilliEsnaf\Message;

use JsonException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\PttAkilliEsnaf\Models\ProcessCardFormModel;
use Psr\Http\Message\ResponseInterface;

class PaymentInquiryResponse extends AbstractResponse
{
    protected $response;

    protected $request;

    /**
     * @param RequestInterface $request
     * @param ProcessCardFormModel $data
     */
    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, $data);

        $this->request = $request;

        $this->response = $data;

        if ($data instanceof ResponseInterface) {

            $body = (string)$data->getBody();

            try {

                $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

                $data['rawResult'] = preg_replace('/\n+/', '', $body);

                $this->response = $data;

            } catch (JsonException $e) {

                $this->response = [
                    'Message' => $e->getMessage(),
                    'data' => $body
                ];

            }
        }
    }

    public function isSuccessful(): bool
    {
        return $this->response['BankResponseCode'] === '00';
    }

    public function getData(): bool
    {
        return $this->response;
    }
}
