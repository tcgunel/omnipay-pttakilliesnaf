<?php

namespace Omnipay\PttAkilliEsnaf\Message;

use JsonException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\PttAkilliEsnaf\Models\BinLookupResponseModel;
use Omnipay\PttAkilliEsnaf\Models\ProcessCardFormModel;
use Psr\Http\Message\ResponseInterface;

class BinLookupResponse extends AbstractResponse
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

                $this->response = new BinLookupResponseModel(json_decode($body, true, 512, JSON_THROW_ON_ERROR));

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
        return $this->response->Code === 0;
    }

    public function getMessage(): string
    {
        return $this->response->Message;
    }

    public function getData(): BinLookupResponseModel
    {
        return $this->response;
    }
}
