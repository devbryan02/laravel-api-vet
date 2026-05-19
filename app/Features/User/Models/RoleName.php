<?php

namespace App\Features\User\Models;

enum RoleName: string
{
    case ADMIN = 'ADMIN';
    case VETERINARIAN = 'VETERINARIAN';
    case OWNER = 'OWNER';
}
