<?php

/**
 *  This file is part of the Symfony Theatre application.
 *
 * (c) Christian Otter <phnixdev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\HttpFoundation\Request;

/**
 * Determines some basic application settings, by evaluating
 * the TLD part of the calling HTTP_HOST if the application was called over the webserver, or
 * ENV variables or parameter options if the application was called over the console.
 */
class Env
{
    /**
     * @var ArgvInput|null
     */
    private $input;

    /**
     * @var Request|null
     */
    private $request;

    /**
     * Env constructor.
     *
     * @param Request|null $request
     * @param ArgvInput|null $input
     *
     * @throws \Exception
     */
    public function __construct(Request $request = null, ArgvInput $input = null)
    {
        if (null === $request && null === $input) {
            throw new \Exception('Request and ArgvInput cannot be both null');
        }

        if ($request && $input) {
            throw new \Exception('Request and ArgvInput is not allowed at the same time');
        }

        $this->request = $request;
        $this->input = $input;
    }

    /**
     * Determine the application environment.
     *
     * The result will always be prod if there was no explicit call for dev or test.
     *
     * @return string
     */
    public function getEnv()
    {
        /**
         * If $input is an instance of ArgvInput then we are on the CLI
         */
        if ($this->isCli()) {
            $env = $this->input->getParameterOption(['--env', '-e'], getenv('THEATRE_ENV') ?: 'prod');
        } else {
            $httpHostArray = explode(".", $this->request->server->get('HTTP_HOST'));
            $env = end($httpHostArray);
        }

        if (!in_array($env, array('dev', 'test', 'prod'))) {
            $env = 'prod';
        }

        return $env;
    }

    /**
     * Is debugging mode?
     *
     * The result will be false if the application environment is prod or the console was called with no-debug option.
     *
     * @return bool
     */
    public function isDebug()
    {
        if ($this->isCli()) {
            return
                getenv('THEATRE_DEBUG') !== '0' &&
                !$this->input->hasParameterOption(['--no-debug', '']) &&
                $this->getEnv() !== 'prod';
        }

        return 'prod' !== $this->getEnv();
    }

    /**
     * Should the application be cached?
     *
     * The result will be true if the application environment is prod.
     *
     * @return bool
     */
    public function shouldCache()
    {
        return 'prod' === $this->getEnv();
    }

    /**
     * @return bool
     */
    private function isCli()
    {
        return !is_null($this->input);
    }
}
