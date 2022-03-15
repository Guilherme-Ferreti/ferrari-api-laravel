<?php

namespace App\DTOs;

use Spatie\DataTransferObject\DataTransferObject;

class StoreScheduleDTO extends DataTransferObject
{
    public string $time_option_id;
    public string $billing_address_id;
    public string $schedule_at;
    public int $installments;
    public array $services;
}