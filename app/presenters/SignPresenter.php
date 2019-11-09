<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Model\SignModel;


final class SignPresenter extends Nette\Application\UI\Presenter
{       
        
        private $sm;

	public function __construct(SignModel $sm)
	{
                $this->sm = $sm;
	}
        
        
	/**
	 * Sign-in form factory.
	 */
	protected function createComponentSignInForm(): Form
	{
		$form = new Form;
		$form->addText('username', 'Username:')
			->setRequired('Please enter your username.');

		$form->addPassword('password', 'Password:')
			->setRequired('Please enter your password.');

		$form->addSubmit('send', 'Sign in');
                
		// call method signInFormSucceeded() on success
		$form->onSuccess[] = [$this, 'signInFormSucceeded'];
		return $form;
	}


	public function signInFormSucceeded(Form $form, \stdClass $values): void
	{       
		try {
			$this->getUser()->login($values->username, $values->password);
                        $this->redirect('Homepage:');
                        /*if (!$this->getPresenter()->isAjax())
                        {
                            $this->redirect('Homepage:');
                        }
                        else {
                            $this->redrawControl();
                        }*/
                        
			

		} catch (Nette\Security\AuthenticationException $e) {
			$form->addError('Incorrect username or password.');
		}
	}


	public function actionOut(): void
	{
		$this->getUser()->logout();
                $this->getUser()->getIdentity()->username = NULL;
		$this->flashMessage('You have been signed out.');
		$this->redirect('Homepage:');
	}
        
        /**
	 * Sign-add form factory.
	 */
	protected function createComponentSignAddForm(): Form
	{
		$form = new Form;
		$form->addText('username', 'Username:')
			->setRequired('Please enter your username.');

		$form->addPassword('password', 'Password:')
			->setRequired('Please enter your password.');

		$form->addSubmit('send', 'Registration');

		// call method signInFormSucceeded() on success
		$form->onSuccess[] = [$this, 'signAddFormSucceeded'];
		return $form;
	}


	public function signAddFormSucceeded(Form $form, \stdClass $values): void
	{       
		try {
			$this->sm->add($values->username, $values->password);
                        $this->getUser()->login($values->username, $values->password);
                        $this->redirect('Homepage:');
                            
                } catch (Nette\Security\AuthenticationException $e) {
			$form->addError('Registration not complete.');
		}
	}
}
