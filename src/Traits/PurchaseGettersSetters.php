<?php

namespace Omnipay\PttAkilliEsnaf\Traits;

trait PurchaseGettersSetters
{
    public function setClientId($value)
    {
        return $this->setParameter('clientId', $value);
    }

	public function getClientId()
	{
		return $this->getParameter('clientId');
	}

    public function setApiUser($value)
    {
        return $this->setParameter('apiUser', $value);
    }

	public function getApiUser()
	{
		return $this->getParameter('apiUser');
	}


    public function setApiPass($value)
    {
        return $this->setParameter('apiPass', $value);
    }

	public function getApiPass()
	{
		return $this->getParameter('apiPass');
	}

    public function setDescription($value)
    {
        return $this->setParameter('description', $value);
    }

	public function getDescription()
	{
		return $this->getParameter('description');
	}

    public function setEcho($value)
    {
        return $this->setParameter('echo', $value);
    }

	public function getEcho()
	{
		return $this->getParameter('echo');
	}

    public function setSecure($value)
    {
        return $this->setParameter('secure', $value);
    }

	public function getSecure()
	{
		return $this->getParameter('secure');
	}

    public function getEndpoint()
    {
        return $this->endpoint;
    }

    public function setInstallment($value)
    {
        return $this->setParameter('installment', $value);
    }

    public function getInstallment()
    {
        return $this->getParameter('installment');
    }

    public function getExtraParameters()
    {
        return $this->getParameter('extraParameters');
    }

    public function setExtraParameters($value)
    {
        return $this->setParameter('extraParameters', $value);
    }

    public function getHash()
    {
        return $this->getParameter('hash');
    }

    public function setHash($value)
    {
        return $this->setParameter('hash', $value);
    }

    public function getMdStatus()
    {
        return $this->getParameter('mdStatus');
    }

    public function setMdStatus($value)
    {
        return $this->setParameter('mdStatus', $value);
    }

    public function getBankResponseCode()
    {
        return $this->getParameter('bankResponseCode');
    }

    public function setBankResponseCode($value)
    {
        return $this->setParameter('bankResponseCode', $value);
    }

    public function getBankResponseMessage()
    {
        return $this->getParameter('bankResponseMessage');
    }

    public function setBankResponseMessage($value)
    {
        return $this->setParameter('bankResponseMessage', $value);
    }

    public function getRequestStatus()
    {
        return $this->getParameter('requestStatus');
    }

    public function setRequestStatus($value)
    {
        return $this->setParameter('requestStatus', $value);
    }

    public function getHashParameters()
    {
        return $this->getParameter('hashParameters');
    }

    public function setHashParameters($value)
    {
        return $this->setParameter('hashParameters', $value);
    }
}
