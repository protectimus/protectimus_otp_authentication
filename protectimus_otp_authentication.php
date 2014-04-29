<?php

require(__DIR__ . '/lib/bootstrap.php');

/**
 * Protectimus One-time Password Authentication Plugin v1.0
 *
 * Allows to integrate two-factor authentication into RoundCube Webmail.
 * Before use, you must configure the authentication using one-time passwords in Protectimus.
 * Detailed information http://www.protectimus.com/.
 *
 * Copyright (C) 2013-2014 Protectimus Solution LLP
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/.
 */
class protectimus_otp_authentication extends rcube_plugin
{

	const PROTECTIMUS_OTP_CHECK = 'protectimus_otp_check';
	const PROTECTIMUS_ERROR_MESSAGE = 'protectimus_error_message';
	const PROTECTIMUS_CHALLENGE = 'protectimus_challenge';
	private $rcmail;

	function init()
	{
		$this->rcmail = rcmail::get_instance();
		$this->load_config();
		$this->add_texts('localization/', true);
		$this->add_hook('startup', array($this, 'startup'));
		$this->add_hook('login_after', array($this, 'login_after'));
		if ($this->rcmail->config->get('protectimus_allow_users_to_modify_authentication_enabled_state')) {
			$this->add_hook('preferences_list', array($this, 'preferences_list'));
			$this->add_hook('preferences_save', array($this, 'preferences_save'));
		}
		$this->register_action('plugin.protectimus_otp_authentication', array($this, 'otp_form'));
		$this->register_action('plugin.protectimus_otp_authentication_action', array($this, 'protectimus_otp_authentication_request_handler'));
	}

	function startup($args)
	{
		if ($_SESSION[self::PROTECTIMUS_OTP_CHECK] && $args['action'] != "plugin.protectimus_otp_authentication_action") {
			$args['_task'] = null;
			$args['action'] = 'plugin.protectimus_otp_authentication';
			return $args;
		} else if (!$_SESSION[self::PROTECTIMUS_OTP_CHECK] && ($args['action'] == "plugin.protectimus_otp_authentication" || $args['action'] == "plugin.protectimus_otp_authentication_action")) {
			$this->rcmail->output->redirect(array('_task' => 'mail', 'action' => null));
		}
		return $args;
	}

	function login_after($args)
	{
		$resource_id = $this->rcmail->config->get('protectimus_resource_id');
		if (!empty($resource_id)
		&& (!$this->rcmail->config->get('protectimus_allow_users_to_modify_authentication_enabled_state')
		|| ($this->rcmail->config->get('protectimus_allow_users_to_modify_authentication_enabled_state')
		&& $this->rcmail->config->get('protectimus_authentication_enabled', $this->rcmail->config->get('protectimus_authentication_enabled_by_default_for_user')) == true))) {
			try {
				$api = new ProtectimusApi($this->rcmail->config->get('protectimus_api_username'), $this->rcmail->config->get('protectimus_api_key'), $this->rcmail->config->get('protectimus_api_url'));
				$response = $api->prepareAuthentication($resource_id, null, null, $this->rcmail->user->data['username']);
				if (isset($response->response->challenge)) {
					$_SESSION[self::PROTECTIMUS_CHALLENGE] = $response->response->challenge;
				}
			} catch (Exception $e) {
			}
			$_SESSION[self::PROTECTIMUS_OTP_CHECK] = true;
			$args['_task'] = null;
			$args['action'] = 'plugin.protectimus_otp_authentication';
			return $args;
		}
	}

	function preferences_list($args)
	{
		if ($args['section'] == 'server') {
			$protectimus_authentication_enabled = $this->rcmail->config->get('protectimus_authentication_enabled', $this->rcmail->config->get('protectimus_authentication_enabled_by_default_for_user'));
			$field_id = 'protectimus_authentication_enabled';
			$checkbox = new html_checkbox(array('name' => '_protectimus_authentication_enabled', 'id' => $field_id, 'value' => 1));
			$args['blocks']['protectimus']['name'] = 'Protectimus';
			$args['blocks']['protectimus']['options']['protectimus_authentication_enabled'] = array(
                'title' => html::label($field_id, $this->gettext('use_2fa')),
                'content' => $checkbox->show($protectimus_authentication_enabled ? 1 : 0)
			);
		}
		return $args;
	}

