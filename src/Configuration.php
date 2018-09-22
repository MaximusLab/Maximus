<?php
/*
 * This file is part of the Maximus package.
 *
 * (c) Leo Tsun <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Maximus;

use Symfony\Component\HttpFoundation\ParameterBag;

class Configuration
{
    /**
     * @var string Configuration file path (default should be %kernel.project_dir%/maximus.json)
     */
    private $filePath;

    /**
     * @var ParameterBag Configuration parameters
     */
    private $parameters;

    /**
     * @var bool Check the configuration is loaded or not
     */
    private $loaded = false;

    /**
     * Configuration constructor.
     *
     * @param string $filePath Configuration file path (default should be %kernel.project_dir%/maximus.json)
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Get Maximus configuration value
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $parameters = $this->load();

        return $parameters->get($key, $default);
    }

    /**
     * Set Maximus configuration value
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function set($key, $value)
    {
        $this->parameters->set($key, $value);

        $parametersText = json_encode($this->parameters->all(), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);

        file_put_contents($this->filePath, $parametersText);

        return $this;
    }

    /**
     * @return ParameterBag
     */
    private function load()
    {
        if ($this->loaded && $this->parameters instanceof ParameterBag) {
            return $this->parameters;
        }

        if (!file_exists($this->filePath)) {
            throw new \InvalidArgumentException(sprintf('Maximus configuration file is not found with "%s"', $this->filePath));
        }

        $parameters = @json_decode(file_get_contents($this->filePath), true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \RuntimeException(sprintf('json_decode with error: "%s"', json_last_error_msg()));
        }

        $parameters = empty($parameters) || !is_array($parameters) ? [] : $parameters;
        $this->parameters = new ParameterBag($parameters);

        return $this->parameters;
    }
}
