<?php

namespace Omnipay\PttAkilliEsnaf\Models;

use Omnipay\PttAkilliEsnaf\Constants\Currency;

class ThreedStartModel extends BaseModel
{
    public function __construct(?array $abstract)
    {
        parent::__construct($abstract);
    }

    /**
     * @required
     */
    public string $ClientId;

    /**
     * @required
     */
    public string $ApiUser;

    /**
     * @required
     */
    public string $Rnd;

    /**
     * @required
     */
    public string $TimeSpan;

    /**
     * @required
     */
    public string $Hash;

    /**
     * @required
     */
    public string $CallbackUrl;

    public string $OrderId;

    /**
     * @required
     */
    public string $Amount;

    /**
     * @required
     */
    public int $Currency;

    /**
     * @required
     */
    public int $InstallmentCount;

    /**
     * @required
     */
    public string $ThreeDSessionId;

    public string $Description = '';

    public string $Echo = '';

    public string $ExtraParameters = '';

    public function setClientId(string $ClientId): void
    {
        $this->ClientId = substr(trim($ClientId), 0, 20);
    }

    public function setApiUser(string $ApiUser): void
    {
        $this->ApiUser = substr(trim($ApiUser), 0, 100);
    }

    public function setRnd(string $Rnd): void
    {
        $this->Rnd = substr(trim($Rnd), 0, 24);
    }

    public function setTimeSpan(string $TimeSpan): void
    {
        $this->TimeSpan = substr(trim($TimeSpan), 0, 14);
    }

    public function setHash(string $Hash): void
    {
        $this->Hash = substr(trim($Hash), 0, 512);
    }

    public function setCallbackUrl(string $CallbackUrl): void
    {
        $this->CallbackUrl = substr(trim($CallbackUrl), 0, 1024);
    }

    public function setOrderId(string $OrderId): void
    {
        $this->OrderId = substr(trim($OrderId), 0, 20);
    }

    public function setAmount(string $Amount): void
    {
        $this->Amount = $Amount * 100;
    }

    public function setCurrency(string $Currency): void
    {
        $this->Currency = Currency::TRY;
    }

    public function setInstallmentCount(int $InstallmentCount): void
    {
        $this->InstallmentCount = min($InstallmentCount, 12);

        if ($this->InstallmentCount === 1){

            $this->InstallmentCount = 0;

        }
    }

    public function setDescription(string $Description): void
    {
        $this->Description = substr(trim($Description), 0, 256);
    }

    public function setEcho(string $Echo): void
    {
        $this->Echo = substr(trim($Echo), 0, 256);
    }

    public function setExtraParameters(string $ExtraParameters): void
    {
        $this->ExtraParameters = substr(trim($ExtraParameters), 0, 4000);
    }

    public function setThreeDSessionId(string $ThreeDSessionId): void
    {
        $this->ThreeDSessionId = $ThreeDSessionId;
    }
}
