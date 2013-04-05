<?php

namespace Fpn\ApiClient\Core\Tests\Utility;

use Fpn\ApiClient\Core\Utility\Caster;

class WhateverClass
{
    public $foo;
    public $bar;

    public function __construct($foo = null, $bar = null)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }
}

class CasterTest extends \PHPUnit_Framework_TestCase
{
    public function testCanCastObjectIntoAnother()
    {
        $expected = $stdClass = new WhateverClass('foo', 'bar');
        $stdClass = json_decode(json_encode($expected));

        $casted = new WhateverClass();
        Caster::cast($stdClass, $casted);

        $this->assertEquals($expected, $casted);
    }
}
