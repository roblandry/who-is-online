<?php
/*
 Plugin Name: Who is Online
 Plugin URI: http://wordpress.org/extend/plugins/who-is-online/
 Description: Displays who is currently on your blog and for how long.
 Author: Peter McDonald
 Version: 0.1.5
 Author URI: http://collectionmanagers.com/
 */

/*
 * Code that handles the plugin activation
 */

// FEATURE REQUEST http://support.collectionmanagers.com/showthread.php?tid=9

load_plugin_textdomain('who-is-online','/wp-content/plugins/who-is-online/languages/');

register_activation_hook(__FILE__, 'who_is_online_install');
function who_is_online_install()
{
    global $wpdb;
    $who_is_online_table = $wpdb->prefix . 'who_is_online';
    $sql = 'CREATE TABLE ' . $who_is_online_table . ' (
  ip int(20) NOT NULL default \'0\',
  user_id bigint(20) default NULL,
  botname varchar(255) NOT NULL default \'\',
  path varchar(255) NOT NULL default \'\',
  first_visit bigint(20) NULL default NULL,
  last_visit bigint(20) NULL default NULL,
  view_count bigint(20) NOT NULL default \'0\',
  PRIMARY KEY (ip)
);';
    require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    add_option('who_is_online_version', '0.1.5');
    add_option('who_is_online_time_before_off', '5');
}

/*
 * Code that handles the plugin deactivation
 */

register_deactivation_hook(__FILE__, 'who_is_online_uninstall');
function who_is_online_uninstall()
{
    global $wpdb;
    $who_is_online_table = $wpdb->prefix . 'who_is_online';
    $sql = 'DROP TABLE ' . $who_is_online_table;
    $wpdb->query($sql);
    delete_option('who_is_online_version');
    delete_option('who_is_online_time_before_off');
}

/*
 * Code that automatically upgrades the plugin
 */

if (version_compare(get_option('who_is_online_version'), '0.1.5', '<'))
{
    who_is_online_update();
}
function who_is_online_update()
{
    global $wpdb;
    $who_is_online_table = $wpdb->prefix . 'who_is_online';
    $sql = 'CREATE TABLE ' . $who_is_online_table . ' (
  ip int(20) NOT NULL default \'0\',
  user_id bigint(20) default NULL,
  botname varchar(255) NOT NULL default \'\',
  path varchar(255) NOT NULL default \'\',
  first_visit bigint(20) NULL default NULL,
  last_visit bigint(20) NULL default NULL,
  view_count bigint(20) NOT NULL default \'0\',
  PRIMARY KEY (ip)
);';
    require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    update_option('who_is_online_version', '0.1.5');
}

/*
 * Code for the admin page widget
 */

add_action('wp_dashboard_setup', 'who_is_online_add_dashboard_widget');
function who_is_online_add_dashboard_widget()
{
    wp_add_dashboard_widget('who_is_online_dashboard_widget', __('Who Is Online', 'who-is-online'), 'who_is_online_dashboard_widget');
}
function who_is_online_dashboard_widget()
{
    global $wpdb;
    $members_s = '';
    $guests_text = '';
    $person = __('person', 'who-is-online');
    $who_is_online_table = $wpdb->prefix . 'who_is_online';
    $guests_sql = 'SELECT COUNT(*) AS user_count FROM ' . $who_is_online_table . ' WHERE user_id IS NULL;';
    $members_sql = 'SELECT COUNT(user_id) AS user_count FROM ' . $who_is_online_table . ';';
    $result = $wpdb->get_row($members_sql);
    $members = $result->user_count;
    $result2 = $wpdb->get_row($guests_sql);
    $guests = $result2->user_count;
    $total_users = $members + $guests;
    printf(_n('There is currently %d person online.', 'There is currently %d people online.', $total_users, 'who-is-online'), $total_users);
    echo '<br><br>';
    printf(_n('%d member.', '%d members.', $members, 'who-is-online'), $members);
    echo '<br>';
    printf(_n('%d guest.', '%d guests.', $guests, 'who-is-online'), $guests);
    echo '<br><br>';
    echo '<a href="users.php?page=who-is-online/who-is-online.php" id=who_is_online>'._('View them here.').'</a>';
}

