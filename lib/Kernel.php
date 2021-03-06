<?php


namespace CsrDelft;

use Exception;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Exception\LoaderLoadException;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

/**
 * Configureer waar configuratie bestanden te vinden zijn.
 */
class Kernel extends BaseKernel {
	use MicroKernelTrait;

	const CONFIG_EXTS = '.{php,xml,yaml,yml}';

	public function registerBundles() {
		$contents = require $this->getProjectDir() . '/config/bundles.php';
		foreach ($contents as $class => $envs) {
			if ($envs[$this->environment] ?? $envs['all'] ?? false) {
				yield new $class();
			}
		}
	}

	public function getProjectDir(): string {
		return dirname(__DIR__);
	}

	public function getCacheDir() {
		return $this->getProjectDir() . '/var/cache/' . $this->environment;
	}

	public function getLogDir() {
		return $this->getProjectDir() . '/var/log';
	}

	/**
	 * @param ContainerBuilder $container
	 * @param LoaderInterface $loader
	 * @throws Exception
	 */
	protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader) {
		$container->addResource(new FileResource($this->getProjectDir() . '/config/bundles.php'));
		$container->setParameter('container.dumper.inline_class_loader', true);
		$confDir = $this->getProjectDir() . '/config';
		$loader->load($confDir . '/{packages}/*' . self::CONFIG_EXTS, 'glob');
		$loader->load($confDir . '/{packages}/' . $this->environment . '/**/*' . self::CONFIG_EXTS, 'glob');
		$loader->load($confDir . '/{services}' . self::CONFIG_EXTS, 'glob');
		$loader->load($confDir . '/{services}_' . $this->environment . self::CONFIG_EXTS, 'glob');

		// We willen dat alles ook werkt als Memcache niet bestaat
		if (class_exists("Memcache") && env('CACHE_HOST') != "") {
			$loader->load($confDir . '/custom/memcache.yaml');
		}
	}

	/**
	 * @param RouteCollectionBuilder $routes
	 * @throws LoaderLoadException
	 */
	protected function configureRoutes(RouteCollectionBuilder $routes) {
		$confDir = $this->getProjectDir() . '/config';
		$routes->import($confDir . '/{routes}/' . $this->environment . '/**/*' . self::CONFIG_EXTS, '/', 'glob');
		$routes->import($confDir . '/{routes}/*' . self::CONFIG_EXTS, '/', 'glob');
		$routes->import($confDir . '/{routes}' . self::CONFIG_EXTS, '/', 'glob');
	}
}
