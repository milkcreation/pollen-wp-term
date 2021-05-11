<?php

declare(strict_types=1);

namespace Pollen\WpTaxonomy;

use Pollen\Support\Concerns\BootableTraitInterface;
use Pollen\Support\Concerns\ConfigBagAwareTraitInterface;
use Pollen\Support\Proxy\ContainerProxyInterface;
use WP_Term;
use WP_Term_Query;

interface WpTaxonomyManagerInterface extends BootableTraitInterface, ConfigBagAwareTraitInterface, ContainerProxyInterface
{
    /**
     * Chargement.
     *
     * @return static
     */
    public function boot(): WpTaxonomyManagerInterface;

    /**
     * Instance du terme de taxonomie courante ou associée à une définition.
     *
     * @param string|int|WP_Term|null $term
     *
     * @return WpTermQueryInterface|null
     */
    public function term($term = null): ?WpTermQueryInterface;

    /**
     * Liste des instances de termes de taxonomie courants ou associés à une requête WP_Term_Query ou associés à
     * une liste d'arguments.
     *
     * @param WP_Term_Query|array|null $query
     *
     * @return WpTermQueryInterface[]|array
     */
    public function terms($query = null): array;
}