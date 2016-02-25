<?php

namespace Application\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Crypt\Password\Bcrypt;
use Zend\Db\TableGateway\TableGateway;

/**
 * Class UserTable
 * @package CmsAdmin\Core
 */
class UserTable extends AbstractTableGateway
{
    protected $_tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->_tableGateway = $tableGateway;

        parent::__construct($tableGateway);
    }

    /**
     * Special GET for authentication use only.
     *
     * @param $email
     * @return ResultSet
     * @throws \Exception
     */
    public function fetchForAuth($email)
    {
        if (!is_string($email)) {
            throw new \Exception('Email for auth must be a string.');
        }

        $result = $this->_tableGateway->select(
            array(
                'email'   => $email
            )
        );

        return $result;
    }

    /**
     * @param int $length
     * @param bool $allowAmbiguity
     * @return string
     */
    public function generatePassword($length = 10, $allowAmbiguity = false)
    {
        $allowedChars = $allowAmbiguity === true
                      ? 'abcdefghijklmnopqrstuvwxyz0123456789'
                      : 'abcdefghkmnpqrstuvwxyz123456789';
        $maxRand = strlen($allowedChars);

        $password = '';

        for ($i = 0; $i < $length; $i++) {
            $pos = mt_rand(0, $maxRand-1);
            $headsTails = mt_rand(0, 10);

            $password .= $headsTails < 6
                      ? substr($allowedChars, $pos, 1)
                      : strtoupper(substr($allowedChars, $pos, 1));
        }

        return $password;
    }

    /**
     * @param $password
     * @return string
     */
    public function encryptPassword($password)
    {
        $bcrypt = new Bcrypt();

        return $bcrypt->create($password);
    }

    /**
     * @param $suppliedPassword
     * @param $storedPassword
     * @return mixed
     */
    public function verifyPassword($suppliedPassword, $storedPassword)
    {
        $bCrypt = new Bcrypt();
        return $bCrypt->verify($suppliedPassword, $storedPassword);
    }

    /**
     * @param $user
     * @return int
     * @throws \Exception
     */
    public function save($user)
    {
        if (!$user instanceof User) {
            throw new \Exception('Save method requires an instance of User to be passed as the only argument.');
        }

        $data = array(
            'firstName' => $user->firstName,
            'lastName'  => $user->lastName,
            'email'     => $user->email,
            'image'     => $user->image
        );

        if (!empty($user->role)) {
            $data['role'] = $user->role;
        }

        if (!empty($user->password)) {
            $data['password'] = $this->encryptPassword($user->password);
        }

        $id = empty($user->id)
            ? 0
            : (int) $user->id;

        if ($id === 0) {
            $this->_tableGateway->insert($data);
            return $this->_tableGateway->getLastInsertValue();
        } else {
            if ($this->get($id)) {
                $this->_tableGateway->update($data, array('id' => $id));
                return $id;
            } else {
                throw new \Exception("User with id $id does not exist");
            }
        }
    }
}