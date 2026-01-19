<?php

namespace App\Enums;

enum UserType: string
{
    case SuperAdmin = 'super_admin';
    case User = 'user';
    
    public static function usernameLength(UserType $type): string
    {
        return match ($type) {
            self::SuperAdmin => 'super_admin',
            self::User => 'user',
            
        };
    }

    public static function childUserType(UserType $type): UserType
    {
        return match ($type) {
            self::SuperAdmin => self::User,
            self::User => self::User,
        };
    }

    public function getDisplayName(): string
    {
        return match ($this) {
            self::SuperAdmin => 'SuperAdmin',
            self::User => 'User',
            
        };
    }

    public function getRoleName(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::User => 'User',
        };
    }

    public function getUsername(): string
    {
        return match ($this) {
            self::SuperAdmin => 'superadmin',
            self::User => 'user',
        };
    }

    public function getPhone(): string
    {
        return match ($this) {
            self::SuperAdmin => '09123456789',
            self::User => '09123456797',
        };
    }
}

