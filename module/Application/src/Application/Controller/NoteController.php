<?php
namespace Application\Controller;

use Application\Model\Note;
use Zend\View\Model\JsonModel;

class NoteController extends AbstractRestfulJsonController
{
    protected $availableMethods = array('OPTIONS','POST','GET', 'GETLIST');

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
        if (!$this->isAuthorised()) {
            return $this->notAuthenticatedResponse();
        }

        $data  = array(
            'errors' => array()
        );

        $title = (!empty($formData['title'])) ? $formData['title'] : null;
        $text  = (!empty($formData['text'])) ? $formData['text'] : null;

        if (!$title) {
            $data['errors']['title'] = 'Title is a required field';
        }

        if (!$text) {
            $data['errors']['text'] = 'Text is a required field';
        }

        if (empty($data['errors'])) {
            $newNote = new Note();
            $newNote->exchangeArray(array(
                'title'    => $title,
                'text'     => $text,
                'user'     => $this->_loggedInUser->id,
                'modified' => date("Y-m-d H:i:s"),
                'created'  => date("Y-m-d H:i:s"),
            ));
            $noteTable = $this->getServiceLocator()->get('NoteTable');
            $noteId    = $noteTable->save($newNote);
        }

        return $this->_returnJsonViewModel($data);
    }

    public function getList()
    {
        if (!$this->isAuthorised()) {
            return $this->notAuthenticatedResponse();
        }

        $page           = (int) $this->params()->fromRoute('page',1);
        $itemsPerPage   = (int) $this->params()->fromRoute('items-per-page', 10);
        $noteTable      = $this->getServiceLocator()->get('NoteTable');
        $results        = $noteTable->fetchForListing(array(), new Note(), $page, $itemsPerPage, 'created', 'DESC');


        $data = array(
            'items' => \Zend\Stdlib\ArrayUtils::iteratorToArray($results),
            'pagination' => array(
                'totalItemCount' => $results->getTotalItemCount(),
                'currentPage'    => $results->getCurrentPageNumber(),
                'pageCount'      => $results->count(),
                'itemsInPage'    => $results->getItemCountPerPage(),
            ),
            'errors' => array()
        );

        return $this->_returnJsonViewModel($data);
    }

    public function get($id)
    {
        if (!$this->isAuthorised()) {
            return $this->notAuthenticatedResponse();
        }

        $noteTable = $this->getServiceLocator()->get('NoteTable');

        $data = array(
            'note' => $noteTable->get($id),
            'errors' => array()
        );

        return $this->_returnJsonViewModel($data);
    }

    public function update($id, $data)
    {
        if (!$this->isAuthorised()) {
            return $this->notAuthenticatedResponse();
        }

        $returnData = array(
            'errors' => array()
        );

        $text = (!empty($data['text'])) ? $data['text'] : null;

        if (!$text) {
            $returnData['errors']['text'] = 'Text is a required field';
        } else {
            $noteTable = $this->getServiceLocator()->get('NoteTable');
            $note = $noteTable->get($id);

            $note->text = $text;
            $note->modified = date("Y-m-d H:i:s");
            $noteTable->save($note);
        }

        return $this->_returnJsonViewModel($returnData);
    }
}
