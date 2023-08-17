<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementor
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

define('HELLO_ELEMENTOR_VERSION', '2.8.1');

if (!isset($content_width)) {
    $content_width = 800; // Pixels.
}

if (!function_exists('hello_elementor_setup')) {
    /**
     * Set up theme support.
     *
     * @return void
     */
    function hello_elementor_setup()
    {
        if (is_admin()) {
            hello_maybe_update_theme_version_in_db();
        }

        if (apply_filters('hello_elementor_register_menus', true)) {
            register_nav_menus(['menu-1' => esc_html__('Header', 'hello-elementor')]);
            register_nav_menus(['menu-2' => esc_html__('Footer', 'hello-elementor')]);
        }

        if (apply_filters('hello_elementor_post_type_support', true)) {
            add_post_type_support('page', 'excerpt');
        }

        if (apply_filters('hello_elementor_add_theme_support', true)) {
            add_theme_support('post-thumbnails');
            add_theme_support('automatic-feed-links');
            add_theme_support('title-tag');
            add_theme_support(
                'html5',
                [
                    'search-form',
                    'comment-form',
                    'comment-list',
                    'gallery',
                    'caption',
                    'script',
                    'style',
                ]
            );
            add_theme_support(
                'custom-logo',
                [
                    'height' => 100,
                    'width' => 350,
                    'flex-height' => true,
                    'flex-width' => true,
                ]
            );

            /*
             * Editor Style.
             */
            add_editor_style('classic-editor.css');

            /*
             * Gutenberg wide images.
             */
            add_theme_support('align-wide');

            /*
             * WooCommerce.
             */
            if (apply_filters('hello_elementor_add_woocommerce_support', true)) {
                // WooCommerce in general.
                add_theme_support('woocommerce');
                // Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).
                // zoom.
                add_theme_support('wc-product-gallery-zoom');
                // lightbox.
                add_theme_support('wc-product-gallery-lightbox');
                // swipe.
                add_theme_support('wc-product-gallery-slider');
            }
        }
    }
}
add_action('after_setup_theme', 'hello_elementor_setup');

function hello_maybe_update_theme_version_in_db()
{
    $theme_version_option_name = 'hello_theme_version';
    // The theme version saved in the database.
    $hello_theme_db_version = get_option($theme_version_option_name);

    // If the 'hello_theme_version' option does not exist in the DB, or the version needs to be updated, do the update.
    if (!$hello_theme_db_version || version_compare($hello_theme_db_version, HELLO_ELEMENTOR_VERSION, '<')) {
        update_option($theme_version_option_name, HELLO_ELEMENTOR_VERSION);
    }
}

if (!function_exists('hello_elementor_scripts_styles')) {
    /**
     * Theme Scripts & Styles.
     *
     * @return void
     */
    function hello_elementor_scripts_styles()
    {
        $min_suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

        if (apply_filters('hello_elementor_enqueue_style', true)) {
            wp_enqueue_style(
                'hello-elementor',
                get_template_directory_uri() . '/style' . $min_suffix . '.css',
                [],
                HELLO_ELEMENTOR_VERSION
            );
        }

        if (apply_filters('hello_elementor_enqueue_theme_style', true)) {
            wp_enqueue_style(
                'hello-elementor-theme-style',
                get_template_directory_uri() . '/theme' . $min_suffix . '.css',
                [],
                HELLO_ELEMENTOR_VERSION
            );
        }
    }
}
add_action('wp_enqueue_scripts', 'hello_elementor_scripts_styles');

if (!function_exists('hello_elementor_register_elementor_locations')) {
    /**
     * Register Elementor Locations.
     *
     * @param ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager theme manager.
     *
     * @return void
     */
    function hello_elementor_register_elementor_locations($elementor_theme_manager)
    {
        if (apply_filters('hello_elementor_register_elementor_locations', true)) {
            $elementor_theme_manager->register_all_core_location();
        }
    }
}
add_action('elementor/theme/register_locations', 'hello_elementor_register_elementor_locations');

