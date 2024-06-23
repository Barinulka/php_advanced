<?php

namespace App\UnitTests\Commands;

use App\Commands\Arguments;
use App\Exception\ArgumentsException;
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

    public function testItThrowsAnExceptionWhenArgumentIsAbsent(): void
    {
        // Подготавливаем объект с пустыми данными
        $command = new Arguments([]);

        // Описываем ожидаемый тип исключения и его сообщения
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('Не передан аргумент: some_key');

        // ВЫполняем действие приводящее к исключению
        $command->get('some_key');
    }
}
