<?php

namespace App\DTOs;

use Spatie\DataTransferObject\DataTransferObject;

class RegisterDTO extends DataTransferObject
{
    public string $email;
    public string $name;
    public ?string $birth_at;
    public string $password;
    public string $phone;
    public string $document;
}
