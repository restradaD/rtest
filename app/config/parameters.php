<?php
$clearDB = getenv('CLEARDB_DATABASE_URL');

if (!empty($clearDB)) {
    $db = parse_url($clearDB);

    $container->setParameter('database_driver', 'pdo_mysql');
    $container->setParameter('database_host', $db['host']);
    $container->setParameter('database_port', isset($db['port']) ? $db['port'] : 3306);
    $container->setParameter('database_name', substr($db["path"], 1));
    $container->setParameter('database_user', $db['user']);
    $container->setParameter('database_password', $db['pass']);
    $container->setParameter('secret', getenv('SECRET'));
}