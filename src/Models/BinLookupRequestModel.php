<?php

namespace Omnipay\PttAkilliEsnaf\Models;

class BinLookupRequestModel extends BaseModel
{
    public function __construct(?array $abstract)
    {
        parent::__construct($abstract);
    }

    public string $ClientId;
    public string $ApiUser;
    public string $Rnd;
    public string $TimeSpan;
    public string $Hash;
    public string $Bin;

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

    public function setBin(string $cc_number): void
    {
        $this->Bin = substr(trim($cc_number), 0, 6);
    }
}
