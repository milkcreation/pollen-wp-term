<?php

declare(strict_types=1);

namespace Pollen\WpTerm;

use WP_Term;
use Wp_Term_Query;

interface WpTermProxyInterface
{
    /**
     * Instance du gestionnaire de taxonomies Wordpress|Instance(s) de terme(s) de taxonomie.
     *
     * @param true|string|int|WP_Term|WP_Term_Query|array|null $query
     *
     * @return WpTermManagerInterface|WpTermQueryInterface|WpTermQueryInterface[]|array
     */
    public function wpTerm($query = null);

    /**
     * Définition du gestionnaire de taxonomies Wordpress.
     *
     * @param WpTermManagerInterface $wpTermManager
     *
     * @return void
     */
    public function setWpTermManager(WpTermManagerInterface $wpTermManager): void;
}
