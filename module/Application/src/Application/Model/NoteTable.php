<?php
namespace Application\Model;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

/**
 * Class NoteTable
 * @package Application\Model
 */
class NoteTable extends AbstractTableGateway
{
    public function fetchForListing($columns = array(), $model, $page = 1, $itemsPerPage = 20, $orderBy = 'id', $orderDirection = Select::ORDER_ASCENDING)
    {
        if (empty($columns)) {
            $columns = array("*");
        } else {
            // Id needs to always be returned
            $columns[] = 'id';
        }

        $sql    = $this->_tableGateway->getSql();
        // specify the columns to return
        $select = $sql->select()
            ->columns($columns)
            ->join(array('u' => 'user'), 'user = u.id', array('userFirstName' => 'firstName', 'userLastName' => 'lastName' , 'userImage' => 'image'))
            ->order(array($orderBy . ' ' . $orderDirection ));
        // create a new result set based on the $model entity
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype($model);
        // create a new pagination adapter object
        $paginationAdapter = new DbSelect(
        // our configured select object
            $select,
            // the adapter to run it against
            $this->_tableGateway->getAdapter(),
            // the result set to hydrate
            $resultSetPrototype
        );

        $paginator = new Paginator($paginationAdapter);

        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($itemsPerPage);

        return $paginator;
    }

    public function get($id)
    {
        $id  = (int) $id;

        $sql    = $this->_tableGateway->getSql();
        // specify the columns to return
        $select = $sql->select()
            ->where(array('note.id' => $id))
            ->join(array('u' => 'user'), 'user = u.id', array('userFirstName' => 'firstName', 'userLastName' => 'lastName' , 'userImage' => 'image'))
        ;
        $rowSet = $this->_tableGateway->selectWith($select);
        $row = $rowSet->current();

        if (!$row) {
            throw new \Exception("Could not find a record in the `{$this->getTableGateway()->getTable()}` table with an `id` column value of {$id}.");
        }

        return $row;
    }
}