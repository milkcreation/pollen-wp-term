<?php

declare(strict_types=1);

namespace Pollen\WpTerm;

use Pollen\Support\Concerns\BootableTraitInterface;
use Pollen\Support\Concerns\ConfigBagAwareTraitInterface;
use Pollen\Support\Proxy\ContainerProxyInterface;
use WP_Term;
use WP_Term_Query;

interface WpTermManagerInterface extends BootableTraitInterface, ConfigBagAwareTraitInterface, ContainerProxyInterface
{
    /**
     * Chargement.
     *
     * @return static
     */
    public function boot(): WpTermManagerInterface;

    /**
     * Liste des instances de termes de taxonomie courants ou associés à une requête WP_Term_Query ou associés à
     * une liste d'arguments.
     *
     * @param WP_Term_Query|array|null $query
     *
     * @return WpTermQueryInterface[]|array
     */
    public function fetch($query = null): array;

    /**
     * Instance du terme de taxonomie courante ou associée à une définition.
     *
     * @param string|int|WP_Term|null $term
     *
     * @return WpTermQueryInterface|null
     */
    public function get($term = null): ?WpTermQueryInterface;

    /**
     * Récupération de l'instance d'une taxonomie.
     *
     * @param string $name
     *
     * @return WpTaxonomyInterface|null
     */
    public function getTaxonomy(string $name): ?WpTaxonomyInterface;

    /**
     * Déclaration d'une taxonomie.
     *
     * @param string $name
     * @param array|WpTaxonomyInterface $taxonomyDef
     *
     * @return WpTaxonomyInterface
     */
    public function registerTaxonomy(string $name, $taxonomyDef = []): WpTaxonomyInterface;

    /**
     * Instance du gestionnaire de taxonomies.
     *
     * @return WpTaxonomyManagerInterface
     */
    public function taxonomyManager(): WpTaxonomyManagerInterface;
}