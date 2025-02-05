<?php

namespace App\Enum;

enum UserRole: string
{
    case ADMIN = 'admin';
    case CHEF = 'chef';
    case ORGANISATEUR = 'organisateur';
    case CLIENT = 'client';
}