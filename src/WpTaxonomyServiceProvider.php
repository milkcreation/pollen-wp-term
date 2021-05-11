<?php

declare(strict_types=1);

namespace Pollen\WpTaxonomy;

use Pollen\Container\BaseServiceProvider;

class WpTaxonomyServiceProvider extends BaseServiceProvider
{
    /**
     * @var string[]
     */
    protected $provides = [
        WpTaxonomyManagerInterface::class,
    ];

    /**
     * @inheritdoc
     */
    public function register(): void
    {
        $this->getContainer()->share(WpTaxonomyManagerInterface::class, function() {
            return new WpTaxonomyManager([], $this->getContainer());
        });
    }
}