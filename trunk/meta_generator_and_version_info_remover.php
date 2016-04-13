<?php
/*
Plugin Name: Meta Generator and Version Info Remover
Plugin URI: http://pankajgurudeb.blogspot.com/2013/04/meta-generator-and-version-info-remover.html
Description: This plugin will remove the version information that gets appended to enqueued style and script URLs. It will also remove the Meta Generator in the head and in RSS feeds. Adds a bit of obfuscation to hide the WordPress version number and generator tag that many sniffers detect automatically from view source. But always remember to keep your WordPress updated.
Author: Pankaj Kumar Mondal
Author URI: http://pankajgurudeb.blogspot.com
Tags: remove, version, generator, security, meta, appended version, css ver, js ver, meta generator
Version: 3.3
License: GPLv2 or later.
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

class Meta_generator_and_version_info_remover {
    
    public $options;
    
    public function __construct() {
        
        $this->options = get_option('meta_generator_and_version_info_remover_options');
        $this->pkm_register_settings_and_fields();
    }

    public function pkm_add_menu_page() {
    
        add_options_page('Meta Generator and Version Info Remover', 'Meta Generator and Version Info Remover', 'administrator', __FILE__, array('Meta_generator_and_version_info_remover','pkm_display_options_page'));
    }

    public static function pkm_display_options_page() {
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2> Meta Generator and Version Info Remover Settings </h2>
            <form method="post" action="options.php">
                
                <?php 
                settings_fields('meta_generator_and_version_info_remover_options');
                do_settings_sections(__FILE__);
                ?>
                
                <p class="submit">
                    <input name="submit" type="submit" class="button-primary" value="Save Changes" />
                </p>
            </form>
        </div>
        <?php
    }

    public function pkm_register_settings_and_fields() {
        register_setting('meta_generator_and_version_info_remover_options', 'meta_generator_and_version_info_remover_options');
        
        add_settings_section('pkm_meta_generator_remover_section', 'Meta Generator Remover Settings', array($this, 'pkm_meta_generator_and_version_info_remover_callback'), __FILE__);
        add_settings_field('pkm_meta_generator_remover_enable_checkbox', 'Remove Meta Generator Tag', array($this, 'pkm_meta_generator_remover_checkbox_setting'), __FILE__, 'pkm_meta_generator_remover_section');
        
        add_settings_section('pkm_meta_generator_and_version_info_remover_section', 'Version Info Remover Settings', array($this, 'pkm_meta_generator_and_version_info_remover_callback'), __FILE__);
        add_settings_field('pkm_version_info_remover_style_checkbox', 'Remove Version from Stylesheet', array($this, 'pkm_version_info_remover_style_checkbox_setting'), __FILE__, 'pkm_meta_generator_and_version_info_remover_section');
        add_settings_field('pkm_version_info_remover_script_checkbox', 'Remove Version from Script', array($this, 'pkm_version_info_remover_script_checkbox_setting'), __FILE__, 'pkm_meta_generator_and_version_info_remover_section');
        add_settings_field('pkm_version_info_remover_script_exclude_css', 'Enter Stylesheet/Script file names to exclude from version removal (comma separated list)', array($this, 'pkm_version_info_remover_script_exclude_css'), __FILE__, 'pkm_meta_generator_and_version_info_remover_section');
    }
    
    public function pkm_meta_generator_and_version_info_remover_callback() {
        // no callback as of now
    }
    
    public function pkm_meta_generator_remover_checkbox_setting() {
        ?>
        <input name="meta_generator_and_version_info_remover_options[pkm_meta_generator_remover_enable_checkbox]" type="checkbox" value="1"<?php checked( 1 == $this->options['pkm_meta_generator_remover_enable_checkbox'] ); ?> />
        <?php 
    }

    public function pkm_version_info_remover_style_checkbox_setting() {
        ?>
        <input name="meta_generator_and_version_info_remover_options[pkm_version_info_remover_style_checkbox]" type="checkbox" value="1"<?php checked( 1 == $this->options['pkm_version_info_remover_style_checkbox'] ); ?> />
        <?php
    }

    public function pkm_version_info_remover_script_checkbox_setting() {
        ?>
        <input name="meta_generator_and_version_info_remover_options[pkm_version_info_remover_script_checkbox]" type="checkbox" value="1"<?php checked( 1 == $this->options['pkm_version_info_remover_script_checkbox'] ); ?> />
        <?php
    }

    public function pkm_version_info_remover_script_exclude_css() {
        ?>
        <textarea placeholder="Enter comma separated list of file names (Stylesheet/Script files) to exclude them from version removal process. Version info will be kept for these files." name="meta_generator_and_version_info_remover_options[pkm_version_info_remover_script_exclude_css]" rows="7" cols="60" style="resize:none;"><?php if (isset($this->options['pkm_version_info_remover_script_exclude_css'])) { echo $this->options['pkm_version_info_remover_script_exclude_css']; } ?></textarea>
        <?php
    }
}

$options = get_option('meta_generator_and_version_info_remover_options');
if( isset($options['pkm_version_info_remover_script_exclude_css']) ) {
    $exclude_file_list = $options['pkm_version_info_remover_script_exclude_css'];
} else {
    $exclude_file_list = '';
}
$exclude_files_arr = array_map('trim', explode(',', $exclude_file_list));

/**
 * Hook into the generator.
 */
