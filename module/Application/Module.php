<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Application\Model\AuthStorage;
use Application\Model\Note;
use Application\Model\NoteTable;
use Application\Model\User;
use Application\Model\UserDbTable;
use Application\Model\UserTable;
use Zend\Authentication\AuthenticationService;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Zend\Session\Container;
use Zend\Session\SaveHandler\DbTableGateway;
use Zend\Session\SaveHandler\DbTableGatewayOptions;
use Zend\Session\SessionManager;
use Zend\View\Model\JsonModel;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        $app  = $e->getApplication();
        $sm   = $app->getServiceManager();
        $auth = $sm->get('AuthService');

        $eventManager->attach(MvcEvent::EVENT_ROUTE, function($e) use ( $auth, $sm) {

            $match = $e->getRouteMatch();

            // 404 returns here with missing RouteMatch
            if (!$match instanceof RouteMatch) {
                return;
            }

            $name = $match->getMatchedRouteName();

            // User is already authenticated and has an identity
            if ($auth->hasIdentity()) {
                return;
            }

            // if this isn't a API route then return - we don't want to try and handle XHR requests en masse
            if (preg_match('/^api?[^\w-]/', $name) == 0) return;

            // if it's an AJAX request give some info to the caller about next steps
            if ($e->getApplication()->getRequest()->isXmlHttpRequest()) {
                $response = $e->getResponse();
                $response->setStatusCode(401);
                $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
                $response->setContent('{"success" : false, "redirect" : true, "redirectUrl" : "/login"}');
                return $response;
            }
        }, -100);
    }

    public function onDispatchError($e)
    {
        return $this->getJsonModelError($e);
    }
    public function onRenderError($e)
    {
        return $this->getJsonModelError($e);
    }
    public function getJsonModelError($e)
    {
        $error = $e->getError();
        if (!$error) {
            return;
        }

        $response      = $e->getResponse();
        $exception     = $e->getParam('exception');
        $exceptionJson = array();

        if ($exception) {
            $exceptionJson = array(
                'class' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'message' => $exception->getMessage(),
                'stacktrace' => $exception->getTraceAsString()
            );
        }

        $errorJson = array(
            'message'   => 'An error occurred during execution; please try again later.',
            'error'     => $error,
            'exception' => $exceptionJson,
        );

        if ($error == 'error-router-no-match') {
            $errorJson['message'] = 'Resource not found.';
        }

        $model = new JsonModel(array('errors' => array($errorJson)));
        $e->setResult($model);
        return $model;
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'NoteTable' =>  function($sm) {
                    $tableGateway = $sm->get('NoteTableGateway');
                    return new NoteTable($tableGateway);
                },
                'NoteTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Note());
                    return new TableGateway('note', $dbAdapter, null, $resultSetPrototype);
                },
                'UserTable' =>  function($sm) {
                    $tableGateway = $sm->get('UserTableGateway');
                    return new UserTable($tableGateway);
                },
                'UserTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new User());
                    return new TableGateway('user', $dbAdapter, null, $resultSetPrototype);
                },
                'SessionSaveHandler' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $tableGateway = new TableGateway('session', $dbAdapter);
                    $saveHandler  = new DbTableGateway($tableGateway, new DbTableGatewayOptions());
                    return $saveHandler;
                },
                'Zend\Session\SessionManager' => function ($sm) {
                    $config = $sm->get('config');

                    if (isset($config['session'])) {
                        $session = $config['session'];

                        $sessionConfig = NULL;

                        if (isset($session['config'])) {
                            $class         = isset($session['config']['class'])
                                ? $session['config']['class']
                                : 'Zend\Session\Config\SessionConfig';
                            $options       = isset($session['config']['options'])
                                ? $session['config']['options']
                                : array();
                            $sessionConfig = new $class();
                            $sessionConfig->setOptions($options);
                        }

                        $sessionStorage = NULL;

                        if (isset($session['storage'])) {
                            $class          = $session['storage'];
                            $sessionStorage = new $class();
                        }

                        $sessionSaveHandler = NULL;

                        if (isset($session['save_handler'])) {
                            // class should be fetched from service manager since it will require constructor arguments
                            $sessionSaveHandler = $sm->get($session['save_handler']);
                        }

                        $sessionManager = new SessionManager($sessionConfig, $sessionStorage, $sessionSaveHandler);
                        $sessionManager->regenerateId = true;

                        if (isset($session['validator'])) {
                            $chain = $sessionManager->getValidatorChain();
                            foreach ($session['validator'] as $validator) {
                                $validator = new $validator();
                                $chain->attach('session.validate', array($validator, 'isValid'));
                            }
                        }
                    } else {
                        $sessionManager = new SessionManager();
                    }

                    Container::setDefaultManager($sessionManager);
                    return $sessionManager;
                },
                'AuthStorage' => function($sm) {
                    $sessionManager = $sm->get('Zend\Session\SessionManager');
                    $authStorage = new AuthStorage('user', NULL, $sessionManager);
                    return $authStorage;
                },
                'AuthService' => function($sm) {
                    $dbTableAuthAdapter = new UserDbTable();
                    $dbTableAuthAdapter->setServiceLocator($sm);

                    $storage = $sm->get('AuthStorage');
                    $authService = new AuthenticationService($storage, $dbTableAuthAdapter);

                    return $authService;
                }
            ),
        );
    }
}
