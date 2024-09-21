Учебный проект
===
***
В рабках учебного проекта будет создано простое API для небольшого чата
***

## Развертывание проекта
* Установка зависимостей
```
composer install
```

## Работа с консолью
* Добалвение пользователя
```
php cli.php username=some_login first_name=some_name last_name=some_last_name
```

## PHPUnit тесты

Тесты находятся в папке tests

* Запуск unit тестов

```
composer test
```

* Анализ покрытия кода тестами
> Для работы должно быть установлено расширение PHP Xdebug
``` 
 php -dxdebug.mode=coverage vendor/bin/phpunit tests --coverage-html coverage_report --coverage-filter src
```
В результате PHPUnit сгенерирует отчёт в виде html-файлов в папке coverage_report. 
В любом браузере можно будет открыть файл coverage_report/index.html и просмотреть отчеты по покрытию кода тестами.