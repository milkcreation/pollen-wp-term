<?php

declare(strict_types=1);

namespace Pollen\WpTerm;

use Pollen\Support\Proxy\ContainerProxyInterface;

interface WpTaxonomyManagerInterface extends ContainerProxyInterface
{
    /**
     * Récupération de la liste des instance de taxonomies déclarées.
     *
     * @return WpTaxonomyInterface[]|array
     */
    public function all(): array;

    /**
     * Récupération d'une instance de taxonomie déclarée.
     *
     * @param string $name.
     *
     * @return WpTaxonomyInterface|null
     */
    public function get(string $name): ?WpTaxonomyInterface;

    /**
     * Déclaration d'une taxonomie.
     *
     * @param string $name
     * @param WpTaxonomyInterface|array $taxonomyDef
     *
     * @return WpTaxonomyInterface
     */
    public function register(string $name, $taxonomyDef): WpTaxonomyInterface;
}