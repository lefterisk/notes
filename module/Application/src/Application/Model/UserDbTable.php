<?php
namespace Application\Model;

use Zend\Authentication\Adapter\AbstractAdapter;
use Zend\Authentication\Result as AuthenticationResult;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UserDbTable extends AbstractAdapter implements ServiceLocatorAwareInterface
{
    public $dbAdapter;
    public $userTblGateway;

    protected $_resultRowObject;
    protected $_serviceLocator;

    /**
     * setServiceLocator
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->_serviceLocator = $serviceLocator;
    }

    /**
     * getServiceLocator
     */
    public function getServiceLocator()
    {
        return $this->_serviceLocator;
    }

    public function authenticate()
    {
        $this->userTblGateway = $this->getServiceLocator()->get('UserTable');

        if (!$this->userTblGateway) {
            throw new \Exception('No user table gateway available.');
        }

        $user = $this->userTblGateway->fetchForAuth($this->identity);

        $messages = array();

        if ($user->count() < 1) {
            $authResult = AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND;
            $messages[] = 'Sorry, you don\'t appear to be a registered user. Check your details and try again, or contact support@acknowledgement.co.uk';
            
            return new AuthenticationResult(
                $authResult,
                $this->identity,
                $messages
            );
        }

        $user = $user->current();

        if ($this->userTblGateway->verifyPassword($this->credential, $user->password) === true) {
            $authResult = AuthenticationResult::SUCCESS;
        } else {
            $authResult = AuthenticationResult::FAILURE_CREDENTIAL_INVALID;
            $messages[] = 'Your email and password combination is incorrect.';
        }

        unset(
            $user->password
        );

        $this->_setResultRowObject($user);

        return new AuthenticationResult(
            $authResult,
            $user,
            $messages
        );
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function refreshUser()
    {
        $this->userTblGateway = $this->getServiceLocator()->get('UserTable');

        if (!$this->userTblGateway) {
            throw new \Exception('No user table gateway available.');
        }

        $user = $this->userTblGateway->fetchForAuth($this->identity);

        if ($user->count() < 1) {
            return false;
        }

        $user = $user->current();

        unset(
            $user->password
        );

        $this->_setResultRowObject($user);

        return $user;
    }

    /**
     * @param $resultRowObject
     */
    protected function _setResultRowObject($resultRowObject)
    {
        $this->_resultRowObject = $resultRowObject;
    }

    /**
     * @return mixed
     */
    public function getResultRowObject()
    {
        return $this->_resultRowObject;
    }
}