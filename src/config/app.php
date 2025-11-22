<?php 
// As variáveis de ambiente já foram carregadas pelo index.php

if (!defined('APP_NAME')) {
    define('APP_NAME', getenv('APP_NAME')); 
}

if (!defined('APP_URL')) {
    define('APP_URL', getenv('APP_URL')); 
}

date_default_timezone_set('America/Sao_Paulo');