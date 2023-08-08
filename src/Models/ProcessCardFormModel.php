<?php

namespace Omnipay\PttAkilliEsnaf\Models;

class ProcessCardFormModel extends BaseModel
{
    public function __construct(?array $abstract)
    {
        parent::__construct($abstract);
    }

    public string $ThreeDSessionId;
    public string $CardHolderName;
    public string $CardNo;
    public string $ExpireDate;
    public string $Cvv;

}


