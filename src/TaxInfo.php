<?php
/**
 * Taxonomy info.
 *
 * @package     DRPPSM\TaxInfo
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2024, Daryl Peterson
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 * @since       1.0.0
 */

namespace DRPPSM;

use WP_Post;
use WP_Term;

defined( 'ABSPATH' ) || exit;

/**
 * Taxonomy info.
 *
 * @package     DRPPSM\TaxInfo
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2024, Daryl Peterson
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 * @since       1.0.0
 */
class TaxInfo {

	/**
	 * Ids array
	 *
	 * @var array
	 * @since 1.0.0
	 */
	private array $ids;

	/**
	 * Links array
	 *
	 * @var array
	 * @since 1.0.0
	 */
	private array $links;

	/**
	 * Names array
	 *
	 * @var array
	 * @since 1.0.0
	 */
	private array $names;

	/**
	 * Taxonomy name.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private string $taxonomy;

	/**
	 * Term id.
	 *
	 * @var int
	 * @since 1.0.0
	 */
	private int $term_id;

	/**
	 * Term object.
	 *
	 * @var WP_Term
	 * @since 1.0.0
	 */
	private WP_Term $term;

	/**
	 * Sermon info.
	 *
	 * @var SermonsInfo
	 * @since 1.0.0
	 */
	private ?SermonsInfo $sermons;

	/**
	 * Taxonomy list
	 *
	 * @var array
	 * @since
	 */
	private array $list;

	/**
	 * Pointer for taxonomy.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private string $pointer;

	/**
	 * TaxInfo constructor.
	 *
	 * @param string $taxonomy Taxonomy name.
	 * @since 1.0.0
	 */
	public function __construct( string $taxonomy, int $term_id ) {

		$this->taxonomy = $taxonomy;
		$this->term_id  = $term_id;

		$this->ids     = array();
		$this->links   = array();
		$this->names   = array();
		$this->pointer = DRPPSM_TAX_SERIES;

		try {
			$this->set_term( $term_id, $taxonomy );
			$this->init();

			Logger::debug( $this );

			// @codeCoverageIgnoreStart
		} catch ( \Exception $e ) {
			Logger::error(
				array(
					'MESSAGE' => $e->getMessage(),
					'TRACE'   => $e->getTrace(),
				)
			);
			// @codeCoverageIgnoreEnd
		}
	}

	/**
	 * Serialize magic method.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function __serialize(): array {
		return array(
			'ids'      => $this->ids,
			'links'    => $this->links,
			'names'    => $this->names,
			'object'   => $this->term,
			'taxonomy' => $this->taxonomy,
			'term_id'  => $this->term_id,
		);
	}

	/**
	 * Unserialize magic method.
	 *
	 * @param array $data Data.
	 * @return void
	 * @since 1.0.0
	 */
	public function __unserialize( array $data ): void {
		$this->ids      = $data['ids'];
		$this->links    = $data['links'];
		$this->names    = $data['names'];
		$this->term     = $data['object'];
		$this->taxonomy = $data['taxonomy'];
		$this->term_id  = $data['term_id'];
	}

	/**
	 * To string magic method.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function __toString(): string {
		$msg  = "Term : $this->term->name ";
		$msg .= 'Sermons : ' . $this->sermons->count();
		$msg .= 'Books : ' . $this->books()->count();
		$msg .= 'Series : ' . $this->series()->count();
		$msg .= 'Topics : ' . $this->topics()->count();
		return $msg;
	}

	/**
	 * Get summary.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function summary(): array {
		return array(
			'term'     => $this->term->name,
			'term_id'  => $this->term_id,
			'taxonomy' => $this->taxonomy,
			'sermons'  => $this->sermons->count(),
			'books'    => $this->books()->count(),
			'series'   => $this->series()->count(),
			'topics'   => $this->topics()->count(),
		);
	}

	/**
	 * Switch to books taxonomy.
	 *
	 * @return self
	 * @since 1.0.0
	 */
	public function books() {
		$this->pointer = DRPPSM_TAX_BIBLE;
		return $this;
	}

	/**
	 * Switch to series taxonomy.
	 *
	 * @return self
	 * @since 1.0.0
	 */
	public function series() {
		$this->pointer = DRPPSM_TAX_SERIES;
		return $this;
	}

	/**
	 * Switch to topics taxonomy.
	 *
	 * @return self
	 * @since 1.0.0
	 */
	public function topics() {
		$this->pointer = DRPPSM_TAX_TOPICS;
		return $this;
	}

	/**
	 * Switch to preachers taxonomy.
	 *
	 * @return self
	 * @since 1.0.0
	 */
	public function preachers() {
		$this->pointer = DRPPSM_TAX_PREACHER;
		return $this;
	}

