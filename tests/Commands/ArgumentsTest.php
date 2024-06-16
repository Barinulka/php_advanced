<?php

namespace App\UnitTests\Commands;

use App\Commands\Arguments;
use PHPUnit\Framework\TestCase;

class ArgumentsTest extends TestCase
{
    public function testItReturnsArgumentsValueByName() {
        $command = new Arguments(['some_key' => 'some_value']);

        $value = $command->get('some_key');

        $this->assertEquals('some_value', $value);
    }

    public function testItReturnsValuesAsString() {
        $command = new Arguments(['some_key'=> 123]);

        $value = $command->get('some_key');

        $this->assertSame('123', $value);
        $this->assertEquals('123', $value);
        $this->assertIsString($value);
    }
}
