<?php

namespace hypeJunction\Roles\Crud;

class Menus {

	/**
	 * Setup menu
	 *
	 * @param string $hook "register"
	 * @param string $type "menu:page"
	 * @param \ElggMenuItem[] $return Menu
	 * @param array $params Hook params
	 * @return \ElggMenuItem[]
	 */
	public static function setupPageMenu($hook, $type, $return, $params) {

		$return[] = \ElggMenuItem::factory([
			'name' => 'crud',
			'href' => 'admin/roles/crud',
			'text' => elgg_echo('admin:roles:crud'),
			'context' => 'admin',
			'section' => 'roles'
		]);

		return $return;
	}
}