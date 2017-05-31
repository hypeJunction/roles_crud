<?php

namespace hypeJunction\Roles\Crud;

use ElggRole;

class Policy {

	/**
	 * Get configurable capabilities for a user type
	 *
	 * @param ElggRole $role Role
	 *
	 * @return array
	 */
	public static function getCapabilityTypes(ElggRole $role) {

		$is_visitor = $role->name == VISITOR_ROLE;

		$capabilities = [
			'create:self_container' => !$is_visitor,
			'create:user_container' => !$is_visitor,
			'create:object_container' => !$is_visitor,
			'create:group_container' => !$is_visitor,
			'create:site_container' => !$is_visitor,
			'read:owned' => !$is_visitor,
			'read:unowned' => true,
			'update:owned' => !$is_visitor,
			'update:unowned' => !$is_visitor,
			'delete:owned' => !$is_visitor,
			'delete:unowned' => !$is_visitor,
		];

		return elgg_trigger_plugin_hook('capability_types', 'roles', [
			'role' => $role,
		], $capabilities);
	}
}