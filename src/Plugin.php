<?php

namespace Detain\MyAdminPayum;

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class Plugin
 *
 * @package Detain\MyAdminPayum
 */
class Plugin {

	public static $name = 'Payum Payment System';
	public static $description = 'Allows handling of Webuzo HTML5 VNC Connections';
	public static $help = '';
	public static $type = 'plugin';

	/**
	 * Plugin constructor.
	 */
	public function __construct() {
	}

	/**
	 * @return array
	 */
	public static function getHooks() {
		return [
			//'system.settings' => [__CLASS__, 'getSettings'],
			//'ui.menu' => [__CLASS__, 'getMenu'],
			//'function.requirements' => [__CLASS__, 'getRequirements'],
		];
	}

	/**
	 * @param \Symfony\Component\EventDispatcher\GenericEvent $event
	 */
	public static function getMenu(GenericEvent $event) {
		$menu = $event->getSubject();
		if ($GLOBALS['tf']->ima == 'admin') {
			function_requirements('has_acl');
					if (has_acl('client_billing'))
							$menu->add_link('admin', 'choice=none.abuse_admin', '/lib/webhostinghub-glyphs-icons/icons/development-16/Black/icon-spam.png', 'Webuzo');
		}
	}

	/**
	 * @param \Symfony\Component\EventDispatcher\GenericEvent $event
	 */
	public static function getRequirements(GenericEvent $event) {
		$loader = $event->getSubject();
		$loader->add_page_requirement('webuzo_configure', '/../vendor/detain/myadmin-payum-payments/src/webuzo_configure.php');
	}

	/**
	 * @param \Symfony\Component\EventDispatcher\GenericEvent $event
	 */
	public static function getSettings(GenericEvent $event) {
		$settings = $event->getSubject();
		$settings->add_text_setting('General', 'Webuzo', 'abuse_imap_user', 'Webuzo IMAP User:', 'Webuzo IMAP Username', ABUSE_IMAP_USER);
		$settings->add_text_setting('General', 'Webuzo', 'abuse_imap_pass', 'Webuzo IMAP Pass:', 'Webuzo IMAP Password', ABUSE_IMAP_PASS);
	}

}