if (!function_exists('hello_elementor_content_width')) {
    /**
     * Set default content width.
     *
     * @return void
     */
    function hello_elementor_content_width()
    {
        $GLOBALS['content_width'] = apply_filters('hello_elementor_content_width', 800);
    }
}
add_action('after_setup_theme', 'hello_elementor_content_width', 0);

if (is_admin()) {
    require get_template_directory() . '/includes/admin-functions.php';
}

/**
 * If Elementor is installed and active, we can load the Elementor-specific Settings & Features
 */

// Allow active/inactive via the Experiments
require get_template_directory() . '/includes/elementor-functions.php';

/**
 * Include customizer registration functions
 */
function hello_register_customizer_functions()
{
    if (is_customize_preview()) {
        require get_template_directory() . '/includes/customizer-functions.php';
    }
}
add_action('init', 'hello_register_customizer_functions');

if (!function_exists('hello_elementor_check_hide_title')) {
    /**
     * Check hide title.
     *
     * @param bool $val default value.
     *
     * @return bool
     */
    function hello_elementor_check_hide_title($val)
    {
        if (defined('ELEMENTOR_VERSION')) {
            $current_doc = Elementor\Plugin::instance()->documents->get(get_the_ID());
            if ($current_doc && 'yes' === $current_doc->get_settings('hide_title')) {
                $val = false;
            }
        }
        return $val;
    }
}
add_filter('hello_elementor_page_title', 'hello_elementor_check_hide_title');

if (!function_exists('hello_elementor_add_description_meta_tag')) {
    /**
     * Add description meta tag with excerpt text.
     *
     * @return void
     */
    function hello_elementor_add_description_meta_tag()
    {
        $post = get_queried_object();

        if (is_singular() && !empty($post->post_excerpt)) {
            echo '<meta name="description" content="' . esc_attr(wp_strip_all_tags($post->post_excerpt)) . '">' . "\n";
        }
    }
}
add_action('wp_head', 'hello_elementor_add_description_meta_tag');

/**
 * BC:
 * In v2.7.0 the theme removed the `hello_elementor_body_open()` from `header.php` replacing it with `wp_body_open()`.
 * The following code prevents fatal errors in child themes that still use this function.
 */
if (!function_exists('hello_elementor_body_open')) {
    function hello_elementor_body_open()
    {
        wp_body_open();
    }
}

function um_033021_logout_user_links($args)
{
    ?>

    <li>
        <a
            href="<?php echo esc_url(add_query_arg('redirect_to', UM()->permalinks()->get_current_url(true), um_get_core_page('logout'))); ?>">
            <?php _e('Logout', 'ultimate-member'); ?>
        </a>
    </li>

    <?php
}

remove_action('um_logout_user_links', 'um_logout_user_links', 100);
add_action('um_logout_user_links', 'um_033021_logout_user_links', 100);

