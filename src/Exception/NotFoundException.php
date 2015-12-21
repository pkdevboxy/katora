<?php

namespace Katora\Exception;

use Interop\Container\Exception\NotFoundException as InteropException;

class NotFoundException extends ContainerException implements InteropException
{
    function __construct($id)
    {
        parent::__construct("Service with id '{$id}' was not found in container");
    }
}
