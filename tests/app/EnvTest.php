<?php

/**
 *  This file is part of the Symfony Theatre application.
 *
 * (c) Christian Otter <phnixdev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\App;

use App\Env;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\HttpFoundation\Request;

class EnvTest extends TestCase
{
    protected function setUp()
    {
        // Unset both environment vars to avoid test distortion if they are set in e.g. ~/.profile
        putenv('THEATRE_DEBUG');
        putenv('THEATRE_ENV');
    }

    /**
     * Test the application environment under different hostnames
     */
    public function testGetEnv()
    {
        $tests = [
            'prod' => [
                'example.prod',
                'example.com',
                '127.0.0.1'
            ],
            'test' => [
                'example.test'
            ],
            'dev' => [
                'example.dev',
            ]
        ];

        foreach ($tests as $expected => $urls) {
            // Test without subdomain
            foreach ($urls as $url) {
                $_SERVER['HTTP_HOST'] = $url;
                $request = Request::createFromGlobals();
                $env = new Env($request);

                $this->assertEquals($expected, $env->getEnv());
            }

            // Test with subdomain
            foreach ($urls as $url) {
                $_SERVER['HTTP_HOST'] = 'www.'.$url;
                $request = Request::createFromGlobals();
                $env = new Env($request);

                $this->assertEquals($expected, $env->getEnv());
            }
        }
    }

    /**
     * Test application environment on CLI by passing the env command-line option
     */
    public function testGetEnvConsole()
    {
        $tests = [
            '-e=prod'      => 'prod',
            '-e=something' => 'prod',
            '-e=test'      => 'test',
            '-e=dev'       => 'dev',
        ];

        foreach ($tests as $arg => $expected) {
            $env = new Env(null, new ArgvInput([
                'bin/console',
                $arg
            ]));

            $this->assertEquals($expected, $env->getEnv());
        }

        // Test bin/console without argument
        $env = new Env(null, new ArgvInput([
            'bin/console',
        ]));

        $this->assertEquals('prod', $env->getEnv());
    }

    /**
     * Test the application environment on CLI if environment variable THEATRE_ENV is set
     */
    public function testGetEnvConsoleWithEnvVar()
    {
        $tests = [
            'prod' => ['prod', 'something'],
            'test' => ['test'],
            'dev'  => ['dev']
        ];

        foreach ($tests as $expected => $vars) {
            foreach ($vars as $var) {
                putenv('THEATRE_ENV=' . $var);
                $env = new Env(null, new ArgvInput());

                $this->assertEquals($expected, $env->getEnv());
            }
        }
    }

    /**
     * Test if the application should run in debug mode under different hostnames
     */
    public function testIsDebug()
    {
        $tests = [
            'example.com'  => false,
            'example.prod' => false,
            '127.0.0.1'    => false,
            'example.test' => true,
            'example.dev'  => true,
        ];

        foreach ($tests as $url => $expected) {
            $_SERVER['HTTP_HOST'] = $url;
            $request = Request::createFromGlobals();
            $env = new Env($request);

            $this->assertEquals($expected, $env->isDebug());
        }
    }

    /**
     * Test if the application should run in debug mode on CLI by passing the env command-line option
     */
    public function testIsDebugConsole()
    {
        $tests = [
            '-e=prod'      => false,
            '-e=something' => false,
            '-e=test'      => true,
            '-e=dev'       => true,
        ];

        foreach ($tests as $arg => $expected) {
            $env = new Env(null, new ArgvInput([
                'bin/console',
                $arg
            ]));

            $this->assertEquals($expected, $env->isDebug());
        }

        // Test bin/console without argument
        $env = new Env(null, new ArgvInput([
            'bin/console',
        ]));

        $this->assertEquals(false, $env->isDebug());
    }

    /**
     * Test if application should run in debug mode if environment variable THEATRE_DEBUG is set
     */
    public function testIsDebugConsoleWithEnvVar()
    {
        $tests = [
            'prod' => [
                '0'    => false,
                '1'    => false,
                'true' => false
            ],
            'test' => [
                '0'    => false,
                '1'    => true,
                'true' => true
            ],
            'dev' => [
                '0'    => false,
                '1'    => true,
                'true' => true
            ]
        ];

        foreach ($tests as $_env => $test) {
            foreach ($test as $var => $expected) {
                putenv('THEATRE_ENV=' . $_env);
                putenv('THEATRE_DEBUG=' . $var);
                $env = new Env(null, new ArgvInput());

                $this->assertEquals($expected, $env->isDebug());
            }
        }
    }

    /**
     * Test if application should cache under different hostnames
     */
    public function testShouldCache()
    {
        $tests = [
            'example.com'  => true,
            'example.prod' => true,
            '127.0.0.1'    => true,
            'example.test' => false,
            'example.dev'  => false,
        ];

        foreach ($tests as $url => $expected) {
            $_SERVER['HTTP_HOST'] = $url;
            $request = Request::createFromGlobals();
            $env = new Env($request);

            $this->assertEquals($expected, $env->shouldCache());
        }
    }

    /**
     * Test if application should cache on CLI by passing the env command-line option
     */
    public function testShouldCacheConsole()
    {
        $tests = [
            '-e=prod'      => true,
            '-e=something' => true,
            '-e=test'      => false,
            '-e=dev'       => false,
        ];

        foreach ($tests as $arg => $expected) {
            $env = new Env(null, new ArgvInput([
                'bin/console',
                $arg
            ]));

            $this->assertEquals($expected, $env->shouldCache());
        }

        // Test bin/console without argument
        $env = new Env(null, new ArgvInput([
            'bin/console',
        ]));

        $this->assertEquals(true, $env->shouldCache());
    }

    /**
     * Test constructing the Env object without Request and ArgvInput parameter
     */
    public function testRequestAndArgvInputNotSetException()
    {
        $this->expectException(\Exception::class);

        new Env();
    }

    /**
     * Test constructing the Env object with both Request and ArgvInput parameter
     */
    public function testRequestAndArgvInputException()
    {
        $request = Request::createFromGlobals();
        $input = new ArgvInput();

        $this->expectException(\Exception::class);

        new Env($request, $input);
    }
}