/*
 * Code that handles the plugin options page
 */

add_action('admin_menu', 'who_is_online_add_options_page');
function who_is_online_add_options_page()
{
    if(function_exists('add_options_page'))
    {
        add_options_page('Who Is Online', __('Who Is Online', 'who-is-online'), 8, __FILE__, 'who_is_online_options_page');
    }
}
function who_is_online_options_page()
{
    if(isset($_POST['minutes']) and !empty($_POST['minutes']))
    {
        if(ctype_digit($_POST['minutes']))
        {
            update_option('who_is_online_time_before_off', $_POST['minutes']);
            echo '<div id="message" class="updated fade"><p><strong>'.__('Options saved', 'who-is-online').'</strong></p></div>';
        }
        else
        {
            echo '<div id="message" class="updated fade"><p><strong>'.__('You entered an invalid value', 'who-is-online').'</strong></p></div>';
        }
    }
    echo who_is_online_output_options_page();
}
function who_is_online_output_options_page()
{
    return '<div class=wrap>
	<form method="post">
    <h2>'.__('Who\'s Online', 'who-is-online').'</h2>
    <fieldset class="options" name="general">
      <legend>'.__('General settings', 'who-is-online').'</legend>
      <table width="100%" cellspacing="2" cellpadding="5" class="editform">
        <tr>
          <th nowrap valign="top" width="33%">'.__('Minutes Before Offline', 'who-is-online').'</th>
          <td><input type="text" name="minutes" id="minutes" value="' . get_option('who_is_online_time_before_off') . '" /> <span class="description">'.__('Determines how much time will pass before someone is considered offline.', 'who-is-online').'</span>
          </td>
        </tr>
      </table>
    </fieldset>
    <p class="submit">
	<input type="submit" name="Submit" class="button-primary" value="'.__('Save Changes', 'who-is-online').'" />
	</p>
  </form>';
}

/*
 * Code for the visitor sidebar widget
 */

add_action('init', who_is_online_widget_register);
function who_is_online_widget_register()
{
    register_sidebar_widget(__('Who Is Online', 'who-is-online'), 'who_is_online_widget');
}
function who_is_online_widget($args)
{
    global $wpdb;
    $who_is_online_table = $wpdb->prefix . 'who_is_online';
    extract($args);
    $guests_sql = 'SELECT COUNT(*) AS user_count FROM ' . $who_is_online_table . ' WHERE user_id IS NULL;';
    $members_sql = 'SELECT COUNT(user_id) AS user_count FROM ' . $who_is_online_table . ';';
    $result = $wpdb->get_row($members_sql);
    $members = $result->user_count;
    $result2 = $wpdb->get_row($guests_sql);
    $guests = $result2->user_count;
    echo $before_widget;
    echo $before_title;
    echo __('Who\'s Online', 'who-is-online');
    echo $after_title;
    echo '<ul><li>';
    printf(_n('%d Member.', '%d Members.', $members, 'who-is-online'), $members);
    echo '</li><li>';
    printf(_n('%d Guest.', '%d Guests.', $guests, 'who-is-online'), $guests);
    echo '</li></ul>';
    echo $after_widget;
}

/*
 * Code that handles the page showing online users
 */

