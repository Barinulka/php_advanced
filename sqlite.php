<?php

$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

$connection->exec(
  "INSERT into users (first_name, last_name) VALUES ('Ivan', 'Ivanov')"
);