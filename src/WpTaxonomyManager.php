<?php

declare(strict_types=1);

namespace Pollen\WpTerm;

use Pollen\Support\Proxy\ContainerProxy;
use Psr\Container\ContainerInterface as Container;

class WpTaxonomyManager implements WpTaxonomyManagerInterface
{
    use ContainerProxy;

    /**
     * Instance du gestionnaire des post Wordpress.
     * @var WpTermManagerInterface
     */
    protected $wpTerm;

    /**
     * Liste des types de post déclarés.
     * @var WpTaxonomyInterface[]|array
     */
    public $taxonomies = [];

    /**
     * @param WpTermManagerInterface $wpTerm
     * @param Container|null $container
     */
    public function __construct(WpTermManagerInterface $wpTerm, ?Container $container = null)
    {
        $this->wpTerm = $wpTerm;

        if ($container !== null) {
            $this->setContainer($container);
        }
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->taxonomies;
    }

    /**
     * @inheritDoc
     */
    public function get(string $name): ?WpTaxonomyInterface
    {
        return $this->taxonomies[$name] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function register(string $name,$taxonomyDef): WpTaxonomyInterface
    {
        if (!$taxonomyDef instanceof WpTaxonomyInterface) {
            $taxonomy = new WpTaxonomy($name, is_array($taxonomyDef) ? $taxonomyDef : []);
        } else {
            $taxonomy = $taxonomyDef;
        }
        $this->taxonomies[$name] = $taxonomy;

        return $taxonomy;
    }
}