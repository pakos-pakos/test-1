<?php

namespace App\Model;

use Nette\Database\Context;
use Nette\Security\Passwords;


class SignModel extends DBModel

{
    public function __construct(Context $DB) {
        parent:: __construct($DB);
    }
    
    
    /**
     * Adds new user.
     * @param type $username
     * @param type $password
     * @throws DuplicateNameException
     */ 
    public function add($username, $password)
    {
            $passwords = new Passwords();
            try {
                    $this->DM_getDB()->table('users')->insert([
                            'username' => $username,
                            'password' => $passwords->hash($password),
                            'role' => 'user',
                    ]);
            } catch (Nette\Database\UniqueConstraintViolationException $e) {
                    throw new DuplicateNameException;
            }
    }
    
    
       
}