	function preferences_save($args)
	{
		if ($args['section'] == 'server') {
			$args['prefs']['protectimus_authentication_enabled'] = isset($_POST['_protectimus_authentication_enabled']) ? true : false;
			return $args;
		}
	}

	function otp_form()
	{
		$this->register_handler('plugin.pagetitle', array($this, 'page_title'));
		if (!empty($_SESSION[self::PROTECTIMUS_ERROR_MESSAGE])) {
			$this->register_handler('plugin.message', array($this, 'error_message'));
		}
		if (!empty($_SESSION[self::PROTECTIMUS_CHALLENGE])) {
			$this->register_handler('plugin.challenge', array($this, 'challenge'));
		}
		$this->register_handler('plugin.body', array($this, 'protectiomus_otp'));
		if ($this->rcmail->config->get('protectimus_show_self_service_url') && filter_var($this->rcmail->config->get('protectimus_self_service_url'), FILTER_VALIDATE_URL) !== false) {
			$this->register_handler('plugin.selfservice', array($this, 'selfservice'));
		}
		$this->rcmail->output->send('protectimus_otp_authentication.login');
	}

	function page_title()
	{
		return $this->gettext('protectimus_otp_authentication');
	}

	function error_message()
	{
		$message = $_SESSION[self::PROTECTIMUS_ERROR_MESSAGE];
		$_SESSION[self::PROTECTIMUS_ERROR_MESSAGE] = null;
		return '<div class="t-alert-container"><div class="t-error"><div class="t-dismiss"></div><div class="t-message-container">' . $message . '</div></div></div>';
	}

	function challenge()
	{
		return '<div>' . $this->gettext('challenge') . ': <b>' . $_SESSION[self::PROTECTIMUS_CHALLENGE] . '</b></div>';
	}

	function protectiomus_otp()
	{
		$this->include_stylesheet('skins/default/css/application.css');
		$this->include_stylesheet('skins/default/css/style.css');
	}

	function selfservice() {
		return '<div class="revision-box">' . $this->gettext('self_service_url') . ': <a href="' . $this->rcmail->config->get('protectimus_self_service_url') . '" target="_blank">' . $this->rcmail->config->get('protectimus_self_service_url') . '</a></div>';
	}

	function protectimus_otp_authentication_request_handler($args = array())
	{
		try {
			$api = new ProtectimusApi($this->rcmail->config->get('protectimus_api_username'), $this->rcmail->config->get('protectimus_api_key'), $this->rcmail->config->get('protectimus_api_url'));
			$response = $api->authenticateUserToken($this->rcmail->config->get('protectimus_resource_id'), null, $this->rcmail->user->data['username'], $_POST['otp'], $_SERVER['REMOTE_ADDR']);
			if ($response->response->result) {
				$_SESSION[self::PROTECTIMUS_OTP_CHECK] = null;
				$_SESSION[self::PROTECTIMUS_CHALLENGE] = null;
				$this->rcmail->output->redirect(array('_task' => 'mail', 'action' => null));
			} else {
				$_SESSION[self::PROTECTIMUS_ERROR_MESSAGE] = $this->gettext('invalid_otp');
				$this->rcmail->output->redirect(array('_task' => null, 'action' => 'plugin.protectimus_otp_authentication'));
			}
		} catch (Exception $e) {
			$_SESSION[self::PROTECTIMUS_ERROR_MESSAGE] = $e->getMessage();
			$this->rcmail->output->redirect(array('_task' => null, 'action' => 'plugin.protectimus_otp_authentication'));
		}
	}

}

?>