function my_acf_add_local_field_groups()
{
    // Define the field group based on the provided JSON data
    acf_add_local_field_group(
        array(
            'key' => 'group_64d682486664c',
            // Change the key as needed
            'title' => 'Opportunity input',
            'fields' => array(
                array(
                    'key' => 'field_notice_id',
                    'label' => 'Notice ID',
                    'name' => 'notice_id',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_notice_solicitationNumber',
                    'label' => 'Solicitation Number',
                    'name' => 'solicitationNumber',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_notice_fullParentPathName',
                    'label' => 'Full Parent Path Name',
                    'name' => 'fullParentPathName',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_notice_fullParentPathCode',
                    'label' => 'Full Parent Path Code',
                    'name' => 'fullParentPathCode',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_notice_postedDate',
                    'label' => 'Posted Date',
                    'name' => 'postedDate',
                    'type' => 'date_picker',
                ),
                array(
                    'key' => 'field_notice_type',
                    'label' => 'Type',
                    'name' => 'type',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_notice_baseType',
                    'label' => 'Base Type',
                    'name' => 'baseType',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_notice_archiveType',
                    'label' => 'Archive Type',
                    'name' => 'archiveType',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_notice_archiveDate',
                    'label' => 'Archive Date',
                    'name' => 'archiveDate',
                    'type' => 'date_picker',
                ),
                array(
                    'key' => 'field_notice_typeOfSetAsideDescription',
                    'label' => 'Type of Set Aside Description',
                    'name' => 'typeOfSetAsideDescription',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_notice_typeOfSetAside',
                    'label' => 'Type of Set Aside',
                    'name' => 'typeOfSetAside',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_notice_responseDeadLine',
                    'label' => 'Response Deadline',
                    'name' => 'responseDeadLine',
                    'type' => 'date_time_picker',
                ),
                array(
                    'key' => 'field_notice_naicsCode',
                    'label' => 'NAICS Code',
                    'name' => 'naicsCode',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_naics_codes',
                    'label' => 'NAICS Codes',
                    'name' => 'naicsCodes',
                    'type' => 'repeater',
                    'layout' => 'table',
                    // Display the repeater as a table
                    'sub_fields' => array(
                        array(
                            'key' => 'field_naics_code',
                            'label' => 'NAICS Code',
                            'name' => 'naicsCode',
                            'type' => 'text',
                        ),
                    ),
                ),
                array(
                    'key' => 'field_classification_code',
                    'label' => 'Classification Code',
                    'name' => 'classification_code',
                    'type' => 'text',
                    // Use the text field type
                ),
                array(
                    'key' => 'field_active',
                    'label' => 'Active',
                    'name' => 'active',
                    'type' => 'radio',
                    // Use the radio field type for Yes/No options
                    'choices' => array(
                        'yes' => 'Yes',
                        'no' => 'No',
                    ),
                    'layout' => 'horizontal',
                    // Display the radio options horizontally
                ),
                array(
                    'key' => 'field_award',
                    'label' => 'Award',
                    'name' => 'award',
                    'type' => 'group',
                    // Use the "group" field type for the "award" data
                    'sub_fields' => array(
                        array(
                            'key' => 'field_award_date',
                            'label' => 'Date',
                            'name' => 'date',
                            'type' => 'date_picker',
                            // Use the appropriate field type for the date
                        ),
                        array(
                            'key' => 'field_award_number',
                            'label' => 'Number',
                            'name' => 'number',
                            'type' => 'text',
                        ),
                        array(
                            'key' => 'field_award_amount',
                            'label' => 'Amount',
                            'name' => 'amount',
                            'type' => 'text',
                        ),
                        array(
                            'key' => 'field_award_awardee',
                            'label' => 'Awardee',
                            'name' => 'awardee',
                            'type' => 'group',
                            // Use the "group" field type for the "awardee" data
                            'sub_fields' => array(
                                array(
                                    'key' => 'field_awardee_name',
                                    'label' => 'Name',
                                    'name' => 'name',
                                    'type' => 'text',
                                ),
                                array(
                                    'key' => 'field_awardee_ueisam',
                                    'label' => 'UEI SAM',
                                    'name' => 'ueiSAM',
                                    'type' => 'text',
                                ),
                                array(
                                    'key' => 'field_awardee_cagecode',
                                    'label' => 'CAGE Code',
                                    'name' => 'cageCode',
                                    'type' => 'text',
                                ),
                            ),
                        ),
                    ),
                ),
                array(
                    'key' => 'field_point_of_contact',
                    'label' => 'Point of Contact',
                    'name' => 'point_of_contact',
                    'type' => 'repeater',
                    // This is a repeater field (array)
                    'sub_fields' => array(
                        array(
                            'key' => 'field_poc_type',
                            'label' => 'Type',
                            'name' => 'type',
                            'type' => 'text',
                        ),
                        array(
                            'key' => 'field_poc_fullName',
                            'label' => 'Full Name',
                            'name' => 'fullName',
                            'type' => 'text',
                        ),
                        array(
                            'key' => 'field_poc_title',
                            'label' => 'Title',
                            'name' => 'title',
                            'type' => 'text',
                        ),
                        array(
                            'key' => 'field_poc_email',
                            'label' => 'Email',
                            'name' => 'email',
                            'type' => 'email',
                        ),
                        array(
                            'key' => 'field_poc_phone',
                            'label' => 'Phone',
                            'name' => 'phone',
                            'type' => 'text',
                        ),
                        array(
                            'key' => 'field_poc_fax',
                            'label' => 'Fax',
                            'name' => 'fax',
                            'type' => 'text',
                        ),
                    ),
                    'layout' => 'table',
                    // Choose the layout you prefer
                ),
                array(
                    'key' => 'field_description',
                    'label' => 'Description URL',
                    'name' => 'description_url',
                    'type' => 'url',
                    // Use the URL field type
                ),
                array(
                    'key' => 'field_organization_type',
                    'label' => 'Organization Type',
                    'name' => 'organization_type',
                    'type' => 'text',
                    // Use the text field type
                ),
                array(
                    'key' => 'field_office_address',
                    'label' => 'Office Address',
                    'name' => 'office_address',
                    'type' => 'group',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_zipcode_office',
                            'label' => 'Zip Code',
                            'name' => 'zipcode',
                            'type' => 'text',
                        ),
                        array(
                            'key' => 'field_city_office',
                            'label' => 'City',
                            'name' => 'city',
                            'type' => 'text',
                        ),
                        array(
                            'key' => 'field_country_code_office',
                            'label' => 'Country Code',
                            'name' => 'country_code',
                            'type' => 'text',
                        ),
                        array(
                            'key' => 'field_state_office',
                            'label' => 'State',
                            'name' => 'state',
                            'type' => 'text',
                        ),
                    ),
                ),
                array(
                    'key' => 'field_place_of_performance',
                    'label' => 'Place of Performance',
                    'name' => 'place_of_performance',
                    'type' => 'group',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_street_address_performance',
                            'label' => 'Street Address',
                            'name' => 'street_address',
                            'type' => 'text',
                        ),
                        array(
                            'key' => 'field_street_address2_performance',
                            'label' => 'Street Address 2',
                            'name' => 'street_address2',
                            'type' => 'text',
                        ),
                        array(
                            'key' => 'field_zip_performance',
                            'label' => 'Zip',
                            'name' => 'zip',
                            'type' => 'text',
                        ),
                        array(
                            'key' => 'field_city_performance',
                            'label' => 'City',
                            'name' => 'city',
                            'type' => 'group',
                            // Remove 'layout' => 'table'
                            'sub_fields' => array(
                                array(
                                    'key' => 'field_city_code_performance',
                                    'label' => 'Code',
                                    'name' => 'code',
                                    'type' => 'text',
                                ),
                                array(
                                    'key' => 'field_city_name_performance',
                                    'label' => 'Name',
                                    'name' => 'name',
                                    'type' => 'text',
                                ),
                            ),
                        ),
                        array(
                            'key' => 'field_state_performance',
                            'label' => 'State',
                            'name' => 'state',
                            'type' => 'group',
                            // Remove 'layout' => 'table'
                            'sub_fields' => array(
                                array(
                                    'key' => 'field_state_code_performance',
                                    'label' => 'Code',
                                    'name' => 'code',
                                    'type' => 'text',
                                ),
                                array(
                                    'key' => 'field_state_name_performance',
                                    'label' => 'Name',
                                    'name' => 'name',
                                    'type' => 'text',
                                ),
                            ),
                        ),
                        array(
                            'key' => 'field_country_performance',
                            'label' => 'Country',
                            'name' => 'country',
                            'type' => 'group',
                            // Remove 'layout' => 'table'
                            'sub_fields' => array(
                                array(
                                    'key' => 'field_country_code_performance',
                                    'label' => 'Code',
                                    'name' => 'code',
                                    'type' => 'text',
                                ),
                                array(
                                    'key' => 'field_country_name_performance',
                                    'label' => 'Name',
                                    'name' => 'name',
                                    'type' => 'text',
                                ),
                            ),
                        ),
                    ),
                ),
                array(
                    'key' => 'field_additional_info_link',
                    'label' => 'Additional Info Link',
                    'name' => 'additional_info_link',
                    'type' => 'url',
                    // Use the "url" field type for URL values
                ),
                array(
                    'key' => 'field_ui_link',
                    'label' => 'UI Link',
                    'name' => 'ui_link',
                    'type' => 'url',
                    // Use the "url" field type for URL values
                ),
                array(
                    'key' => 'field_links',
                    'label' => 'Links',
                    'name' => 'links',
                    'type' => 'repeater',
                    'layout' => 'table',
                    // Use the "repeater" field type for an array of links
                    'sub_fields' => array(
                        array(
                            'key' => 'field_link_rel',
                            'label' => 'Rel',
                            'name' => 'rel',
                            'type' => 'text',
                        ),
                        array(
                            'key' => 'field_link_href',
                            'label' => 'Href',
                            'name' => 'href',
                            'type' => 'url',
                        ),
                    ),
                ),
                array(
                    'key' => 'field_resource_links',
                    'label' => 'Resource Links',
                    'name' => 'resourceLinks',
                    'type' => 'repeater',
                    'layout' => 'table',
                    // Use the "repeater" field type for an array of resource links
                    'sub_fields' => array(
                        array(
                            'key' => 'field_resource_link',
                            'label' => 'Link',
                            'name' => 'link',
                            'type' => 'url',
                        ),
                    ),
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'opportunity',
                    ),
                ),
            ),
            'graphql_field_name' => 'OpportunityInput', // This is the name of the field group in GraphQL
        )
    );
}