	/**
	 * Get names array or string.
	 *
	 * @param bool        $array True to return array, false to return string.
	 * @param null|string $taxonomy Optional taxonomy name.
	 * @return array|string
	 * @since 1.0.0
	 */
	public function names(
		bool $array = true,
		?string $taxonomy = null
	): array|string {

		if ( ! isset( $taxonomy ) ) {
			$taxonomy = $this->pointer;
		}

		if ( ! isset( $this->names[ $taxonomy ] ) ) {
			return array();
		}

		if ( $array ) {
			return array_values( $this->names[ $taxonomy ] );
		}

		$names = $this->names[ $taxonomy ];
		asort( $names );

		return implode( ', ', $names );
	}

	/**
	 * Get ids array.
	 *
	 * @param null|string $taxonomy Optional taxonomy name.
	 * @return array
	 * @since 1.0.0
	 */
	public function ids( ?string $taxonomy = null ): array {
		if ( ! isset( $taxonomy ) ) {
			$taxonomy = $this->pointer;
		}

		if ( ! isset( $this->ids[ $taxonomy ] ) ) {
			return array();
		}

		return $this->ids[ $taxonomy ];
	}

	/**
	 * Get links array.
	 *
	 * @param null|string $taxonomy Optional taxonomy name.
	 * @return array
	 * @since 1.0.0
	 */
	public function links( ?string $taxonomy = null ): array {
		if ( ! isset( $taxonomy ) ) {
			$taxonomy = $this->pointer;
		}

		if ( ! isset( $this->links[ $taxonomy ] ) ) {
			return array();
		}

		return $this->links[ $taxonomy ];
	}

	/**
	 * Get term link.
	 *
	 * @param int         $id Term id.
	 * @param null|string $taxonomy Optional taxonomy name.
	 * @return string|null
	 * @since 1.0.0
	 */
	public function link( int $id, ?string $taxonomy = null ): ?string {
		if ( ! isset( $taxonomy ) ) {
			$taxonomy = $this->pointer;
		}

		if ( ! isset( $this->links[ $taxonomy ][ $id ] ) ) {
			return null;
		}

		return $this->links[ $taxonomy ][ $id ];
	}

	/**
	 * Get taxonomy count.
	 *
	 * @param null|string $taxonomy Optional taxonomy name.
	 * @return int
	 * @since 1.0.0
	 */
	public function count( ?string $taxonomy = null ): int {
		if ( ! isset( $taxonomy ) ) {
			$taxonomy = $this->pointer;
		}

		if ( ! isset( $this->ids[ $taxonomy ] ) ) {
			return 0;
		}

		return count( $this->ids[ $taxonomy ] );
	}

	/**
	 * Refresh object.
	 *
	 * @return void
	 */
	public function refresh() {
		$this->ids     = array();
		$this->links   = array();
		$this->names   = array();
		$this->pointer = $this->taxonomy;
		$this->init();
	}

	/**
	 * Initialize.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	private function init() {
		$sermons = TaxUtils::get_sermons_by_term(
			$this->taxonomy,
			$this->term_id,
			-1
		);

		$this->sermons = new SermonsInfo( $sermons );
		foreach ( $sermons as $sermon ) {
			$this->set_terms( $sermon );
		}
	}

	/**
	 * Set term info.
	 *
	 * @param WP_Post $sermon Sermon post.
	 * @return void
	 * @since 1.0.0
	 */
	private function set_terms( WP_Post $sermon ): void {
		$this->list = array_values( DRPPSM_TAX_MAP );
		foreach ( $this->list as $tax ) {
			$terms = wp_get_post_terms( $sermon->ID, $tax );

			if ( is_wp_error( $terms ) || ! is_array( $terms ) || count( $terms ) === 0 ) {
				continue;
			}

			$term = $terms[0];
			$tid  = $term->term_id;

			if ( ! isset( $this->ids[ $tax ] ) ) {
				$this->ids[ $tax ] = array();
			}

			if ( in_array( $tid, $this->ids[ $tax ] ) ) {
				continue;
			}

			$this->ids[ $tax ][]         = $tid;
			$this->names[ $tax ][ $tid ] = $term->name;
			$link                        = get_term_link( $tid, $tax );
			if ( ! is_wp_error( $link ) ) {
				$this->links[ $tax ][ $tid ] = $link;
			}
		}
	}

	/**
	 * Set object.
	 *
	 * @param int    $term_id Term id.
	 * @param string $taxonomy Taxonomy name.
	 * @return void
	 * @since 1.0.0
	 */
	private function set_term( int $term_id, string $taxonomy ): void {
		$term = get_term( $term_id, $taxonomy );

		if ( is_wp_error( $term ) || ! isset( $term ) ) {
			return;
		}

		if ( is_array( $term ) && count( $term ) !== 0 ) {
			$obj = $term[0];
		}
		if ( is_a( $term, 'WP_Term' ) ) {
			$obj = $term;
		}

		if ( ! isset( $obj ) ) {
			return;
		}

		$this->term = $obj;
	}
}