<?php
use Core\Classes\Abstracts\ControlMapper;

class VisualConstructorPlugin extends ControlMapper
{
    function __construct() {
        add_action('init', array(&$this, 'init'), 100);
        add_action('init', array(&$this, 'custom_post_type'));
        add_action('add_meta_boxes', array(&$this, 'custom_fields'));
        add_action('save_post', array(&$this, 'save_custom_fields'));
        add_action('product_cat_edit_form_fields', array(&$this, 'product_cat_taxonomy_custom_fields'), 10, 2);
        add_action('edited_product_cat', array(&$this, 'save_taxonomy_custom_fields'), 10, 2);
        add_action('user_register', array(&$this, 'user_registration'), 10, 1);
        add_filter('um_profile_tabs', array(&$this, 'add_custom_profile_tab'), 1000);
        add_action('um_profile_content_settings_default', array(&$this, 'um_profile_content_settings_default'));
        add_action('um_profile_content_orders_default', array(&$this, 'get_pizzeria_orders_html'));
        add_action('um_after_account_general', array(&$this, 'um_account_extra_fields'), 100);
        add_action('um_submit_account_details', array(&$this, 'um_submit_account_details'));

        add_action('admin_menu', array(&$this, 'vconst_admin_settings'));
        add_action('admin_post_save_settings', array(&$this, 'save_settings'));
        add_action('admin_post_save_admin_settings', array(&$this, 'save_admin_settings'));

        add_filter('woocommerce_add_cart_item_data', array(&$this, 'add_pizzeria_to_cart_item'), 10, 2);

        add_shortcode('constructor', array(&$this, 'get_constructor_page'));
        add_shortcode('pizzerias_list', array(&$this, 'get_pizzerias'));

        add_action('rest_api_init',  array(&$this, 'add_endpoint'));
        add_action('wp_enqueue_scripts', array(&$this, 'enqueue_styles'));
        add_filter('single_template', array(&$this, 'pizzeria_template'));
        add_filter('woocommerce_locate_template', array(&$this, 'payments_list'), 10, 3 );

        add_filter('query_vars', array(&$this, 'custom_query_vars_filter'));
        add_filter('rewrite_rules_array', array(&$this, 'add_rewrite_rules'));

        add_filter('woocommerce_checkout_fields' , array(&$this, 'woo_checkout_fields'));
        add_action('woocommerce_checkout_update_order_meta', array(&$this, 'update_order_meta'));

        add_action('woocommerce_checkout_order_review', array(&$this, 'add_pizzeria_field_to_checkout'), 10, 1);
        add_action('woocommerce_checkout_order_processed', array(&$this, 'add_pizzeria_to_order'));
        add_action('woocommerce_before_calculate_totals', array(&$this, 'add_pizzeria_price'), 1000);
        add_filter('wc_order_statuses', array(&$this, 'add_order_statuses'));

        register_activation_hook(__FILE__, array(&$this, 'add_role'));
    }

    /**
     * WP depends
     * */
    function custom_post_type() {
        register_post_type('pizzeria',
            array(
                'labels' => array(
                    'name' => __('Pizzerias'),
                    'singular_name' => __('Pizzeria')
                ),
                'public' => true,
                'has_archive' => true,
                'supports' => array('title', 'thumbnail', 'editor', 'comments', 'author', 'custom-fields'),
                'rewrite' => array('slug' => 'pizzeria')
            )
        );

        register_post_status( 'wc-accepted', array(
            'label'                     => 'Accepted',
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop('Accepted <span class="count">(%s)</span>', 'Accepted <span class="count">(%s)</span>')
        ));
    }

    /**
     * WP depends
     * */
    function custom_fields() {
        add_meta_box('owner', 'Owner',
            array(&$this, 'pizzeria_meta_box'), 'pizzeria', 'normal', 'default');

        add_meta_box('address', 'Address',
            array(&$this, 'pizzeria_meta_box'), 'pizzeria', 'normal', 'default');

        add_meta_box('default', 'Menu Item',
            array(&$this, 'product_meta_box'), 'product', 'normal', 'default');
    }

