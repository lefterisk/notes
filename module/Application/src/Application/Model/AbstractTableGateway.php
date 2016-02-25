<?php
namespace Application\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class AbstractTableGateway
 * @package Application\Model
 */
class AbstractTableGateway implements ServiceLocatorAwareInterface
{
    protected $_tableGateway;
    protected $_connection;
    protected $_adapter;
    protected $_serviceLocator;
    protected $_deleteStrategy;

    public static $transactionCount = 0;

    /**
     * @param TableGateway $tableGateway
     */
    public function __construct(TableGateway $tableGateway)
    {
        $this->_tableGateway = $tableGateway;
        $this->_adapter      = $this->_tableGateway->getAdapter();
        $this->_connection   = $this->_adapter->getDriver()->getConnection();
    }

    public function getTableGateway()
    {
        return $this->_tableGateway;
    }

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

    /**
     * Wrapper for the connection's beginTransaction method to allow for
     * multiple transactions to be initialised in code. Each call after the
     * first only increments a count, returning the new value.
     *
     * @return number
     */
    public function beginTransaction()
    {
        if (self::$transactionCount === 0) {
            $this->_connection->beginTransaction();
        }

        return ++self::$transactionCount;
    }

    /**
     * Wrapper for the connection's commit method to allow for
     * multiple transactions to be committed in code. Each call after the
     * first only decrements a count, returning the new value.
     *
     * @return number
     */
    public function commit()
    {
        if (self::$transactionCount === 1) {
            $this->_connection->commit();
        }

        return --self::$transactionCount;
    }

    /**
     * Wrapper for the connection's rollback method.
     */
    public function rollback()
    {
        if (self::$transactionCount === 1) {
            $this->_connection->rollback();
        }

        return --self::$transactionCount;
    }

    /**
     * @param array $where
     * @return bool
     * @throws \Exception
     */
    public function recordExists($where = array())
    {
        if (empty($where)) {
            throw new \Exception('Record Exists requires some values to check against.');
        }

        $rowset = $this->getTableGateway()->select($where);

        return $rowset->count() === 0 ? false : true;
    }

    public function now()
    {
        return date("Y-m-d H:i:s");
    }

    /**
     * @param $id
     * @return array|\ArrayObject|null
     * @throws \Exception
     */
    public function get($id)
    {
        $id  = (int) $id;
        $rowSet = $this->_tableGateway->select(array('id' => $id));
        $row = $rowSet->current();

        if (!$row) {
            throw new \Exception("Could not find a record in the `{$this->getTableGateway()->getTable()}` table with an `id` column value of {$id}.");
        }

        return $row;
    }

    /**
     * @param $where
     * @return bool|null|\Zend\Db\ResultSet\ResultSet|ResultSetInterface
     * @throws \Exception
     */
    public function fetch($where = array())
    {
        if (is_numeric($where)) {
            throw new \Exception("If you need a single record then use the get method instead.");
        }

        $this->preFetchHook($where);

        if (is_object($where) && $where instanceof Select) {
            $results = $this->_tableGateway->selectWith($where);
        } else {
            $results = $this->_tableGateway->select($where);
        }

        $this->postFetchHook($results);

        $results->buffer();

        if ($results->count() === 0) {
            return false;
        }

        return $results;
    }

    /**
     * @param array $columns
     * @param $model
     * @param int $page
     * @param int $itemsPerPage
     * @param string $orderBy
     * @param string $orderDirection
     * @return Paginator
     */
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

    /**
     * @param $objectToSave
     * @return int
     * @throws \Exception
     */
    public function save($objectToSave)
    {
        if (!$objectToSave instanceof AbstractModel) {
            throw new \Exception('Abstract save method requires an instance of AbstractModel to be passed as the only argument.');
        }

        $data = $objectToSave->getArrayCopy();

        $this->preSaveHook($data);

        $id = empty($objectToSave->id)
            ? 0
            : (int) $objectToSave->id;

        if ($id === 0) {
            $this->_tableGateway->insert($data);
            $id = $this->_tableGateway->getLastInsertValue();
            $this->postSaveHook($id);
            return $this->_tableGateway->getLastInsertValue();
        } else {
            if ($this->get($id)) {
                $this->_tableGateway->update($data, array('id' => $id));
                $this->postSaveHook($id);
                return $id;
            } else {
                throw new \Exception("Object with id $id does not exist");
            }
        }
    }

    /**
     * @param $id
     * @throws \Exception
     */
    public function delete($id)
    {
        $this->beginTransaction();

        try {
            $this->preDeleteHook($id);

            $this->_tableGateway->delete(array('id' => (int)$id));

            $this->postDeleteHook($id);
            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    /**
     * @param $where
     */
    protected function preFetchHook(&$where) {}

    /**
     * @param ResultSetInterface $results
     */
    protected function postFetchHook(ResultSetInterface &$results) {}

    /**
     * @param $data
     */
    protected function preSaveHook(&$data) {}

    /**
     * @param $id
     */
    protected function postSaveHook(&$id) {}

    /**
     * @param $id
     */
    protected function preDeleteHook(&$id) {}

    /**
     * @param $id
     */
    protected function postDeleteHook(&$id) {}
}