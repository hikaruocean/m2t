<?php
namespace Conpoz\App\Controller;

class Error
{
    public function http404Action () {
        header('HTTP/1.1 404 NOT FOUND');
        echo 'hello 404!';
    }
}
