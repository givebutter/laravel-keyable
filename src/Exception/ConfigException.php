<?php

namespace Givebutter\LaravelKeyable\Exception;

class ConfigException extends \RuntimeException
{
    /**
     * @param string $configKey
     * @param string $configValue
     * @return self
     */
    public static function withUnsupportedConfig(string $configKey, string $configValue): self
    {
        return new self(
            sprintf(
                'The config `%s` key with value `%s` is not supported',
                $configKey,
                $configValue
            )
        );
    }
}
