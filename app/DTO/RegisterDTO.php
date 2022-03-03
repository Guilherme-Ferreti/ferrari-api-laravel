<?php

namespace App\DTO;

use Spatie\DataTransferObject\Attributes\MapFrom;
use Spatie\DataTransferObject\DataTransferObject;

class RegisterDTO extends DataTransferObject
{
    public string $email;
    public string $name;
    public string $birth_at;
    public string $password;
    public string $phone;
    public string $document;
}
