<?php
namespace Application\Model;

use Zend\Authentication\Storage;
use Zend\Session\SessionManager;

class AuthStorage extends Storage\Session
{
    /**
     * 
     * @param string $namespace
     * @param string $member
     * @param SessionManager $manager
     */
    public function __construct($namespace = null, $member = null, SessionManager $manager = null)
    {
        parent::__construct($namespace, $member, $manager);
    }
    
    public function setRememberMe($rememberMe = 0, $time = 1209600)
    {
         if ($rememberMe == 1) {
             $this->session->getManager()->rememberMe($time);
         }
    }

    public function forgetMe()
    {
        $this->session->getManager()->forgetMe();
    }
}