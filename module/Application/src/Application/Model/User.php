<?php
namespace Application\Model;

/**
 * Class User
 * @package Application\Model
 */
class User extends AbstractModel
{
    public $id;
    public $image;
    public $firstName;
    public $lastName;
    public $email;
    public $password;

    /**
     * @return string
     */
    public function getName()
    {
        return ucwords($this->firstName) . ' '. ucwords($this->lastName);
    }
}