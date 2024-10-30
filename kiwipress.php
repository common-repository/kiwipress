<?php
  defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
  /*
  Plugin Name:  KiwiPress
  Plugin URI:   https://kiwipress.tk
  Description:  This plugin, KiwiPress, integrates the KiwiIRC chat system into WordPress
  Version:      1.2
  Author:       Scott McGann (cantelope)
  Author URI:   https://cantelope.org
  License:      GPL2
  License URI:  https://www.gnu.org/licenses/gpl-2.0.html
  */
  
  function KiwiPress_plugin_add_settings_link( $links ) {
      $settings_link = '<a href="options-general.php?page=KiwiPress+Settings">' . __( 'Settings' ) . '</a>';
      array_push( $links, $settings_link );
      return $links;
  }
  $KiwiPressPlugin = plugin_basename( __FILE__ );
  add_filter( "plugin_action_links_$KiwiPressPlugin", 'KiwiPress_plugin_add_settings_link' );

  
  function deployKiwiPress() {
    wp_register_style( 'KiwiOptionsCSS', plugins_url( 'kiwipress.css', __FILE__ ) );
    wp_enqueue_style('KiwiOptionsCSS');
    $opt_val = get_option( 'kiwiOptions' );
    $server = $opt_val["network"];
    $theme = $opt_val["theme"];
    $channel = $opt_val["channel"];
    if(substr($channel, 0, 1) != '#') $channel = "#" . $channel;
    $nick = $opt_val["nick"];
    $URL = "https://kiwiirc.com/nextclient/$server/?nick=$nick&theme=$theme$channel";
    return "<iframe src='$URL' class='kiwiFrame'></iframe>";
  }
  add_shortcode('kiwipress', 'deployKiwiPress');
  add_action('admin_menu', 'KiwiPress_add_pages');
  function KiwiPress_add_pages() {
      // Add a new submenu under Settings:
      add_options_page(__('KiwiPress Settings','kiwiPress'), __('KiwiPress Settings','kiwiPress'), 'manage_options', 'KiwiPress Settings', 'KiwiPress_settings_page');
  }
  function KiwiPress_settings_page() {
    //must check that the user has the required capability 
    if (!current_user_can('manage_options') || (isset($_POST['_wpnonce']) && !wp_verify_nonce( $_POST['_wpnonce'], "update_options" ))){
      wp_nonce_ays('You do not have sufficient permissions to access this page.');
    }
    $hidden_field_name = 'mt_submit_hidden';
    $opt_val = get_option( 'kiwiOptions' );
    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
      $opt_val = $_POST;
      update_option( "kiwiOptions", $opt_val );
      ?>
        <div class="updated"><p><strong><?php _e('settings saved.', 'kiwiPress' ); ?></strong></p></div>
      <?php
    }
    echo '<div class="wrap">';
    echo "<h2>" . __( 'KiwiPress Plugin Settings', 'kiwiPress' ) . "</h2>";
    wp_register_style( 'KiwiOptionsCSS', plugins_url( 'kiwipress.css', __FILE__ ) );
    wp_enqueue_style('KiwiOptionsCSS');
    ?>

    <div class="kiwiOptions">
      <form name="form1" method="post" action="" >
        <?php wp_nonce_field( 'update_options' ); ?>
        <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y" class="KiwiPress_input">
        <p><?php _e("IRC NETWORK:", 'kiwiPress' ); ?> 
          <input type="text" name="network" value="<?php echo $opt_val["network"]; ?>" size="20" class="KiwiPress_input">
        </p>
        <p><?php _e("DEFAULT NICK:", 'kiwiPress' ); ?> 
          <input type="text" name="nick" value="<?php echo $opt_val["nick"]; ?>" size="20" class="KiwiPress_input">
        </p>
        <p><?php _e("CHANNEL:", 'kiwiPress' ); ?> 
          <input type="text" name="channel" value="<?php echo $opt_val["channel"]; ?>" size="20" class="KiwiPress_input">
        </p>
        <p><?php _e("THEME:", 'kiwiPress' ); ?> 
          <select name="theme" class="KiwiPress_input">
            <option value="Default" <?php if(isset($opt_val["theme"]) && $opt_val["theme"] == "Default") echo "selected" ?>>Default</option>
            <option value="Dark" <?php if(isset($opt_val["theme"]) && $opt_val["theme"] == "Dark") echo "selected" ?>>Dark</option>
            <option value="Nightswatch" <?php if(isset($opt_val["theme"]) && $opt_val["theme"] == "Nightswatch") echo "selected" ?>>Nightswatch</option>
            <option value="Radioactive" <?php if(isset($opt_val["theme"]) && $opt_val["theme"] == "Radioactive") echo "selected" ?>>Radioactive</option>
            <option value="Osprey" <?php if(isset($opt_val["theme"]) && $opt_val["theme"] == "Osprey") echo "selected" ?>>Osprey</option>
            <option value="Sky" <?php if(isset($opt_val["theme"]) && $opt_val["theme"] == "Sky") echo "selected" ?>>Sky</option>
            <option value="Coffee" <?php if(isset($opt_val["theme"]) && $opt_val["theme"] == "Coffee") echo "selected" ?>>Coffee</option>
          </select>
        </p><hr />
        <p class="submit">
        <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
        </p>
      </form>
    </div>
    <div class="kiwiOptions usage">
      Usage:<br>
      Configure these settings,<br>then simply add the<br>following shortcode to any<br>of your pages/posts.<br><br>
      <strong>[kiwipress]</strong>
    </div>
    <?php
}
?>
