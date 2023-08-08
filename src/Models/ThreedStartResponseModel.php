<?php

namespace Omnipay\PttAkilliEsnaf\Models;

class ThreedStartResponseModel extends BaseModel
{
    public function __construct(?array $abstract)
    {
        parent::__construct($abstract);
    }

    public int $Code;

    public string $Message;

    public string $ThreeDSessionId;

    public string $TransactionId;
}
