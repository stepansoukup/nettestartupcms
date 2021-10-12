<?php

declare(strict_types=1);


namespace App\Model\User;


class User
{
    public const ROLE_USER = 'user';
	public const ROLE_EDITOR = 'editor';
	public const ROLE_ADMIN = 'admin';

	public const ROLES = [
        self::ROLE_USER => 'User',
		self::ROLE_EDITOR => 'Redaktor',
		self::ROLE_ADMIN => 'Administrator'
	];
}