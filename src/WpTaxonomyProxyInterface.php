<?php

declare(strict_types=1);

namespace Pollen\WpTaxonomy;

use WP_Term;
use Wp_Term_Query;

interface WpTaxonomyProxyInterface
{
    /**
     * Instance du gestionnaire de taxonomies Wordpress.
     *
     * @return WpTaxonomyManagerInterface
     */
    public function wpTaxonomy(): WpTaxonomyManagerInterface;

    /**
     * Instance(s) de terme(s) de taxonomie.
     *
     * @param true|string|int|WP_Term|WP_Term_Query|array|null $query
     *
     * @return WpTermQueryInterface|WpTermQueryInterface[]|array
     */
    public function wpTerm($query = null);

    /**
     * Définition du gestionnaire de taxonomies Wordpress.
     *
     * @param WpTaxonomyManagerInterface $wpTaxonomyManager
     *
     * @return void
     */
    public function setWpTaxonomyManager(WpTaxonomyManagerInterface $wpTaxonomyManager): void;
}
