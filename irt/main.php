<?php

namespace IRT;

/**
 * Tag enhancement class
 *
 * This class enhances tag input interface on admin screen.
 * Original interface mis-respond to IME's character selection.
 *
 * @package IRT
 */
class Main extends Singleton
{

    /**
     * @var string Version
     */
    protected $version = IRT_VERSION;

	/**
	 * Action name used for Ajax and nonce
	 *
	 * @var string
	 */
	private  $action = 'irt_tag_search';

    /**
     * Nonce key name
     *
     * @var string
     */
    private $nonce_key = '_irtnonce';

    /**
     * Constructor
     */
    protected function __construct(){
        // Register scripts
        add_action('init', array($this, 'register_scripts'));
        // Enqueu scripts
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        // Ajax
        add_action('wp_ajax_'.$this->action, array($this, 'ajax'));
        // Filter
        add_filter('plugin_row_meta', array($this, 'filter_plugin_row'), 10, 2);
    }

	/**
	 * Handle Ajax
	 *
	 * @global \wpdb $wpdb
	 */
	public function ajax(){
		/** @var $wpdb \wpdb */
		global $wpdb;
        $return = array();
        try{
            if( !( $taxonomy = get_taxonomy($this->get('taxonomy')) ) ){
                throw new \Exception('No taxonomy');
            }
            if( !wp_verify_nonce($this->get($this->nonce_key), $this->nonce_key) ){
                throw new \Exception('Invalid nonce');
            }
            switch( $this->get('type') ){
                case 'list': // Get default list
                    $terms = implode(', ', array_map( function($term) use ($wpdb) {
                        return $wpdb->prepare('%s', trim($term));
                    }, explode(',', $this->get('terms'))));
                    $sql = <<<EOS
                    SELECT t.term_id AS id, t.name
                    FROM {$wpdb->term_taxonomy} AS tt
                    INNER JOIN {$wpdb->terms} AS t
                    ON t.term_id = tt.term_id
                    WHERE tt.taxonomy = %s
                      AND t.name IN ({$terms})
EOS;
                    $return = $wpdb->get_results($wpdb->prepare($sql, $taxonomy->name));
                    break;
                default: // Search by query
                    $query = '%'.trim($this->get('q')).'%';
                    if( $taxonomy && '%%' != $query ){
                        $sql = <<<EOS
                        SELECT t.term_id AS id, t.name, tt.count
                        FROM {$wpdb->terms} AS t
                        INNER JOIN {$wpdb->term_taxonomy} AS tt
                        ON t.term_id = tt.term_id
                        WHERE tt.taxonomy = %s
                          AND t.name LIKE %s
                        ORDER BY t.name ASC
                        LIMIT 20
EOS;
                        $return = $wpdb->get_results($wpdb->prepare($sql, $taxonomy->name, $query));
                        array_push($return, array(
                            'id' => 0,
                            'name' => '[+] '.sprintf(__('Create new %s', IRT_DOMAIN), $taxonomy->labels->singular_name),
                        ));
                    }
                    break;
            }
        }catch (\Exception $e){

        }

        header('Content-Type: application/json');
        echo json_encode($return);
        exit;
	}

    /**
     * Register required assets
     */
    public function register_scripts(){
        $prefix = WP_DEBUG ? '.min' : '';
        $base = plugin_dir_url(dirname(__FILE__)).'assets';
        // Token input
        wp_register_script('jquery-tokeninput', $base."/js/jquery.tokeninput{$prefix}.js", array('jquery'), '1.6.1');
        // Token CSS
        wp_register_style('jquery-tokeninput-mp6', $base."/css/token-input-mp6.css", null, $this->version);
        // User script
        wp_register_script('irt', $base."/js/irt{$prefix}.js", array('jquery-tokeninput'), $this->version);
        // User style
        wp_register_style('irt', $base."/css/irt.css", array('jquery-tokeninput-mp6'), $this->version);
    }

	/**
	 * Enqueue scripts
     *
     * @param string $page_slug
	 */
	public function admin_enqueue_scripts( $page_slug = '' ){
		if( false !== array_search($page_slug, array('post.php', 'post-new.php')) ){
			wp_enqueue_script('irt');
			wp_localize_script('irt', 'IRT', array(
				'endpoint' => admin_url('admin-ajax.php'),
				'action' => $this->action,
                'nonceKey' => $this->nonce_key,
				'nonceValue' => wp_create_nonce($this->nonce_key),
				'hintText' => __('Input and search.', IRT_DOMAIN),
				'noResultsText' => __('Not found.', IRT_DOMAIN),
				'searchingText' => __('Searching...', IRT_DOMAIN),
			));
			wp_enqueue_style('irt');
		}
	}

    /**
     * Add github link
     *
     * @param array $links
     * @param string $file
     * @return array
     */
    public function filter_plugin_row( array $links, $file){
        if( false !== strpos($file, 'ime-ready-taxonomy') ){
            $links[] = 'Fork me on <a href="https://github.com/hametuha/ime-ready-taxonomy" target="_blank">Github</a>';
        }
        return $links;
    }
}
