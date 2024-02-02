<?php

namespace Omnipay\PttAkilliEsnaf\Models;

class PaymentInquiryModel extends BaseModel
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

    public string $OrderId;

    public ?string $transactionId;

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

    public function setOrderId(string $OrderId): void
    {
        $this->OrderId = substr(trim($OrderId), 0, 20);
    }
}
