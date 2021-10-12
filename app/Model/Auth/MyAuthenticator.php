<?php

declare(strict_types=1);

namespace App\Model\Auth;

use Nette;
use Nette\Security\SimpleIdentity;

class MyAuthenticator implements Nette\Security\Authenticator
{
	private Nette\Database\Explorer $database;
	private Nette\Security\Passwords $passwords;

	public function __construct(
		Nette\Database\Explorer $database,
		Nette\Security\Passwords $passwords
	) {
		$this->database = $database;
		$this->passwords = $passwords;
	}

	public function authenticate(string $username, string $password): SimpleIdentity
	{
		$row = $this->database->table('users')
		                      ->where('username', $username)
		                      ->fetch();

		if (!$row) {
			throw new Nette\Security\AuthenticationException('User not found.');
		}

		if (!$this->passwords->verify($password, $row->password)) {
			throw new Nette\Security\AuthenticationException('Invalid password.');
		}

		return new SimpleIdentity(
			$row->id,
			$row->role, // nebo pole více rolí
			[
				'username' => $row->username,
				'firstname' => $row->firstname,
				'lastname' => $row->lastname,
				'email' => $row->email,
			]
		);
	}
}
