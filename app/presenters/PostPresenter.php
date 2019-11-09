<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Model\PostModel;


final class PostPresenter extends Nette\Application\UI\Presenter
{
        private $pm;
        
        /**
	 * Hodnota aktualneho $postId.
	 * @persistent - proměnná se přenáší mezi HTTP požadavky
	 */
	public $postId = 0;
        
	public function __construct(PostModel $pm)
	{
                $this->pm = $pm;
	}


	public function renderShow(int $postId): void
	{
		$post = $this->pm->getPost_Id($postId);
		if (!$post) {
			$this->error('Post not found');
		}
                
                $this->postId = $postId;
		$this->template->post = $post;
		$this->template->comments = $this->pm->getRelated($post, 'comments');
                /*$this->template->showRating = $this->pm->showRating($postId);
                $this->template->userRating = $this->GetRating();*/
                $this->handleShowRating();
	}
        
        /*public function handleShow(): void
        {
            //$this->redrawControl();
        }*/
        
        public function handleShowRating(): void
        {
            $this->template->showRating = $this->pm->showRating($this->postId);
            $this->template->userRating = $this->GetRating();
        }
        
        
        public function handleSetRating(bool $rating, int $postID): void
        {   
            $param = Array(
                'post_id'=>$postID,
                'user_id'=>$this->getUser()->id,
                'rating'=>$rating);
            $this->pm->setRating($param);
            
            $this->redrawControl();
            $this->redrawControl('ShowRating');
        }
        
        public function GetRating()
        {
            if ($this->getUser()->isLoggedIn()) {
                $param = Array(
                    'post_id'=>$this->postId,
                    'user_id'=>$this->getUser()->id);
                return $this->pm->getRating($param);
            }
            return NULL;
        }
        
        
	protected function createComponentCommentForm(): Form
	{       
                $username = (isset($this->getUser()->getIdentity()->username)) ? 
                            $this->getUser()->getIdentity()->username : NULL;
                
		$form = new Form;
		
                if (!$username) {
                    $form->addText('name', 'Your name:')
                            ->setRequired()->setValue($username);

                    $form->addEmail('email', 'Email:');
                }
                
		$form->addTextArea('content', 'Comment:')
			->setRequired();
                
                $form->addSubmit('send', 'Publish comment');
		$form->onSuccess[] = [$this, 'commentFormSucceeded'];

		return $form;
	}


	public function commentFormSucceeded(Form $form, \stdClass $values): void
	{
		$this->pm->insertPostsTbl_Succeeded($this->getParameter('postId'), $values, $this->getUser()->id);

		$this->flashMessage('Thank you for your comment', 'success');
		$this->redirect('this');
	}


	public function actionCreate(): void
	{
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('Sign:in');
		}
	}


	public function actionEdit(int $postId): void
	{
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('Sign:in');
		}

		$post = $this->pm->getPost_Id($postId);
		if (!$post) {
			$this->error('Post not found');
		}
		$this['postForm']->setDefaults($post->toArray());
	}


	protected function createComponentPostForm(): Form
	{
		if (!$this->getUser()->isLoggedIn()) {
			$this->error('You need to log in to create or edit posts');
		}

		$form = new Form;
		$form->addText('title', 'Title:')
			->setRequired();
		$form->addTextArea('content', 'Content:')
			->setRequired();

		$form->addSubmit('send', 'Save and publish');
		$form->onSuccess[] = [$this, 'postFormSucceeded'];

		return $form;
	}


	public function postFormSucceeded(Form $form, array $values): void
	{
		$postId = $this->getParameter('postId');
                $values['created'] = $this->getUser()->getIdentity()->username;

		if ($postId) {
			$post = $this->pm->updatePostsTbl($postId, $values);
		} else {
			$post = $this->pm->insertPostsTbl($values);
                        $postId = $post->id;
		}

		$this->flashMessage('Post was published', 'success');
                $this->redirect('show', $postId);
	}
}
