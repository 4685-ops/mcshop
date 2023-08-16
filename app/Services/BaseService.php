<?php

namespace App\Services;



use App\Exceptions\BusinessException;

class BaseService
{
    protected static $instance = [];

    /**
     * @return static
     */
    public static function getInstance(): BaseService
    {
        if ((static::$instance[static::class] ?? null) instanceof static) {
            return static::$instance[static::class];
        }
        return static::$instance[static::class] = new static();
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }


    /**
     * @param  array  $codeResponse
     * @param  string  $info
     * @throws BusinessException
     */
    public function throwBusinessException(array $codeResponse, $info = '')
    {
        throw new BusinessException($codeResponse, $info);
    }

}
