<?php

declare(strict_types=1);


namespace App\AdminModule\Presenters;


use App\Model\User\User;
use Nette;
use Latte;
use Nette\Application\UI\Form;
use Nette\Security\Passwords;
use Nette\Utils\ArrayHash;


final class UserPresenter extends BaseAdminPresenter
{
	private $user;

	public function actionDefault (): void
	{
		$this->template->usersList = $this->database->table('users')->fetchAll();
	}

	public function actionEdit(?int $id): void
	{
		$idList = [1, 4];

		if(in_array($id, $idList))
		{
			throw new \RuntimeException('Tento uživatel nelze upravit. Je to admin.');
		}

		if($id) {
			$this->user = $this->database->table('users')->get($id);

			if (!$this->user) {
				$this->error('Uživatel nebyl nalezen.');
			}
		}
	}

	protected function createComponentEdit()
	{
		$form = new Form;

		$form->addText('id', 'ID')
		     ->setHtmlAttribute('readonly', true);

		$form->addText('username', 'Uživatelské jméno')
		     ->setRequired('Toto pole je vyžadováno.');

		$form->addText('firstname', 'Jméno')
		     ->setRequired('Toto pole je vyžadováno.');

		$form->addText('lastname', 'Příjmení')
		     ->setRequired('Toto pole je vyžadováno.');

		$form->addEmail('email', 'Email')
		     ->setRequired('Toto pole je vyžadováno.');

        $form->addPassword('password', 'Heslo:')
            ->setHtmlAttribute('placeholder', 'Heslo')
            ->addRule($form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaků', 8);

		$form->addSelect('role', 'Uživatelská role', User::ROLES)
			 ->setRequired('Toto pole je vyžadováno.');

		$form->onSuccess[] = [$this, 'onSuccess'];

		if($this->user) {
			$form->setDefaults($this->user->toArray());
		}

		return $form;
	}

	public function onSuccess(Form $form, ArrayHash $values): void
	{
		$postId = $values->id;

        $password = new Passwords();

        if($values->password) {
            $values->password = $password->hash($values->password);
        } else {
            unset($values->password); // odstranit klíč z formuláře, aby to do db nevložilo prázdný string
        }

		if ($postId) {
			$userEntity = $this->database->table('users')->get($postId);
			$userEntity->update($values);
		} else {
			$userEntity = $this->database->table('users')->insert($values);
		}

		$this->flashMessage('Uživatel byl upraven.', 'success');
		$this->redirect('User:');
	}

	public function handleDelete(int $id): void
	{
		$idList = [1, 4];

		if(in_array($id, $idList))
		{
			throw new \RuntimeException('Tento uživatel nelze smazat. Je to admin.');
		}

		if (!($this->database->table('users')->get($id))) {
			throw new \RuntimeException('Uživatel nenalezen.');
		}

		if($this->database->table('users')->where('id', $id)->delete()) {
			$this->flashMessage('Smazání uživatele proběhlo v pořádku.');
			$this->redirect('this');
		}
	}
}