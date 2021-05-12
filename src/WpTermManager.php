<?php

declare(strict_types=1);

namespace Pollen\WpTerm;

use Pollen\Support\Concerns\BootableTrait;
use Pollen\Support\Concerns\ConfigBagAwareTrait;
use Pollen\Support\Exception\ManagerRuntimeException;
use Pollen\Support\Proxy\ContainerProxy;
use Psr\Container\ContainerInterface as Container;
use WP_Taxonomy;

class WpTermManager implements WpTermManagerInterface
{
    use BootableTrait;
    use ConfigBagAwareTrait;
    use ContainerProxy;

    /**
     * Instance principale.
     * @var static|null
     */
    private static $instance;

    /**
     * Instance du gestionnaire de taxonomies.
     * @var WpTaxonomyManagerInterface
     */
    protected $taxonomyManager;

    /**
     * @param array $config
     * @param Container|null $container Instance du conteneur d'injection de dépendances.
     *
     * @return void
     */
    public function __construct(array $config = [], ?Container $container = null)
    {
        $this->setConfig($config);

        if ($container !== null) {
            $this->setContainer($container);
        }

        if ($this->config('boot_enabled', true)) {
            $this->boot();
        }

        if (!self::$instance instanceof static) {
            self::$instance = $this;
        }
    }

    /**
     * Récupération de l'instance principale.
     *
     * @return static
     */
    public static function getInstance(): WpTermManagerInterface
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }
        throw new ManagerRuntimeException(sprintf('Unavailable [%s] instance', __CLASS__));
    }

    /**
     * @inheritDoc
     */
    public function boot(): WpTermManagerInterface
    {
        if (!$this->isBooted()) {
            add_action('init', function () {
                global $wp_taxonomies;

                foreach ($this->taxonomyManager()->all() as $name => $taxonomy) {
                    if (!$object_type = $taxonomy->params()->get('object_type')) {
                        $object_type = [];
                    } elseif (is_string('object_type')) {
                        $object_type = [$object_type];
                    } elseif (!is_array($object_type)) {
                        $object_type = [];
                    }

                    if (!isset($wp_taxonomies[$name])) {
                        register_taxonomy($name, $object_type, $taxonomy->params()->all());
                    }

                    if ($wp_taxonomies[$name] instanceof WP_Taxonomy) {
                        $taxonomy->setWpTaxonomy($wp_taxonomies[$name]);
                    }
                }
            }, 11);

            add_action('init', function () {
                global $wp_taxonomies;

                foreach ($wp_taxonomies as $name => $attrs) {
                    if (!$this->getTaxonomy($name)) {
                        $this->registerTaxonomy($name, get_object_vars($attrs));
                    }
                }
            }, 999999);

            $this->setBooted();
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function fetch($query = null): array
    {
        return WpTermQuery::fetch($query);
    }

    /**
     * @inheritDoc
     */
    public function get($term = null): ?WpTermQueryInterface
    {
        return WpTermQuery::create($term);
    }

    /**
     * @inheritDoc
     */
    public function getTaxonomy(string $name): ?WpTaxonomyInterface
    {
        return $this->taxonomyManager()->get($name);
    }

    /**
     * @inheritDoc
     */
    public function registerTaxonomy(string $name, $taxonomyDef = []): WpTaxonomyInterface
    {
        return $this->taxonomyManager()->register($name, $taxonomyDef);
    }

    /**
     * @inheritDoc
     */
    public function taxonomyManager(): WpTaxonomyManagerInterface
    {
        if ($this->taxonomyManager === null) {
            $this->taxonomyManager = $this->containerHas(WpTaxonomyManagerInterface::class)
                ? $this->containerGet(WpTaxonomyManagerInterface::class) : new WpTaxonomyManager($this);
        }
        return $this->taxonomyManager;
    }
}