<?php

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\Model\User\User;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette;
use Nette\Security\Passwords;
use Nette\Utils\ArrayHash;


class SignPresenter extends BaseAdminPresenter
{
	/** @var Passwords */
	private $passwords;
    private $user = null;

	/** @var Nette\Database\Explorer */
	//protected Nette\Database\Explorer $database;

	public function __construct(Passwords $passwords, Nette\Database\Explorer $database)
	{
		parent::__construct($database);
		$this->passwords = $passwords;
	}

    public function renderDetail(): void
    {
        $this->template->users = $this->database->table('users');
    }

	public function renderIn(): void
	{
		$this->template->baseLayout = __DIR__ . '/templates/@layoutClear.latte';
	}

	public function renderUp()
	{
		$this->template->baseLayout = __DIR__ . '/templates/@layoutClear.latte';
	}

	protected function createComponentSignInForm(): Form
	{
		$form = new Form;
		$form->addText('username', 'Uživatelské jméno:')
			 ->setHtmlAttribute('placeholder', 'Uživatelské jméno')
		     ->setRequired('Prosím vyplňte své uživatelské jméno.');

		$form->addPassword('password', 'Heslo:')
			 ->setHtmlAttribute('placeholder', 'Heslo')
		     ->setRequired('Prosím vyplňte své heslo.');

		$form->addCheckbox('remember', 'Zapamatovat si přihlášení');

		$form->addSubmit('send', 'Přihlásit');

		$form->onSuccess[] = [$this, 'signInFormSucceeded'];
		return $form;
	}

	public function signInFormSucceeded(Form $form, \stdClass $values): void
	{
		try {
			$this->getUser()->login($values->username, $values->password);
			if ($values->remember) {
				$this->getUser()->setExpiration("14 days");
			} else {
				$this->getUser()->setExpiration("20 minutes");
			}

			$this->redirect('Home:');

		} catch (Nette\Security\AuthenticationException $e) {
			$form->addError('Nesprávné přihlašovací jméno nebo heslo.');
		}
	}

	protected function createComponentSignUpForm(): Form
	{
		$form = new Form;
		$form->addText('username', 'Uživatelské jméno:')
		     ->setHtmlAttribute('placeholder', 'Uživatelské jméno')
		     ->setRequired('Prosím vyplňte své uživatelské jméno.');

		$form->addPassword('password', 'Heslo:')
		     ->setHtmlAttribute('placeholder', 'Heslo')
		     ->setRequired('Prosím vyplňte své heslo.')
		     ->addRule($form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaků', 8);

		$form->addPassword('passwordVerify', 'Heslo pro kontrolu:')
		     ->setHtmlAttribute('placeholder', 'Heslo pro kontrolu')
		     ->setRequired('Zadejte prosím heslo ještě jednou pro kontrolu')
		     ->addRule($form::EQUAL, 'Hesla se neshodují', $form['password'])
		     ->setOmitted();

		$form->addEmail('email', 'E-mail:')
		     ->setHtmlAttribute('placeholder', 'E-mail')
			->setRequired('Prosím vyplňte svůj email.');

		$form->addText('firstname', 'Křestní jméno:')
		     ->setHtmlAttribute('placeholder', 'Křestní jméno')
		     ->setRequired('Prosím vyplňte své křestní jméno.');

		$form->addText('lastname', 'Příjmení:')
		     ->setHtmlAttribute('placeholder', 'Příjmení')
		     ->setRequired('Prosím vyplňte své příjmení.');

		$form->onSuccess[] = [$this, 'signUpFormSucceeded'];

		return $form;
	}

	public function signUpFormSucceeded(Form $form, \stdClass $values): void
	{
		try {
			$this->database->table('users')->insert([
				'username' => $values->username,
				'password' => $this->passwords->hash($values->password),
				'email' => $values->email,
				'firstname' => $values->firstname,
				'lastname' => $values->lastname,
			]);

			$this->flashMessage('Registrace proběhla v pořádku.');
			$this->redirect('Home:');

		} catch (Nette\Database\UniqueConstraintViolationException $e) {
			$form->addError('Tento uživatel již existuje.');
		}
	}

    public function actionDetail(?int $id): void
    {
        if($id) {
            $this->user = $this->database->table('users')->get($id);

            if (!$this->user) {
                $this->error('Uživatel nebyl nalezen.');
            }
        }
    }

    protected function createComponentDetail()
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

        $form->onSuccess[] = [$this, 'onSuccess']; // onSuccess je název funkce, která to dokončí a uloží do db

        if($this->user) {
            $defaults = $this->user->toArray();
            unset($defaults['password']); // smazání hesla

            $form->setDefaults($defaults);
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


}