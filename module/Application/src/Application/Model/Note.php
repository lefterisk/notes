<?php
namespace Application\Model;

/**
 * Class Note
 * @package Application\Model
 */
class Note extends AbstractModel
{
    public $id;
    public $user;
    public $title;
    public $text;
    public $created;
    public $modified;
    public $userFirstName;
    public $userLastName;
    public $userImage;

    public function getArrayCopy()
    {
        $data = parent::getArrayCopy();

        unset($data['userFirstName']);
        unset($data['userLastName']);
        unset($data['userImage']);

        return $data;
    }
}