<?php
namespace Application\Controller;

use Zend\Debug\Debug;
use Zend\Json\Decoder;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\Http\Response;
use Zend\View\Model\JsonModel;
use Zend\EventManager\EventManagerInterface;

/**
 * Class AbstractRestfulJsonController
 * @package Application\Controller
 */
class AbstractRestfulJsonController extends AbstractRestfulController
{

    protected $_loggedInUser;
    protected $_hasLoggedInUser = false;

    /**
     * @return bool
     */
    public function isAuthorised()
    {
        if ($this->_hasLoggedInUser && is_object($this->_loggedInUser)) {
            return true;
        }

        return false;
    }

    /**
     * @param EventManagerInterface $events
     * @return void
     */
    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);

        $controller = $this;
        $auth = $this->getServiceLocator()->get('AuthService');

        $events->attach('dispatch', function ($e) use ($controller, $auth) {
            if ($auth->hasIdentity()) {
                $controller->_loggedInUser    = $auth->getIdentity();
                $controller->_hasLoggedInUser = true;
            }
        }, 100);
    }

    protected function notAuthenticatedResponse($message = null)
    {
        $content= array();
        $this->response->setStatusCode(401);
        if (!empty($message)) {
            if (is_string($message)) {
                $content['content'] = $message;
            } elseif (is_array($message)) {
                $content = $message;
            }
        } else {
            $content['content'] = 'You are not authorised to use this resource';
        }
        return new JsonModel($content);
    }

    /**
     * @param null $message
     * @return JsonModel
     */
    protected function errorResponse($message = null)
    {
        $content= array();
        $this->response->setStatusCode(500);
        if (!empty($message)) {
            if (is_string($message)) {
                $content['content'] = $message;
            } elseif (is_array($message)) {
                $content = $message;
            }
        } else {
            $content['content'] = 'There has been a serious application error';
        }
        return new JsonModel($content);
    }

    /**
     * Pass in $content array with type, title & detail key => values. you can also pass in a further associative array
     * in the content array with a key of additionalFields.
     *
     * See http://tools.ietf.org/html/draft-nottingham-http-problem-06 for more info.
     *
     * @param $content
     * @param int $statusCode HTTP status code
     * @return JsonModel
     */
    protected function problemResponse($content, $statusCode = 403)
    {
        $this->response->setStatusCode($statusCode);
        $this->response->getHeaders()->addHeaderLine('Content-Type', 'application/problem+json');

        $responseContent = array(
            'type'   => !empty($content['type']) ? $content['type'] : 'Type explanation URL undefined.',
            'title'  => !empty($content['title']) ? $content['title'] : 'An application problem occurred.',
            'detail' => !empty($content['detail']) ? $content['detail'] : 'No details supplied.',
            'errors' => $this->getErrors(),
        );

        if (!empty($content['additionalFields']) && is_array($content['additionalFields'])) {
            foreach ($content['additionalFields'] AS $additionalKey => $additionalField) {
                $responseContent[$additionalKey] = $additionalField;
            }
        }

        return new JsonModel($responseContent);
    }

    protected function methodNotAllowed($content)
    {
        $this->response->setStatusCode(405);
        return new JsonModel($content);
    }

    # Override default actions as they do not return valid JsonModels
    public function create($data)
    {
        $responseContent = parent::create($data);
        return $this->methodNotAllowed($responseContent);
    }

    public function delete($id)
    {
        $responseContent = parent::delete($id);
        return $this->methodNotAllowed($responseContent);
    }

    public function deleteList($data)
    {
        $responseContent = parent::deleteList($data);
        return $this->methodNotAllowed($responseContent);
    }

    public function get($id)
    {
        $responseContent = parent::get($id);
        return $this->methodNotAllowed($responseContent);
    }

    public function getList()
    {
        $responseContent = parent::getList();
        return $this->methodNotAllowed($responseContent);
    }

    public function head($id = null)
    {
        $responseContent = parent::head($id);
        return $this->methodNotAllowed($responseContent);
    }

    public function options()
    {
        $responseContent = parent::options();
        return $this->methodNotAllowed($responseContent);
    }

    public function patch($id, $data)
    {
        $responseContent = parent::patch($id, $data);
        return $this->methodNotAllowed($responseContent);
    }

    public function replaceList($data)
    {
        $responseContent = parent::replaceList($data);
        return $this->methodNotAllowed($responseContent);
    }

    public function patchList($data)
    {
        $responseContent = parent::patchList($data);
        return $this->methodNotAllowed($responseContent);
    }

    public function update($id, $data)
    {
        $responseContent = parent::update($id, $data);
        return $this->methodNotAllowed($responseContent);
    }

    /**
     * Decodes the request payload and JSON decodes them into an associative array
     *
     * @return array
     */
    protected function _getParamsFromJsonRequestPayload()
    {
        try {
            return Decoder::decode($this->getRequest()->getContent(), Json::TYPE_ARRAY);
        } catch (\Exception $e) {
            return array();
        }
    }

    /**
     * @param array $data
     * @return JsonModel
     */
    protected function _returnJsonViewModel($data = array())
    {
        return new JsonModel($data);
    }
}