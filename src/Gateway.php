<?php

namespace Omnipay\PttAkilliEsnaf;

use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\PttAkilliEsnaf\Traits\PurchaseGettersSetters;

/**
 * PttAkilliEsnaf Gateway
 * (c) Tolga Can Günel
 * 2015, mobius.studio
 * http://www.github.com/tcgunel/omnipay-pttakilliesnaf
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
            "installment"     => "1",
            "secure"          => true,
            "currency"        => 'TRY',
            "description"     => '',
            "echo"            => '',
            "extraParameters" => '',

        ];
    }

    public function purchase(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\PttAkilliEsnaf\Message\PurchaseRequest', $parameters);
    }

    public function verifyEnrolment(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\PttAkilliEsnaf\Message\VerifyEnrolmentRequest', $parameters);
    }

    public function paymentInquiry(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\PttAkilliEsnaf\Message\PaymentInquiryRequest', $parameters);
    }
}
