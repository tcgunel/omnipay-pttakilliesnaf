<?php

namespace Omnipay\PttAkilliEsnaf\Models;

class BinLookupResponseModel extends BaseModel
{
    public function __construct(?array $abstract)
    {
        parent::__construct($abstract);
    }

    public int $CardPrefix;

    public ?int $BankId;

    public ?string $BankCode;

    public ?string $BankName;

    public ?string $CardName;

    public ?string $CardClass;

    public ?string $CardType;

    public ?string $Country;

    public ?array $CommissionPackages;

    public int $Code;

    public string $Message;

    public function setCommissionPackages(?array $commissonPackages): void
    {
        foreach ($commissonPackages as $commissonPackage) {
            if (! empty($commissonPackage['InstallmentRate'])) {
                $rates = [
                    [
                        'Rate' => 0,
                        'Constant' => 0,
                        'Installment' => 1,
                    ],
                ];

                foreach ($commissonPackage['InstallmentRate'] as $key => $InstallmentRate) {
                    $rates[] = array_merge($InstallmentRate, ['Installment' => (int) str_replace('T', '', $key)]);
                }

                $this->CommissionPackages = $rates;
            }
        }
    }
}
