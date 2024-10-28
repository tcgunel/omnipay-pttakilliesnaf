<?php

namespace Omnipay\PttAkilliEsnaf;

use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\PttAkilliEsnaf\Message\BinLookupRequest;
use Omnipay\PttAkilliEsnaf\Message\PaymentInquiryRequest;
use Omnipay\PttAkilliEsnaf\Message\PurchaseRequest;
use Omnipay\PttAkilliEsnaf\Message\VerifyEnrolmentRequest;
use Omnipay\PttAkilliEsnaf\Traits\PurchaseGettersSetters;

/**
 * PttAkilliEsnaf Gateway
 * (c) Tolga Can Günel
 * 2015, mobius.studio
 * http://www.github.com/tcgunel/omnipay-pttakilliesnaf
 *
 * @method \Omnipay\Common\Message\NotificationInterface acceptNotification(array $options = [])
 * @method \Omnipay\Common\Message\RequestInterface completeAuthorize(array $options = [])
 */
class Gateway extends AbstractGateway
{
    use PurchaseGettersSetters;

    public function getName(): string
    {
        return 'PttAkilliEsnaf';
    }

    public function getDefaultParameters()
    {
        return [
            'installment' => '1',
            'secure' => true,
            'currency' => 'TRY',
            'description' => '',
            'echo' => '',
            'extraParameters' => '',

        ];
    }

    public function purchase(array $options = []): AbstractRequest
    {
        return $this->createRequest(PurchaseRequest::class, $options);
    }

    public function verifyEnrolment(array $options = []): AbstractRequest
    {
        return $this->createRequest(VerifyEnrolmentRequest::class, $options);
    }

    public function paymentInquiry(array $options = []): AbstractRequest
    {
        return $this->createRequest(PaymentInquiryRequest::class, $options);
    }

    public function binLookup(array $options = []): AbstractRequest
    {
        return $this->createRequest(BinLookupRequest::class, $options);
    }
}
