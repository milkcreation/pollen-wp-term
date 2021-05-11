<?php

declare(strict_types=1);

namespace Pollen\WpTaxonomy;

use Pollen\Support\Exception\ProxyInvalidArgumentException;
use Pollen\Support\ProxyResolver;
use RuntimeException;
use WP_Term;
use WP_Term_Query;

/**
 * @see \Pollen\WpTaxonomy\WpTaxonomyProxyInterface
 */
trait WpTaxonomyProxy
{
    /**
     * Instance du gestionnaire de taxonomies Wordpress.
     * @var WpTaxonomyManagerInterface
     */
    private $wpTaxonomyManager;

    /**
     * Instance du gestionnaire de taxonomies Wordpress.
     *
     * @return WpTaxonomyManagerInterface
     */
    public function wpTaxonomy(): WpTaxonomyManagerInterface
    {
        if ($this->wpTaxonomyManager === null) {
            try {
                $this->wpTaxonomyManager = WpTaxonomyManager::getInstance();
            } catch (RuntimeException $e) {
                $this->wpTaxonomyManager = ProxyResolver::getInstance(
                    WpTaxonomyManagerInterface::class,
                    WpTaxonomyManager::class,
                    method_exists($this, 'getContainer') ? $this->getContainer() : null
                );
            }
        }

        return $this->wpTaxonomyManager;
    }

    /**
     * Instance(s) de terme(s) de taxonomie.
     *
     * @param true|string|int|WP_Term|WP_Term_Query|array|null $query
     *
     * @return WpTermQueryInterface|WpTermQueryInterface[]|array
     */
    public function wpTerm($query = null)
    {
        if (is_array($query) || ($query instanceof WP_Term_Query)) {
            return $this->wpTaxonomy()->terms($query);
        }

        if ($term = $this->wpTaxonomy()->term($query)) {
            return $term;
        }

        throw new ProxyInvalidArgumentException('WpTerm Queried is unavailable');
    }

    /**
     * Définition du gestionnaire de taxonomies Wordpress.
     *
     * @param WpTaxonomyManagerInterface $wpTaxonomyManager
     *
     * @return void
     */
    public function setWpTaxonomyManager(WpTaxonomyManagerInterface $wpTaxonomyManager): void
    {
        $this->wpTaxonomyManager = $wpTaxonomyManager;
    }
}
