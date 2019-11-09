<?php

namespace App\Model;

use Nette\Database\Context;


class HomepageModel extends DBModel

{
    public function __construct(Context $DB) {
        parent:: __construct($DB);
    }
    
    /**
     * Ziskat stranky
     * @param type $page cislo stranky, 5-pocet clankov na stranke
     * @return type
     */
    public function getPosts($page) {
        return $this->DM_getDB()->table('posts')
			->order('created_at DESC')
			->page($page, 5);
    }
       
}