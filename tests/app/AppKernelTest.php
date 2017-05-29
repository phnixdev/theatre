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

use App\AppKernel;
use PHPUnit\Framework\TestCase;
use Stringy\Stringy as S;
use Symfony\Component\Config\Loader\DelegatingLoader;

class AppKernelTest extends TestCase
{
    /**
     * Test the bundle registry
     */
    public function testRegisterBundles()
    {
        $kernel = new AppKernel('prod', false);
        $this->assertEquals(7, count($kernel->registerBundles()));

        $kernel = new AppKernel('test', true);
        $this->assertEquals(10, count($kernel->registerBundles()));

        $kernel = new AppKernel('dev', true);
        $this->assertEquals(10, count($kernel->registerBundles()));
    }

    /**
     * Test if the root directory is set right
     */
    public function testGetRootDir()
    {
        $kernel = new AppKernel('prod', false);
        $this->assertTrue(S::create($kernel->getRootDir())->endsWith('/app'));
    }

    /**
     * Test if the cache directory is set right
     */
    public function testGetCacheDir()
    {
        $kernel = new AppKernel('prod', false);
        $this->assertTrue(S::create($kernel->getCacheDir())->endsWith('/var/cache/prod'));
    }

    /**
     * Test if the log directory is set right
     */
    public function testGetLogDir()
    {
        $kernel = new AppKernel('prod', false);
        $this->assertTrue(S::create($kernel->getLogDir())->endsWith('/var/logs'));
    }

    /**
     * Test the configuration container registry
     */
    public function testRegisterContainerConfiguration()
    {
        $kernel = new AppKernel('prod', false);

        $stub = $this->createMock(DelegatingLoader::class);

        $kernel->registerContainerConfiguration($stub);
    }
}