add_action('acf/init', 'my_acf_add_local_field_groups');

function import_json_data_to_acf_opportunities()
{
    // Path to your local JSON file
    $json_file_path = 'C:\Users\ibtis\Downloads\insert.json';

    // Read the contents of the JSON file
    $json_data = file_get_contents($json_file_path);

    // Parse the JSON data
    $data = json_decode($json_data, true);

    if (is_array($data)) {
        foreach ($data as $single_data) {
            $post_data = array(
                'post_title' => $single_data['title'],
                'post_status' => 'publish',
                // Set post status
                'post_type' => 'opportunity', // Set post type
            );
            $post_id = wp_insert_post($post_data);
            if ($post_id) {
                // Update the Notice ID field
                update_field('field_notice_id', $single_data['noticeId'], $post_id);

                // Update the Title field
                // update_field('title', $single_data['title']);

                // Update the Solicitation Number field
                update_field('field_notice_solicitationNumber', $single_data['solicitationNumber'], $post_id);

                // Update the Full Parent Path Name field
                update_field('field_notice_fullParentPathName', $single_data['fullParentPathName'], $post_id);

                // Update the Full Parent Path Code field
                update_field('field_notice_fullParentPathCode', $single_data['fullParentPathCode'], $post_id);

                // Convert the date to the appropriate format for ACF date_picker field
                // Update the Posted Date field
                update_field('field_notice_postedDate', date('Ymd', strtotime($single_data['postedDate'])), $post_id);

                // Update the Type field
                update_field('field_notice_type', $single_data['type'], $post_id);

                // Update the Base Type field
                update_field('field_notice_baseType', $single_data['baseType'], $post_id);

                // Update the Archive Type field
                update_field('field_notice_archiveType', $single_data['archiveType'], $post_id);

                // Update the Archive Date field
                update_field('field_notice_archiveDate', date('Ymd', strtotime($single_data['archiveDate'])), $post_id);

                // Update the Type of Set Aside Description field
                update_field('field_notice_typeOfSetAsideDescription', $single_data['typeOfSetAsideDescription'], $post_id);

                // Update the Type of Set Aside field
                update_field('field_notice_typeOfSetAside', $single_data['typeOfSetAside'], $post_id);

                // Convert the date-time string to a format suitable for ACF's date-time picker
                // Update the Response Deadline field
                update_field('field_notice_responseDeadLine', date('Ymd\TH:i:s', strtotime($single_data['responseDeadLine'])), $post_id);

                // Update the NAICS Code field
                update_field('field_notice_naicsCode', $single_data['naicsCode'], $post_id);

                // Update the NAICS Codes repeater field
                update_field('field_naics_codes', $single_data['naicsCodes'], $post_id);

                // Update the Classification Code field
                update_field('field_classification_code', $single_data['classificationCode'], $post_id);

                // Update the Active field
                update_field('field_active', $single_data['active'], $post_id);

                // Update the 'Award' group field
                update_field('field_award', $single_data['award'], $post_id);

                // Update the 'Point of Contact' repeater field
                update_field('field_point_of_contact', $single_data['pointOfContact'], $post_id);

                // Update the 'Description URL' field
                update_field('field_description', $single_data['description'], $post_id);

                // Update the 'Organization Type' field
                update_field('field_organization_type', $single_data['organizationType'], $post_id);

                // Prepare the data for updating the 'officeAddress' field
                $officeAddress = array(
                    'field_zipcode_office' => $single_data['officeAddress']['zipcode'],
                    'field_city_office' => $single_data['officeAddress']['city'],
                    'field_country_code_office' => $single_data['officeAddress']['countryCode'],
                    'field_state_office' => $single_data['officeAddress']['state'],
                );
                // Update the 'Office Address' field group
                update_field('field_office_address', $officeAddress, $post_id);

                // Update the 'Place of Performance' field group
                update_field('field_place_of_performance', $single_data['placeOfPerformance'], $post_id);

                // Update the 'Additional Info Link' field
                update_field('field_additional_info_link', $single_data['additionalInfoLink'], $post_id);

                // Update the 'UI Link' field
                update_field('field_ui_link', $single_data['uiLink'], $post_id);

                // Prepare the data for updating the 'Links' repeater field
                $linksData = array();
                foreach ($single_data['links'] as $link) {
                    $linkData = array(
                        'field_link_rel' => $link['rel'],
                        'field_link_href' => $link['href']
                    );
                    $linksData[] = $linkData;
                }
                // Update the 'Links' repeater field
                update_field('field_links', $linksData, $post_id);

                // Update the resourceLinks repeater field
                update_field('field_resource_links', $single_data['resourceLinks'], $post_id);

                // Save the repeater field
                update_field('field_resource_links', $single_data['resourceLinks'], $post_id);

            } else {
                echo 'Failed to insert post.';
            }
        }
        echo 'ACF fields updated successfully.';
    } else {
        echo 'Failed to parse JSON data.';
    }
}

// Call the function to import JSON data to ACF field group

// add_action('acf/init', 'import_json_data_to_acf_opportunities');

function display_data_acf()
{
    $args = array(
        'post_type' => 'opportunity',

        // Replace with your actual post type
        'posts_per_page' => -1, // Retrieve all posts
    );

    $posts = get_posts($args);

    if ($posts) {
        foreach ($posts as $post) {

            // Display all the posts 
            print_r($post);

            //ID => ID of a Post example "2008"
            $fields = get_fields("ID");

            // Display the field value
            print_r($fields);

        }
    }
}

// add_action('acf/init', 'display_data_acf');