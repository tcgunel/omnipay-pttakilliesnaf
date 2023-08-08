<?php

namespace Omnipay\PttAkilliEsnaf\Models;

class VerifyEnrolmentRequestModel extends BaseModel
{
    public function __construct(?array $abstract)
    {
        parent::__construct($abstract);
    }

    public string $OrderId;

    public string $ClientId;

    public string $ApiUser;

    public string $ApiPass;

    public string $MdStatus;

    public string $BankResponseCode;

    public string $BankResponseMessage;

    public string $RequestStatus;

    public string $HashParameters;

    public string $Hash;
}