    /**
     * WP depends
     * */
    function save_custom_fields($post_id) {
        $post = get_post($post_id);

        if($post->post_type == 'pizzeria') {
            update_post_meta($post_id, 'owner', $_POST['owner']);
            update_post_meta($post_id, 'address', $_POST['address']);
        }

        if($post->post_type == 'product')
            update_post_meta($post_id, 'default', $_POST['default']);
    }

    /**
     * WP depends
     * Custom taxonomy fields
     * */
    function product_cat_taxonomy_custom_fields($tag) {
        $t_id = $tag->term_id;
        $term_meta = get_term_meta($t_id);
        $html = '';
        ob_start();
        include_once(PLUGIN_PATH.'templates/taxonomy_layer_field.php');
        $html .= ob_get_contents();
        ob_clean();

        $required = $term_meta['term_meta-required'][0] ? 'checked' : '';
        ob_start();
        include_once(PLUGIN_PATH.'templates/taxonomy_required_field.php');
        $html .= ob_get_contents();
        ob_clean();

        $max1 = $term_meta['term_meta-max-1'][0] ? 'checked' : '';
        ob_start();
        include_once(PLUGIN_PATH.'templates/taxonomy_max_field.php');
        $html .= ob_get_contents();
        ob_clean();
        echo $html;
    }

    /**
     * WP depends
     * */
    function save_taxonomy_custom_fields($term_id) {
        if(isset($_POST['term_meta'])) {
            $cat_keys = array_keys($_POST['term_meta']);
            foreach ($cat_keys as $key){
                if (isset($_POST['term_meta'][$key])){
                    update_term_meta($term_id, 'term_meta-'.$key, $_POST['term_meta'][$key]);
                }
            }
        }
    }

    /**
     * Ultimate user depends
     * Add pizzeria tabs
     * @param $tabs array
     * @return array
     * */
    function add_custom_profile_tab($tabs) {
        $tabs['settings'] = array(
            'name' => 'Institution settings',
            'icon' => 'um-faicon-pencil',
        );

        $tabs['orders'] = array(
            'name' => 'Institution orders',
            'icon' => 'um-faicon-pencil',
        );

        return $tabs;
    }

    /**
     * Ultimate user depends
     * Add pizzeria settings page to Ultimate memeber
     * */
    function um_profile_content_settings_default($args) {
        $post = $this->get_pizzeria_page();
        $visual_constructor = $this;
        include_once(PLUGIN_PATH.'templates/pizzeria_settings_form.php');
    }

    /**
     * Ultimate user depends
     * */
    function um_account_extra_fields() {
        $custom_fields = [
            "billing_phone" => "Phone Number (XXX-XXX-XXXX)",
            "billing_address_1" => "Address",
            "billing_city" => "City",
            "billing_state" => "State",
        ];

        foreach ($custom_fields as $key => $value) {

            $fields[$key] = array(
                'title' => $value,
                'metakey' => $key,
                'type' => 'select',
                'label' => $value,
            );

            apply_filters('um_account_secure_fields', $fields, 'general' );

            $field_value = get_user_meta(um_user('ID'), $key, true) ? : '';

            $html = '<div class="um-field um-field-'.$key.'" data-key="'.$key.'">
                    <div class="um-field-label">
                    <label for="'.$key.'">'.$value.'</label>
                    <div class="um-clear"></div>
                    </div>
                    <div class="um-field-area">
                    <input class="um-form-field valid "
                    type="text" name="'.$key.'"
                    id="'.$key.'" value="'.$field_value.'"
                    placeholder=""
                    data-validate="" data-key="'.$key.'">
                    </div>
                    </div>';

            echo $html;
        }
    }

    /**
     * Ultimate user depends
     * */
    function um_submit_account_details() {
        if(isset($_POST['billing_address_1']))
            update_user_meta(get_current_user_id(), 'billing_address_1', $_POST['billing_address_1']);

        if(isset($_POST['billing_phone']))
            update_user_meta(get_current_user_id(), 'billing_phone', $_POST['billing_phone']);

        if(isset($_POST['billing_city']))
            update_user_meta(get_current_user_id(), 'billing_city', $_POST['billing_city']);

        if(isset($_POST['billing_state']))
            update_user_meta(get_current_user_id(), 'billing_state', $_POST['billing_state']);
    }

