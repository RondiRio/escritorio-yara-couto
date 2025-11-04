<?php 
return [ 
    'driver' => 'mysql', 
    'host' => getenv('DB_HOST'), 
    'database' => getenv('DB_DATABASE'), 
    'username' => getenv('DB_USERNAME'), 
    'password' => getenv('DB_PASSWORD'), 
]; 
