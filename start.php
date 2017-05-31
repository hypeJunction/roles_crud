<?php

/**
 * Implement CRUD shortcuts for roles
 *
 * @author    Ismayil Khayredinov <info@hypejunction.com>
 * @copyright Copyright (c) 2017, Ismayil Khayredinov
 */
require_once __DIR__ . '/autoloader.php';

elgg_register_event_handler('init', 'system', function () {

	elgg_register_plugin_hook_handler('permissions_check', 'object', [\hypeJunction\Roles\Crud\Capabilities::class, 'restrictUpdate'], 900);
	elgg_register_plugin_hook_handler('permissions_check', 'group', [\hypeJunction\Roles\Crud\Capabilities::class, 'restrictUpdate'], 900);

	elgg_register_plugin_hook_handler('container_permissions_check', 'object', [\hypeJunction\Roles\Crud\Capabilities::class, 'restrictCreate'], 900);
	elgg_register_plugin_hook_handler('container_permissions_check', 'group', [\hypeJunction\Roles\Crud\Capabilities::class, 'restrictCreate'], 900);

	elgg_register_plugin_hook_handler('permissions_check:delete', 'object', [\hypeJunction\Roles\Crud\Capabilities::class, 'restrictDelete'], 900);
	elgg_register_plugin_hook_handler('permissions_check:delete', 'group', [\hypeJunction\Roles\Crud\Capabilities::class, 'restrictDelete'], 900);

	elgg_register_plugin_hook_handler('get_sql', 'access', [\hypeJunction\Roles\Crud\Capabilities::class, 'restrictRead'], 900);

	elgg_register_action('admin/roles/crud', __DIR__ . '/actions/admin/roles/crud.php', 'admin');

	elgg_register_plugin_hook_handler('register', 'menu:page', [\hypeJunction\Roles\Crud\Menus::class, 'setupPageMenu']);

	elgg_extend_view('admin.css', 'roles/crud.css');

});