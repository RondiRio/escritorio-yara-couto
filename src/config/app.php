<?php 
if (file_exists(__DIR__ . '/../../.env')) { 
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..'); 
    $dotenv->load(); 
} 
define('APP_NAME', getenv('APP_NAME')); 
define('APP_URL', getenv('APP_URL')); 
date_default_timezone_set('America/Sao_Paulo'); 
