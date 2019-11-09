<?php

namespace App\Model;

use Nette\Database\Context;
//use Nette\Http\Session;


class DBModel

{
        
    private $DB;
    //private $SE;
    
    public function __construct(Context $DB/*, Session $SE*/) {
        $this->DB = $DB;
        //$this->SE = $SE->getSection('Lipre');
    }
    
    /**
     * pristup k DB
     * @return type Database\Context
     */
    protected function DM_getDB() {
        //$this->DB->table('xxx')->valid()
        return $this->DB;
    }
    
    /**
     * pristup k Session
     * @return type Http\Session
     */
    /*public function DM_getSE() {
        return $this->SE;
    }*/
    
      
    
}