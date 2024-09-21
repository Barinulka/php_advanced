<?php

namespace App\UnitTests\Commands;

use App\Commands\Arguments;
use App\Exception\ArgumentsException;
use PhpParser\Node\Arg;
use PHPUnit\Framework\Attributes\DataProvider;
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


    // Провайдер данных
    public static function argumentsProvider(): iterable
    {
        return [
            // Первое значение будет передано в тест первым аргументом,
            // Второе значение будет передано вторым аргументом
            ['some_string', 'some_string'],
            ['some_key', 'some_key'],
            [' some_string', 'some_string'],
            [' some_string ', 'some_string'],
            [123, '123'],
            [12.3, '12.3']
        ];
    }

    // Связываем тест с провайдером данных с помощьюаннотации @dataProvider
    // У теста два агрумента
    // В одном тестовом наборе из провайдера данныхдва значения
    #[DataProvider('argumentsProvider')]
    public function testItConvertsArgumentsToStrings(
        $inputValue,
        $expectedValue
    ): void
    {
        $command = new Arguments(['some_key' => $inputValue]);

        $value = $command->get('some_key');

        $this->assertEquals($expectedValue, $value);
    }

    public function testItConstructorContinueWhenEmptyArgumentValue(): void
    {
        $command = new Arguments(['some_key' => 'some_value', 'empty_key' => '']);

        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('Не передан аргумент: empty_key');

        $command->get('empty_key');
    }

}