    /**
     * WP depends
     * @return void
     * */
    function vconst_admin_settings() {
        add_submenu_page('options-general.php', 'Visual Constructor Options', 'Visual Constructor', 'administrator', 'vconst-options', array(&$this, 'admin_settings_page'));
    }

    /**
     * WP depends
     * Add pizzeria to cart
     * @return array
     * */
    function add_pizzeria_to_cart_item($cart_item_data, $product_id) {
        $params = (array)json_decode(file_get_contents('php://input'), true);
        $cart_item_data['pizzeria'] = $params['pizzeria'];
        return $cart_item_data;
    }

    /**
     * WP depends
     * */
    function enqueue_styles() {
        wp_enqueue_script('wp-api');
        wp_localize_script( 'wp-api', 'wpApiSettings', array(
            'root' => esc_url_raw( rest_url() ),
            'nonce' => wp_create_nonce( 'wp_rest' )
        ) );
        echo '<script type="text/javascript">var api_url = "'. $this->core->api_url .'"</script>';
        wp_enqueue_script('jquery');
        wp_enqueue_style('bootstrap-style', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');
        wp_enqueue_style('datetimepicker-style', PLUGIN_URL.'assets/datetimepicker/bootstrap-datetimepicker.min.css');
        wp_enqueue_script('datetimepicker-script', PLUGIN_URL.'assets/datetimepicker/bootstrap-datetimepicker.min.js');

        wp_enqueue_style('vconst-style',  PLUGIN_URL.'Core/assets/css/style.css');
        wp_enqueue_style('vconst-custom-style',  PLUGIN_URL.'Core/assets/css/custom_style.css');
        wp_enqueue_style('vconst-plugin-style',  PLUGIN_URL.'assets/css/style.css');
        wp_enqueue_script('vconst-plugin-script',  PLUGIN_URL.'assets/js/main.js');
        wp_enqueue_script('vconst-core-script',  PLUGIN_URL.'Core/assets/js/main.js');
    }

    /**
     * WP depends
     * Pizzeria custom templates/pages
     * */
    function pizzeria_template($single) {
        global $wp_query, $post;
        $page = get_query_var('vconst-page');
        if ($post->post_type == 'pizzeria' ) {
            if($page == PIZZERIA_SETTINGS_PAGE && file_exists(PLUGIN_PATH.'templates/pizzeria_settings.php')) {
                return PLUGIN_PATH.'templates/pizzeria_settings.php';
            } else if($page == PIZZERIA_CONSTRUCTOR_PAGE && file_exists(PLUGIN_PATH.'templates/constructor.php')) {
                return PLUGIN_PATH.'templates/constructor.php';
            } else if($page == PIZZERIA_ORDERS_PAGE && file_exists(PLUGIN_PATH.'templates/orders.php')) {
                return PLUGIN_PATH.'templates/orders.php';
            }
        }

        return $single;
    }

    /**
     * WP depends
     * Checkout payments list, depends of pizzeria
     * @param $template string
     * @param $template_name string
     * */
    function payments_list($template, $template_name, $template_path) {
        if ($template_name == 'checkout/payment.php') {
            $template = PLUGIN_PATH.'templates/payment.php';
        }
        return $template;
    }

    function custom_query_vars_filter($vars) {
        $vars[] = 'vconst-page';
        return $vars;
    }

    /**
     * WP depends
     * @param $aRules array
     **/
    function add_rewrite_rules($aRules) {
        $aNewRules = array(
            'pizzeria/(.*)/(.*)/(.*)?$' => 'index.php?pizzeria=$matches[1]&vconst-page=$matches[2]&order_id=$matches[3]',
            'pizzeria/(.*)/(.*)?$' => 'index.php?pizzeria=$matches[1]&vconst-page=$matches[2]'
        );
        $aRules = $aNewRules + $aRules;
        return $aRules;
    }

    /**
     * WOO depends
     * Add custom billing fields
     * @return array
     * */
    function woo_checkout_fields($fields) {
        $fields['billing']['delivery'] = array(
            'type' => 'select',
            "label"=>"Delivery",
            "required"=>true,
            "class"=>array('form-row-wide', 'address-field', 'validate-required', 'validate-state', 'woocommerce-validated'),
            "autocomplete"=>"given-name",
            "autofocus"=>false,
            "priority"=>130,
            'options' => array(
                'pickup' => 'I’ll pick it up myself',
                'deliver' => 'Deliver'
            )
        );

        $fields['billing']['when'] = array(
            'type' => 'text',
            "label"=>"When",
            "required"=>true,
            "class"=>array("form-row-wide"),
            "autocomplete"=>"given-name",
            "autofocus"=>false,
            "priority"=>130,
        );

        return $fields;
    }

    /**
     * WP depends
     * @return array
     * */
    function add_pizzeria_field_to_checkout() {
        $pizzeria_id = $this->get_pizzeria_from_cart();
        echo "<input type='hidden' name='pizzeria_id' value='$pizzeria_id'/>";
    }

    /**
     * WP depends
     * Assign pizzeria to order
     * @return array
     * */
    function add_pizzeria_to_order($order_id) {
        $orders = get_post_meta($_POST['pizzeria_id'], 'orders', true);
        $orders[$order_id] = ORDER_PENDING;
        update_post_meta($_POST['pizzeria_id'], 'orders', $orders);
    }

    /**
     * WP depends
     * */
    function add_order_statuses($order_statuses) {
        $order_statuses['wc-accepted'] = 'Accepted';
        return $order_statuses;
    }

    /**
     * WOO depends
     * Save custom billing fields
     * @param $order_id
     * @return void
     * */
    function update_order_meta($order_id) {
        if(!empty($_POST['delivery'])) {
            update_post_meta($order_id, '_billing_delivery', sanitize_text_field($_POST['delivery']));
        }

        if(!empty($_POST['when'])) {
            update_post_meta($order_id, '_billing_when', sanitize_text_field($_POST['when']));
        }
    }

    /**
     * WP depends
     * */
    function admin_enqueue_styles() {
        wp_enqueue_script('jquery');
        wp_enqueue_style('bootstrap-style', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');
        wp_enqueue_script('jquery-ui', 'https://code.jquery.com/ui/1.12.0/jquery-ui.min.js');
    }

    /**
     * WP depends
     * */
    function add_role() { //Pizzeria owner
        add_role( 'owner', 'Owner', array('edit_posts' => true, 'delete_posts' => false));
    }

    /**
     * WP depends
     * Return pizzeria id if cart not empty (User able make order only form one pizzeria per order)
     * @return integer
     * */
    function get_pizzeria_from_cart() {
        $pizzeria_id = null;
        if(!WC()->cart)
            return null;

        foreach(WC()->cart->get_cart() as $cart_item) {
            if(isset($cart_item['pizzeria'])) {
                $pizzeria_id = $cart_item['pizzeria'];
                break;
            }
        }
        return $pizzeria_id;
    }

    /**
     * WP depends
     * @return void
     * */
    function add_endpoint() {
        $this->init();
        register_rest_route('vconst/v1', 'get-constructor-data',array(
            'methods'  => 'GET',
            'callback' => array(&$this, 'get_constructor_data')
        ));

        register_rest_route('vconst/v1', 'add-to-cart',array(
            'methods'  => 'POST',
            'callback' => array(&$this, 'add_to_cart')
        ));

        register_rest_route('vconst/v1', 'get-pizzerias',array(
            'methods'  => 'POST',
            'callback' => array(&$this, 'get_pizzerias_list')
        ));

        register_rest_route('vconst/v1', 'accept-order',array(
            'methods'  => 'POST',
            'callback' => array(&$this, 'accept_order')
        ));

        register_rest_route('vconst/v1', 'deny-order',array(
            'methods'  => 'POST',
            'callback' => array(&$this, 'deny_order')
        ));

        register_rest_route('vconst/v1', 'order-offer',array(
            'methods'  => 'POST',
            'callback' => array(&$this, 'order_offer')
        ));

        register_rest_route('vconst/v1', 'done-order',array(
            'methods'  => 'POST',
            'callback' => array(&$this, 'done_order')
        ));
    }

    /**
     * WP depends
     * Add to cart endpoint
     * @return void
     * */
    function add_to_cart() {
        $params = (array)json_decode(file_get_contents('php://input'), true);

        $pizzeria = $params['pizzeria'] = $this->get_pizzeria(get_post($params['pizzeria']));

        $params = prepare_data_for_cart($params, $this->main_menu, $this->side_menu, $pizzeria);
        $product = get_page_by_title($params['title'], OBJECT, 'product');

        if(is_null($product) || get_post_meta($product->ID, 'ingradients', true) !== $params['ingradients']) {
            $image_title = $this->core->create_order_img($params['image']);
            unset($params['image']);
            $post_id = $this->create_assebled_product($params['title'], $params['ingradients'], $params['price_val'], wp_upload_dir()['baseurl'] . '/orders_img/' . $image_title, wp_upload_dir()['basedir'] . '/orders_img/' . $image_title);
        } else {
            $post_id = $product->ID;
            if($params['last_edit_ts'] > strtotime($product->post_modified)) {
                $image_title = $this->core->create_order_img($params['image']);
                unset($params['image']);
                $this->update_assembled_product($post_id, $params['title'], $params['price_val'], wp_upload_dir()['baseurl'] . '/orders_img/' . $image_title, wp_upload_dir()['basedir'] . '/orders_img/' . $image_title);
            }
        }
        WC()->cart->add_to_cart($post_id);
        foreach($params['side_order'] as $side_prod) {
            WC()->cart->add_to_cart($side_prod['id'], $side_prod['amount']);
        }
        exit;
    }

    /**
     * WP depends
     * Set Pizzeria product price
     * @return void
     * */
    function add_pizzeria_price() {
        $pizzeria = $this->get_pizzeria(get_post($this->get_pizzeria_from_cart()));
        foreach(WC()->cart->get_cart() as $key=> $item) {
            $price = 0;
            if(is_array(get_post_meta($item['data']->get_id(), 'ingradients', true)))
                $price = $pizzeria->get_product_price(get_post_meta($item['data']->get_id(), 'ingradients', true));
            else
                $price = $pizzeria->get_side_product_price($item['data']->get_id());

            $item['data']->set_price($price);
        }
    }

    /**
     * WP depends
     * Create Assembled product
     * @return integer
     * */
    function create_assebled_product($title, $ingradients, $price_val, $image_url, $image_path) {
        $post_id = wp_insert_post( array(
            'post_title' => $title,
            'post_content' => ucfirst(str_replace('-', ' ', ASSEMBLED_PRODUCT_SLUG)),
            'post_status' => 'publish',
            'post_type' => "product",
        ));

        if(is_wp_error($post_id)) throw new Exception($post_id->get_error_message());

        wp_set_object_terms($post_id, ASSEMBLED_PRODUCT_SLUG, 'product_cat');
        update_post_meta($post_id, 'ingradients', $ingradients);

        update_post_meta( $post_id, '_visibility', 'hidden' );
        update_post_meta( $post_id, 'total_sales', '0' );
        update_post_meta( $post_id, '_downloadable', 'no' );
        update_post_meta( $post_id, '_regular_price', $price_val );
        update_post_meta( $post_id, '_sale_price', $price_val );
        update_post_meta( $post_id, '_featured', 'no' );
        update_post_meta( $post_id, '_price', $price_val );
        $this->assing_image_to_product($post_id, $image_url, $image_path, $title);
        return $post_id;
    }

    /**
     * WP depends
     * Update Assembled product
     * @return void
     * */
    function update_assembled_product($post_id, $title, $price_val, $image_url, $image_path) {
        update_post_meta( $post_id, '_visibility', 'hidden' );
        update_post_meta( $post_id, 'total_sales', '0' );
        update_post_meta( $post_id, '_downloadable', 'no' );
        update_post_meta( $post_id, '_regular_price', $price_val );
        update_post_meta( $post_id, '_sale_price', $price_val );
        update_post_meta( $post_id, '_featured', 'no' );
        update_post_meta( $post_id, '_price', $price_val );
        $this->assing_image_to_product($post_id, $image_url, $image_path, $title);
    }

    /**
     * WP depends
     * */
    function assing_image_to_product( $post_id, $image, $image_dir, $title ) {
        require_once( ABSPATH . 'wp-load.php' ); // WordPress loader
        require_once( ABSPATH . 'wp-includes/pluggable.php' ); // WordPress loader
        require_once( ABSPATH . 'wp-admin/includes/image.php' ); // WordPress loader

        $wp_filetype = wp_check_filetype( $image, NULL );

        $attachment  = array(
            'guid'           => $image,
            'post_mime_type' => $wp_filetype['type'],
            'post_title'     => $title,
            'post_content'   => '',
            'post_status'    => 'inherit'
        );
        $attach_id   = wp_insert_attachment( $attachment, $image, $post_id );
        $attach_data = wp_generate_attachment_metadata( $attach_id, $image_dir );
        wp_update_attachment_metadata( $attach_id, $attach_data );
        set_post_thumbnail( $post_id, $attach_id );
    }

    /**
     * WP depends
     * Create pizzeria post on owner registration
     * @param $user_id integer
     * */
    function user_registration($user_id) {
        if(isset($_POST['role']) && $_POST['role'] == 'owner')
            $this->create_pizzeria($user_id);
    }

    /**
     * UM depends
     * Create pizzeria page
     * @param $user_id integer
     * @return void
     * */
    function create_pizzeria($user_id) {
        $args = array(
            'post_title' => $_POST['user_login-'.$_POST['form_id']],
            'post_author' => 1,
            'post_type' => 'pizzeria'
        );

        $post_id = wp_insert_post($args);
        update_post_meta($post_id, 'owner', $user_id);
    }

    /**
     * WP depends
     * Get pizzerias
     * @return array
     * */
    function get_pizzerias() {
        return get_posts(array('post_type' => 'pizzeria', 'posts_per_page' => -1));
    }

    /**
     * WP depends
     * @return void
     * */
    function init() {
        $main_menu = get_posts(array('post_type' => 'product', 'posts_per_page' => -1, 'tax_query' => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => MAIN_MENU_SLUG,
            ),
        )));
        $side_menu = get_posts(array('post_type' => 'product', 'posts_per_page' => -1, 'tax_query' => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => SIDE_MENU_SLUG,
            ),
        )));

        $intolerance = get_terms('product_tag',array(
            'hide_empty' => false,
        ));

        $pizzeria = null;
        if(!is_null($this->get_pizzeria_from_cart()))
            $pizzeria = get_post($this->get_pizzeria_from_cart());

        $this->init_vconst($main_menu, $side_menu, $intolerance, wp_upload_dir()['basedir'],  $pizzeria);
    }

    /**
     * WP depends
     * Get pizzeria name from url
     * @param $pizzeria_id int
     * @return WP_Post
     * */
    function get_pizzeria_page($pizzeria_id=null) {
        if(is_null($pizzeria_id)) {
            $page = str_replace('/pizzeria/', '', $_SERVER['REQUEST_URI']);
            $page = str_replace(PIZZERIA_CONSTRUCTOR_PAGE, '', $page);
            $page = str_replace(PIZZERIA_SETTINGS_PAGE, '', $page);
            $page = str_replace(PIZZERIA_ORDERS_PAGE, '', $page);
            $page = str_replace(SUB_FOLDER, '', $page);
            $page = str_replace('user', '', $page);
            $page = str_replace('?profiletab=', '', $page);
            $page = str_replace('/', '', $page);
            return get_page_by_path($page, OBJECT, 'pizzeria');
        } else {
            return get_post($pizzeria_id);
        }
    }

    /**
     * WP depends
     * Get order data
     * @return WP_Post
     * */
    function get_order_data($order_id) {
        return get_post($order_id);
    }

    /**
     * WP depends
     * Get order data
     * @return array
     * */
    function get_order_items($order_id) {
        return wc_get_order($order_id)->get_items();
    }

    /**
     * WP depends
     * Save Pizzeria settings page
     * @return void
     * */
    function save_settings() {
        if(!current_user_can('administrator') && ((int)get_post_meta($_POST['pizzeria'], 'owner', true) !== get_current_user_id())) {
            return false;
        }

        update_post_meta($_POST['pizzeria'], 'constructor_settings', $_POST);
        wp_safe_redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * WP depends
     * Super admin config page
     * @return void
     * */
    function admin_settings_page() {
        $this->admin_enqueue_styles();
        $saved_styles = get_option('saved_style');
        include_once(PLUGIN_PATH.'templates/admin_settings.php');
    }

    /**
     * WP depends
     * Save Super admin config page
     * @return void
     * */
    function save_admin_settings() {
        parent::save_admin_settings();
        update_option('saved_style', $_POST);
        wp_safe_redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * WP depends
     * Done order endpoint
     * */
    function done_order() {
        if(!current_user_can('administrator') && ((int)get_post_meta($_POST['pizzeria_id'], 'owner', true) !== get_current_user_id())) {
            return false;
        }

        $order = wc_get_order($_POST['order_id']);
        $pizzeria = $this->get_pizzeria(get_post($_POST['pizzeria_id']));

        $orders = get_post_meta($_POST['pizzeria_id'], 'orders', true);
        $pizzeria->orders[$_POST['order_id']] = $orders[$_POST['order_id']] = ORDER_DELIVERED;
        update_post_meta($_POST['pizzeria_id'], 'orders', $orders);

        $order->update_status('wc-completed', $pizzeria->title.' completed order #'.$_POST['order_id']);

        $order_page = get_post($_POST['order_id']);
        echo $this->get_pizzeria_order($order_page, $this->get_order_items($_POST['order_id']), $pizzeria->get_pizzeria_order_status($_POST['order_id']))->get_order_html();
        exit;
    }

    /**
     * WP depends
     * Offer order endpoint
     * */
    function order_offer() {
        if(!current_user_can('administrator') && ((int)get_post_meta($_POST['pizzeria_id'], 'owner', true) !== get_current_user_id())) {
            return false;
        }

        $order_id = $_POST['order_id'];
        unset($_POST['order_id']);
        update_post_meta($order_id, 'offer', $_POST);

        $orders = get_post_meta($_POST['pizzeria_id'], 'orders', true);
        $orders[$order_id] = ORDER_OFFER;
        update_post_meta($_POST['pizzeria_id'], 'orders', $orders);

        $order = $this->get_order($order_id);
        $order->send_offer();
        exit;
    }

    /**
     * WP depends
     * Accept order endpoint
     * */
    function accept_order() {
        if(!current_user_can('administrator') && ((int)get_post_meta($_POST['pizzeria_id'], 'owner', true) !== get_current_user_id())) {
            return false;
        }

        $order = wc_get_order($_POST['order_id']);
        $pizzeria = $this->get_pizzeria(get_post($_POST['pizzeria_id']));

        $orders = get_post_meta($_POST['pizzeria_id'], 'orders', true);
        $pizzeria->orders[$_POST['order_id']] = $orders[$_POST['order_id']] = ORDER_ACCEPTED;
        update_post_meta($_POST['pizzeria_id'], 'orders', $orders);

        $order->update_status('wc-accepted', $pizzeria->title.' accepted order #'.$_POST['order_id']);

        $order_page = get_post($_POST['order_id']);
        echo $this->get_pizzeria_order($order_page, $this->get_order_items($_POST['order_id']), $pizzeria->get_pizzeria_order_status($_POST['order_id']), $_POST['pizzeria_id'])->get_order_html();
        exit;
    }

    /**
     * WP depends
     * Deny order endpoint
     * */
    function deny_order() {
        if(!current_user_can('administrator') && ((int)get_post_meta($_POST['pizzeria_id'], 'owner', true) !== get_current_user_id())) {
            return false;
        }

        $order = wc_get_order($_POST['order_id']);
        $pizzeria = $this->get_pizzeria(get_post($_POST['pizzeria_id']));

        $orders = get_post_meta($_POST['pizzeria_id'], 'orders', true);
        $pizzeria->orders[$_POST['order_id']] = $orders[$_POST['order_id']] = ORDER_DENY;
        update_post_meta($_POST['pizzeria_id'], 'orders', $orders);

        $order->update_status('wc-on-hold', $pizzeria->title.' deny order #'.$_POST['order_id'].', '. $_POST['reason']);

        $order_page = get_post($_POST['order_id']);
        echo $this->get_pizzeria_order($order_page, $this->get_order_items($_POST['order_id']), $pizzeria->get_pizzeria_order_status($_POST['order_id']))->get_order_html();
        exit;
    }

    /**
     * WP depends
     * Get WP order page
     * */
    function get_order_page($order_id) {
        return get_post($order_id);
    }
}