add_action('admin_menu', 'who_is_online_add_users_page');
function who_is_online_add_users_page()
{
    if(function_exists('add_users_page'))
    {
        add_users_page(__('Who Is Online', 'who-is-online'), __('Who Is Online', 'who-is-online'), 8, __FILE__, 'who_is_online_display_online_users');
    }
}
function who_is_online_display_online_users()
{
    global $wpdb;
    $who_is_online_table = $wpdb->prefix . 'who_is_online';
    $sql = 'SELECT t1.ip AS ip, ('.time().'-t1.first_visit) AS time_on_site, t1.path, t1.user_id AS user_id, t1.view_count as view_count, t2.display_name AS username, botname FROM ' . $who_is_online_table . ' AS t1 LEFT JOIN ' . $wpdb->prefix . 'users AS t2 on t1.user_id = t2.ID WHERE ('.time().'-' . get_option('who_is_online_time_before_off') * 60 . ') < last_visit';
    $result = $wpdb->get_results($sql);
    echo '<div class="wrap">
	<div id="icon-users" class="icon32"><br /></div>
<h2>'.__('Online Users', 'who-is-online').'</h2>
<table class="widefat fixed" cellspacing="0">
<thead>
<tr class="thead">
	<th scope="col" id="username" class="manage-column column-username" style="">'.__('Username', 'who-is-online').'</th>
	<th scope="col" id="name" class="manage-column column-name" style="">'.__('IP', 'who-is-online').'</th>
	<th scope="col" id="online" class="manage-column column-online" style="">'.__('Time Online', 'who-is-online').'</th>
	<th scope="col" id="views" class="manage-column column-views" style="">'.__('Page Views', 'who-is-online').'</th>
	<th scope="col" id="role" class="manage-column column-role" style="">'.__('Viewing', 'who-is-online').'</th>
</tr>
</thead>
<tfoot>
<tr class="thead">
	<th scope="col" id="username" class="manage-column column-username" style="">'.__('Username', 'who-is-online').'</th>
	<th scope="col" id="name" class="manage-column column-name" style="">'.__('IP', 'who-is-online').'</th>
	<th scope="col" id="online" class="manage-column column-online" style="">'.__('Time Online', 'who-is-online').'</th>
	<th scope="col" id="views" class="manage-column column-views" style="">'.__('Page Views', 'who-is-online').'</th>
	<th scope="col" id="role" class="manage-column column-role" style="">'.__('Viewing', 'who-is-online').'</th>
</tr>
</tfoot>
<tbody id="users" class="list:user user-list">
';
    if($result)
    {
        foreach($result as $visitor)
        {
            if(!empty($visitor->botname))
            {
                $username = attribute_escape($visitor->botname);
            }
            elseif(!is_null($visitor->username))
            {
                $username = '<a href="user-edit.php?user_id=' . attribute_escape($visitor->user_id) . '">' . attribute_escape($visitor->username) . '</a>';
            }
            else
            {
                $username = __('Guest', 'who-is-online');
            }
            echo '<tr id=\'user-2\' class="alternate">
<td class="username column-username"><img alt=\'\' src=\'http://www.gravatar.com/avatar/afa3d41f09219ba903e58f96a13eb5cd?s=32&amp;d=http%3A%2F%2Fwww.gravatar.com%2Favatar%2Fad516503a11cd5ca435acc9bb6523536%3Fs%3D32&amp;r=G\' class=\'avatar avatar-32 photo\' width=\'32\' /> <strong>' . $username . '</strong></td>
<td class="ip column-ip"><a href="http://ws.arin.net/whois/?queryinput=' . long2ip($visitor->ip) . '" target="_blank">' . long2ip($visitor->ip) . '</a></td>
<td class="online column-online">' . who_is_online_time_convert($visitor->time_on_site) . '</td>
<td class="online column-online">' . $visitor->view_count . '</td>
<td class="viewing column-viewing"><a href="' . attribute_escape($visitor->path) . '">' . attribute_escape($visitor->path) . '</a></td>
</tr>
';
        }
    }
    echo '</table>';
}

/*
 * Code that handles logging activity
 */

