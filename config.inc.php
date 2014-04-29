<?php

/**
 * Protectimus One-time Password Authentication Plugin v1.0 options
 */
// Protectimus credentials
$rcmail_config['protectimus_api_username'] = '';
$rcmail_config['protectimus_api_key'] = '';
$rcmail_config['protectimus_api_url'] = 'https://api.protectimus.com/';

// ID of the resource in Protectimus
$rcmail_config['protectimus_resource_id'] = 1;
// if true, then user will be able to enable/disable Protectimus One-time Password Authentication in Settings/Preferences/Server Settings
$rcmail_config['protectimus_allow_users_to_modify_authentication_enabled_state'] = true;
// default state (enabled/disable) of Protectimus One-time Password Authentication for user, if user is able to enable/disable it
$rcmail_config['protectimus_authentication_enabled_by_default_for_user'] = false;
// if true, then user will see self service url
$rcmail_config['protectimus_show_self_service_url'] = true;
$rcmail_config['protectimus_self_service_url'] = 'http://service.protectimus.com/selfservice/name';

?>