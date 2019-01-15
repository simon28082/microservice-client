<?php

function storage_path($path = '')
{
    return $path;
}

function global_config(): array
{
    return require __DIR__.'/../config/config.php';
}
