<?php

namespace App\Model;

use Nette\Database\Context;


class PostModel extends DBModel

{
    public function __construct(Context $DB) {
        parent:: __construct($DB);
    }
    
    public function getPost_Id($postId) {
        return $this->DM_getDB()->table('posts')->get($postId);
    }
    
    public function getRelated($post, $comment) {
        return $post->related($comment)->order('created_at');
    }
    
    
    public function insertPostsTbl_Succeeded($post_id, $values, $userID) {
        
        if (!\array_key_exists('name', $values)) {
            $user = $this->DM_getDB()->table('users')->get($userID);
            $values->name = $user->username;
            $values->email = $user->email;
        }
        
        $this->DM_getDB()->table('comments')->insert([
			'post_id' => $post_id,
			'name' => $values->name,
			'email' => $values->email,
			'content' => $values->content,
		]);
    }
    
    public function insertPostsTbl($values) {
        return $this->DM_getDB()->table('posts')->insert($values);
    }
    
    public function updatePostsTbl($postId, $values) {
        return $this->getPost_Id($postId)->update($values);
    }
    
    public function showRating($post_id) {
        $rating = $this->DM_getDB()->table('ratings')->where('post_id = ?', $post_id);
        $return[1] = count($rating->where('rating = ?', 1));
        $return[0] = count($rating->where('rating = ?', 0));
        return $return;
    }
    
    
    public function setRating($param) {
        $val = $this->DM_getDB()->table('ratings')
                ->where('post_id = ?', $param['post_id'])
                ->where('user_id = ?', $param['user_id']);
        (count($val)==1) ?
                $val->update($param) :
                $this->DM_getDB()->table('ratings')->insert($param);
    }
    
    public function getRating($param) {
        $return = $this->DM_getDB()->table('ratings')
                ->where('post_id = ?', $param['post_id'])
                ->where('user_id = ?', $param['user_id'])->fetch();
        if ($return) {
            if ($return->rating == 1) {return 'positive';} else {return 'negative';}
        } else {return NULL;}
    }
    
       
}