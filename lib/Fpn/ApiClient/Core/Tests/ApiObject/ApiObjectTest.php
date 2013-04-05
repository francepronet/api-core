<?php

namespace Fpn\ApiClient\Core\Tests\ApiObject;

use Fpn\ApiClient\Core\ApiObject\ApiObject;

class Whatever extends ApiObject
{
    public $id;
    public $foo;

    public function __construct($id = null)
    {
        $this->id = $id;

        parent::__construct();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getFoo()
    {
        return $this->foo;
    }

    public function setFoo($foo)
    {
        $this->foo = $foo;
        return $this;
    }
}

class ApiObjectTest extends \PHPUnit_Framework_TestCase
{
    public function testCanFetchOneItem()
    {
        $object   = new Whatever();
        $expected = new Whatever(3);

        $apiClient = $this->getMock('Fpn\ApiClient\Core\ApiClient', array('request'));
        $apiClient
            ->expects($this->once())
            ->method('request')
            ->will($this->returnValue(json_decode(json_encode($expected))));

        $object->setApiClient($apiClient);

        $object->fetch(3);

        $this->assertEquals($expected->getId(), $object->getId());
    }

    public function testCanFetchMultipleItmes()
    {
        $object   = new Whatever();
        $expected = array(
            new Whatever(1),
            new Whatever(2),
            new Whatever(3)
        );

        $apiClient = $this->getMock('Fpn\ApiClient\Core\ApiClient', array('request'));
        $apiClient
            ->expects($this->any())
            ->method('request')
            ->will($this->returnValue((array)json_decode(json_encode(array('items' => $expected)))));

        $object->setApiClient($apiClient);

        $objects = $object->fetchAll();

        $this->assertEquals($expected, $objects);
    }

    public function testCanCreateNewItem()
    {
        $object   = new Whatever();
        $expected = new Whatever(5);

        $apiClient = $this->getMock('Fpn\ApiClient\Core\ApiClient', array('request'));
        $apiClient
            ->expects($this->any())
            ->method('request')
            ->will($this->returnValue(json_decode(json_encode($expected))));

        $object->setApiClient($apiClient);

        $object->save();

        $this->assertEquals(5, $object->getId());
    }

    public function testCanUpdateObject()
    {
        $object       = new Whatever();
        $beforeUpdate = new Whatever(5);
        $expected     = new Whatever(5);

        $expected->setFoo('foo');

        $apiClient = $this->getMock('Fpn\ApiClient\Core\ApiClient', array('request'));
        $apiClient
            ->expects($this->any())
            ->method('request')
            ->will($this->onConsecutiveCalls(json_decode(json_encode($beforeUpdate)), json_decode(json_encode($expected))));

        $object
            ->setApiClient($apiClient)
            ->fetch(5)
            ->setFoo('foo')
            ->save()
            ;

        $this->assertEquals('foo', $object->getFoo());
    }

    /**
     * @expectedException \Exception
     */
    public function testCanDeleteObject()
    {
        $object   = new Whatever();
        $toDelete = new Whatever(5);


        $apiClient = $this->getMock('Fpn\ApiClient\Core\ApiClient', array('request'));
        $apiClient
            ->expects($this->any())
            ->method('request')
            ->will($this->onConsecutiveCalls(json_decode(json_encode($toDelete)), json_decode(json_encode($toDelete)), $this->throwException(new \Exception('API responded with 4xx error code'))))
            ;

        $object
            ->setApiClient($apiClient)
            ->fetch(5)
            ->delete()
            ->fetch(5)
            ;
    }
}