add_action('get_header', 'who_is_online_upload_activity');
add_action('admin_head', 'who_is_online_upload_activity');
function who_is_online_upload_activity()
{
    who_is_online_cleanup();
    global $user_ID;
    if(who_is_online_real_ip() == $_SERVER['SERVER_ADDR'])
    {
        return;
    }
    else
    {
        global $wpdb;
        $who_is_online_table = $wpdb->prefix . 'who_is_online';
        $botname = who_is_online_get_botname($_SERVER['HTTP_USER_AGENT']);
        if(empty($user_ID))
        {
            $id = 'NULL';
        }
        else
        {
            $id = $wpdb->escape($user_ID);
        }
        $ip = ip2long(who_is_online_real_ip());
        $sql = 'INSERT INTO ' . $who_is_online_table . ' (ip, user_id, botname, path, first_visit, last_visit, view_count) VALUES (\'' . $ip . '\', ' . $id . ', \'' . $botname . '\', \'' . $wpdb->escape($_SERVER['REQUEST_URI']) . '\' , '.time().', '.time().', 1) ON DUPLICATE KEY UPDATE path=\'' . $wpdb->escape($_SERVER['REQUEST_URI']) . '\', view_count=view_count+1, user_id=' . $id . ', last_visit = '.time().';';
        $wpdb->query($sql);
    }
}

/*
 * Misc functions
 */
add_filter('the_author', 'who_is_online_maybe_author');
function who_is_online_maybe_author($username)
{
	global $wpdb;
	$check_online = $wpdb->get_results('SELECT t2.id AS id FROM '.$wpdb->prefix.'who_is_online AS t1 LEFT JOIN '.$wpdb->prefix.'users AS t2 ON t1.user_id = t2.id WHERE t2.user_login = \''.$wpdb->escape($username).'\';');
	if ($check_online) {
		$username = $username . ' ('.__('online', 'who-is-online').')';
	}
	return $username;
}

function who_is_online_real_ip()
{
    if((isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) && long2ip(ip2long($_SERVER['HTTP_X_FORWARDED_FOR'])))
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    elseif(long2ip(ip2long($_SERVER['REMOTE_ADDR'])))
    {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    else
    {
        $ip = '127.0.0.1';
    }
    return $ip;
}
function who_is_online_get_botname($user_agent)
{
    $bots = array('Alexa' => 'ia_archiver', 'All The Web' => 'FAST-WebCrawler', 'All The Web' => 'crawler@fast', 'Altavista' => 'Scooter', 'Ask.com' => 'Ask Jeeves/Teoma', 'DMOZ' => 'Robozilla', 'Exite' => 'Architext spider', 'Google' => 'Googlebot', 'Infoseek' => 'InfoSeek sidewinder', 'Inktomi' => 'Slurp', 'Look Smart' => 'MantraAgent', 'Lycos' => 'Lycos_Spider', 'MSN Search' => 'MSNbot', 'Teoma' => 'Teoma_agent', 'Yahoo' => 'Yahoo Slurp', 'Web Crawler' => 'WebCrawler', 'Wise Nut' => 'ZyBorg');
    if(empty($user_agent))
    {
        $botname = '';
    }
    else
    {
        foreach($bots as $bot => $bot_match)
        {
            if(stristr($user_agent, $bot_match))
            {
                $botname = $bot;
                break;
            }
        }
    }
    return $botname;
}
function who_is_online_time_convert($seconds)
{
    $time = '';
    if($seconds > 3600)
    {
        $hours = ($seconds - ($seconds % 3600)) / 3600;
        $seconds = $seconds % 3600;
        $time .= $hours . ':';
    }
    $minutes = str_pad(($seconds - ($seconds % 60)) / 60, 2, 0, STR_PAD_LEFT);
    $seconds = str_pad($seconds % 60, 2, 0, STR_PAD_LEFT);
    $time .= $minutes . ':' . $seconds;
    return $time;
}
function who_is_online_cleanup()
{
    global $wpdb;
    $who_is_online_table = $wpdb->prefix . 'who_is_online';
    $sql = 'DELETE FROM ' . $who_is_online_table . ' WHERE ('.time().'-' . get_option('who_is_online_time_before_off') * 60 . ') > last_visit';
    $wpdb->query($sql);
}
?>