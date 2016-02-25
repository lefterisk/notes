<?php
namespace Application\Controller;

use Zend\View\Model\JsonModel;

class AuthController extends AbstractRestfulJsonController
{
    protected $availableMethods = array('OPTIONS','POST');

    public function options()
    {
        $this->response->setStatusCode(200);
        $this->response->getHeaders()->addHeaderLine('Allow', implode($this->availableMethods));
        $this->response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $data = array(
            'api' => 'cms-api',
            'authenticated' => $this->isAuthenticated()
        );

        $viewModel = new JsonModel($data);
        return $this->response->setContent($viewModel->serialize());
    }

    public function create($formData)
    {
//        if (!$this->isAuthorised()) {
//            return $this->notAuthenticatedResponse();
//        }

        $auth         = $this->getServiceLocator()->get('AuthService');
        $data         = array(
            'errors' => array()
        );

        $username   = (!empty($formData['username'])) ? $formData['username'] : null;
        $password   = (!empty($formData['password'])) ? $formData['password'] : null;
        $rememberMe = (!empty($formData['rememberMe'])) ? $formData['rememberMe'] : null;

        if (!$username) {
            $data['errors']['username'] = 'Username is a required field';
        }

        if (!$password) {
            $data['errors']['password'] = 'Password is a required field';
        }

        if (!empty($username) && !empty($password)) {
            $username = trim($username);
            $password = trim($password);
            $auth->getAdapter()->setIdentity($username);
            $auth->getAdapter()->setCredential($password);

            $result = $auth->authenticate();

            if ($result->isValid()) {
                if ($rememberMe === '1') {
                    $auth->getStorage()->setRememberMe(1, time() + (10 * 365 * 24 * 60 * 60));
                }
            } else {
                $data['errors'][] = 'Invalid credentials';
            }
        }

        return $this->_returnJsonViewModel($data);
    }
}