if( isset($options['pkm_meta_generator_remover_enable_checkbox']) && ($options['pkm_meta_generator_remover_enable_checkbox'] == 1) ) {
    add_filter( 'the_generator', '__return_null' );
}

/**
 *  remove wp version param from any enqueued scripts (using wp_enqueue_script()) or styles (using wp_enqueue_style()). But first check the list of user defined excluded CSS/JS files... Those files will be skipped and version information will be kept.
 */
function pkm_remove_appended_version_script_style( $target_url ) {
    $filename_arr = explode('?', basename($target_url));
    $filename = $filename_arr[0];
    global $exclude_files_arr, $exclude_file_list;
    // first check the list of user defined excluded CSS/JS files
    if (!in_array(trim($filename), $exclude_files_arr)) {
        /* check if "ver=" argument exists in the url or not */
        if(strpos( $target_url, 'ver=' )) {
            $target_url = remove_query_arg( 'ver', $target_url );
        }
    }
    return $target_url;
}

/**
 * Priority set to 20000. Higher numbers correspond with later execution.
 * Hook into the style loader and remove the version information.
 */
if( isset($options['pkm_version_info_remover_style_checkbox']) && ($options['pkm_version_info_remover_style_checkbox'] == 1) ) {
add_filter('style_loader_src', 'pkm_remove_appended_version_script_style', 20000);
}

/**
 * Hook into the script loader and remove the version information.
 */
if( isset($options['pkm_version_info_remover_script_checkbox']) && ($options['pkm_version_info_remover_script_checkbox'] == 1) ) {
add_filter('script_loader_src', 'pkm_remove_appended_version_script_style', 20000);
}

add_action('admin_menu', 'pkm_meta_generator_add_options_page_function');

function pkm_meta_generator_add_options_page_function() {
    $object = new Meta_generator_and_version_info_remover();
    $object->pkm_add_menu_page();
}

add_action('admin_init', 'pkm_meta_generator_remover_initiate_class');

function pkm_meta_generator_remover_initiate_class() {
    new Meta_generator_and_version_info_remover();
}

function meta_generator_and_version_info_remover_defaults() {
    $current_options = get_option('meta_generator_and_version_info_remover_options');
    
    $defaults = array(
        'pkm_meta_generator_remover_enable_checkbox'            => 1,
        'pkm_version_info_remover_style_checkbox'               => 1,
        'pkm_version_info_remover_script_checkbox'              => 1,
        'pkm_version_info_remover_script_exclude_css'           => ( isset($current_options['pkm_version_info_remover_script_exclude_css']) ? $current_options['pkm_version_info_remover_script_exclude_css'] : '' )
    );
    
    if( is_admin() ) {
        update_option( 'meta_generator_and_version_info_remover_options', $defaults );
    }
}

register_activation_hook( __FILE__, 'meta_generator_and_version_info_remover_defaults' );

function meta_generator_and_version_info_remover_set_plugin_meta($links, $file) {
    
    $plugin = plugin_basename(__FILE__);
 
    // create link
    if ($file == $plugin) {
        return array_merge(
            $links,
            array( sprintf( '<a href="options-general.php?page=%s">%s</a>', $plugin, __('Settings') ) )
        );
    }
 
    return $links;
}

add_filter( 'plugin_row_meta', 'meta_generator_and_version_info_remover_set_plugin_meta', 10, 2 );

?>
