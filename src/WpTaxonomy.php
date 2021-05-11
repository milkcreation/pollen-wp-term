<?php

declare(strict_types=1);

namespace Pollen\WpTerm;

use Pollen\Support\Concerns\BootableTrait;
use Pollen\Support\Concerns\ParamsBagAwareTrait;
use Pollen\Translation\LabelsBag;
use WP_Taxonomy;
use WP_REST_Controller;

/**
 * @see https://developer.wordpress.org/reference/functions/register_taxonomy/
 *
 * @property-read string $label
 * @property-read object $labels
 * @property-read string $description
 * @property-read bool $public
 * @property-read bool $publicly_queryable
 * @property-read bool $hierarchical
 * @property-read bool $show_ui
 * @property-read bool $show_in_menu
 * @property-read bool $show_in_nav_menus
 * @property-read bool $show_tagcloud
 * @property-read bool show_in_quick_edit
 * @property-read bool $show_admin_column
 * @property-read bool|callable $meta_box_cb
 * @property-read callable $meta_box_sanitize_cb
 * @property-read array $object_type
 * @property-read object $cap
 * @property-read array|false $rewrite
 * @property-read string|false $query_var
 * @property-read callable $update_count_callback
 * @property-read bool $show_in_rest
 * @property-read string|bool $rest_base
 * @property-read string|bool $rest_controller_class
 * @property-read WP_REST_Controller $rest_controller
 * @property-read array|string $default_term
 * @property-read bool|null $sort
 * @property-read array|null $args
 * @property-read bool $_builtin
 */
class WpTaxonomy implements WpTaxonomyInterface
{
    use BootableTrait;
    use ParamsBagAwareTrait;
    use WpTermProxy;

    /**
     * Instance du gestionnaire d'intitulés.
     * @var LabelsBag|null
     */
    protected $labelBag;

    /**
     * Nom de qualification.
     * @var string
     */
    protected $name = '';

    /**
     * Instance de la taxonomie Wordpress associée.
     * @return WP_Taxonomy|null
     */
    protected $wpTaxonomy;

    /**
     * @param string $name
     * @param array $params
     *
     * @return void
     */
    public function __construct(string $name, array $params = [])
    {
        $this->name = $name;
        $this->params($params);
    }

    /**
     * @inheritDoc
     */
    public function boot(): WpTaxonomyInterface
    {
        if (!$this->isBooted()) {
            //events()->trigger('wp-term.taxonomy.booting', [&$this]);

            $this->parseParams();

            $this->setBooted();
            //events()->trigger('wp-term.taxonomy.booted', [&$this]);
        }

        return $this;
    }

    /**
     * Récupération des données de délégation de l'objet WP_Taxomony associé.
     *
     * @param int|string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->wpTaxonomy->{$key} ?? null;
    }

    /**
     * Vérification d'une données de délégation de l'objet WP_POST associé.
     *
     * @param int|string $key
     *
     *
     * @return bool
     */
    public function __isset($key): bool
    {
        return isset($this->wpTaxonomy->{$key});
    }

    /**
     * Définition d'une donnée de délégation de l'objet WP_POST associé.
     *
     * @param int|string $key
     * @param mixed $value
     *
     *
     * @return void
     */
    public function __set($key, $value): void
    {
    }

    /**
     * Résolution de sortie de la classe sous forme de chaîne de caractère.
     * {@internal Retourne le nom de qualification du type de post.}
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getName();
    }

    /**
     * @inheritdoc
     */
    public function defaultParams(): array
    {
        return [
            //'label'              => '',
            //'labels'             => '',
            'description'           => '',
            'public'                => true,
            //'exclude_from_search'   => false,
            //'publicly_queryable'    => true,
            //'show_ui'               => true,
            //'show_in_nav_menus'     => true,
            //'show_in_menu'          => true,
            //'show_in_admin_bar'     => true,
            'menu_position'         => null,
            'menu_icon'             => null,
            'capability_type'       => 'post',
            // @todo capabilities   => [],
            'map_meta_cap'          => null,
            'hierarchical'          => false,
            'supports'              => ['title', 'editor'],
            // @todo 'register_meta_box_cb'  => '',
            'taxonomies'            => [],
            'has_archive'           => false,
            'rewrite'               => [
                'slug'       => $this->getName(),
                'with_front' => false,
                'feeds'      => true,
                'pages'      => true,
                'ep_mask'    => EP_PERMALINK,
            ],
            'permalink_epmask'      => EP_PERMALINK,
            'query_var'             => true,
            'can_export'            => true,
            'delete_with_user'      => null,
            'show_in_rest'          => false,
            'rest_base'             => $this->getName(),
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        ];
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function label(?string $key = null, string $default = '')
    {
        if (is_null($key)) {
            return $this->labelBag;
        }
        return $this->labelBag->get($key, $default);
    }

    /**
     * @inheritDoc
     */
    public function parseParams(): void
    {
        $labels = $this->params('labels', []);
        if (is_object($labels)) {
            $this->params(['labels' => get_object_vars($labels)]);
        }

        $this->params(['label' => $this->params('label', $this->getName())]);

        $this->params(['plural' => $this->params('plural', $this->params('labels.name', $this->params('label')))]);

        $this->params(
            ['singular' => $this->params('singular', $this->params('labels.singular_name', $this->params('label')))]
        );

        $this->params(['gender' => $this->params('gender', false)]);

        $this->labelBag = WpTaxonomyLabelsBag::create(
            array_merge(
                [
                    'singular' => $this->params('singular'),
                    'plural'   => $this->params('plural'),
                    'gender'   => $this->params('gender'),
                ],
                $this->params('labels', [])
            ),
            $this->params('label')
        );
        $this->params(['labels' => $this->labelBag->all()]);

        $this->params(
            [
                'publicly_queryable' => $this->params()->has('publicly_queryable')
                    ? $this->params('publicly_queryable') : $this->params('public'),
            ]
        );

        $this->params(
            [
                'show_ui' => $this->params()->has('show_ui')
                    ? $this->params('show_ui') : $this->params('public'),
            ]
        );

        $this->params(
            [
                'show_in_nav_menus' => $this->params()->has('show_in_nav_menus')
                    ? $this->params('show_in_nav_menus') : $this->params('public'),
            ]
        );

        $this->params(
            [
                'show_in_menu' => $this->params()->has('show_in_menu')
                    ? $this->params('show_in_menu') : $this->params('show_ui'),
            ]
        );

        $this->params(
            [
                'show_in_admin_bar' =>
                    $this->params()->has('show_in_admin_bar')
                        ? $this->params('show_in_admin_bar') : $this->params('show_in_menu'),
            ]
        );

        $this->params(
            [
                'show_tagcloud' => $this->params()->has('show_tagcloud')
                    ? $this->params('show_tagcloud')
                    : $this->params('show_ui'),
            ]
        );

        $this->params(
            [
                'show_in_quick_edit' => $this->params()->has('show_in_quick_edit')
                    ? $this->params('show_in_quick_edit')
                    : $this->params('show_ui'),
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function setWpTaxonomy(WP_Taxonomy $taxonomy): WpTaxonomyInterface
    {
        $this->wpTaxonomy = $taxonomy;

        return $this;
    }
}