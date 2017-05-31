<?php

namespace hypeJunction\Roles\Crud;


class Capabilities {


	/**
	 * Apply editing policies
	 *
	 * @param string $hook   "permissions_check"
	 * @param string $type   "object"|"group"
	 * @param bool   $return Permission
	 * @param array  $params Hook params
	 *
	 * @return bool
	 */
	public static function restrictUpdate($hook, $type, $return, $params) {

		$entity = elgg_extract('entity', $params);
		$user = elgg_extract('user', $params);

		if (!$user) {
			return;
		}

		$role = roles_get_role($user);

		$entity_type = $entity->getType();
		$entity_subtype = $entity->getSubtype() ?: 'default';

		$setting = elgg_get_plugin_setting("role:$role->name", 'roles_crud');
		if (!$setting) {
			return;
		}
		$conf = unserialize($setting);

		if ($entity->owner_guid == $user->guid) {
			$capability = 'update:owned';
		} else {
			$capability = 'update:unowned';
		}

		if (!isset($conf[$entity_type][$entity_subtype][$capability])) {
			return;
		}

		switch ($conf[$entity_type][$entity_subtype][$capability]) {

			case 'allow' :
				return true;

			case 'deny' :
				return false;
		}

	}

	/**
	 * Apply create policies
	 *
	 * @param string $hook        "container_permissions_check"
	 * @param string $entity_type "object"|"group
	 * @param bool   $return      Permission
	 * @param array  $params      Hook params
	 *
	 * @return bool
	 */
	public static function restrictCreate($hook, $entity_type, $return, $params) {

		$container = elgg_extract('container', $params);
		$entity_subtype = elgg_extract('subtype', $params) ?: 'default';
		$user = elgg_extract('user', $params);

		if (!$user) {
			return;
		}

		$role = roles_get_role($user);

		$setting = elgg_get_plugin_setting("role:$role->name", 'roles_crud');
		if (!$setting) {
			return;
		}
		$conf = unserialize($setting);

		switch ($container->getType()) {
			case 'site' :
				$capability = 'create:site_container';
				break;

			case 'group' :
				$capability = 'create:group_container';
				break;

			case 'object' :
				$capability = 'create:object_container';
				break;

			case 'user' :
				if ($user->guid == $container->guid) {
					$capability = 'create:self_container';
				} else {
					$capability = 'create:user_container';
				}
				break;
		}

		if (!isset($conf[$entity_type][$entity_subtype][$capability])) {
			return;
		}

		switch ($conf[$entity_type][$entity_subtype][$capability]) {

			case 'allow' :
				return true;

			case 'deny' :
				return false;
		}
	}

	/**
	 * Apply delete policies
	 *
	 * @param string $hook   "permissions_check:delete"
	 * @param string $type   "object"|"group"
	 * @param bool   $return Permission
	 * @param array  $params Hook params
	 *
	 * @return bool
	 */
	public static function restrictDelete($hook, $type, $return, $params) {

		$entity = elgg_extract('entity', $params);
		$user = elgg_extract('user', $params);

		if (!$user) {
			return;
		}

		$role = roles_get_role($user);

		$entity_type = $entity->getType();
		$entity_subtype = $entity->getSubtype() ?: 'default';

		$setting = elgg_get_plugin_setting("role:$role->name", 'roles_crud');
		if (!$setting) {
			return;
		}
		$conf = unserialize($setting);

		if ($entity->owner_guid == $user->guid) {
			$capability = 'delete:owned';
		} else {
			$capability = 'delete:unowned';
		}

		if (!isset($conf[$entity_type][$entity_subtype][$capability])) {
			return;
		}

		switch ($conf[$entity_type][$entity_subtype][$capability]) {

			case 'allow' :
				return true;

			case 'deny' :
				return false;
		}

	}

	/**
	 * Restrict read access
	 *
	 * @param string $hook   "get_sql"
	 * @param string $type   "access"
	 * @param array  $return Access SQL queries
	 * @param array  $params Hook params
	 *
	 * @return array
	 */
	public static function restrictRead($hook, $type, $return, $params) {

		static $catch;

		$table_alias = $params['table_alias'] ? $params['table_alias'] . '.' : '';

		if ($table_alias != 'e.') {
			return;
		}

		$user_guid = (int)elgg_extract('user_guid', $params);

		if (elgg_extract('ignore_access', $params)) {
			return;
		}

		if ($catch) {
			return;
		}

		$catch = true;

		$user = get_entity($user_guid);
		$role = roles_get_role($user ?: null);

		$catch = false;

		$setting = elgg_get_plugin_setting("role:$role->name", 'roles_crud');
		if (!$setting) {
			return;
		}

		$deny = [
			'read:owned' => [],
			'read:unowned' => [],
		];

		$conf = unserialize($setting);
		foreach ($conf as $type => $subtypes) {
			foreach ($subtypes as $subtype => $capabilities) {
				foreach ($capabilities as $capability => $permission) {
					if (!array_key_exists($capability, $deny)) {
						continue;
					}
					if ($permission == 'deny') {
						if ($subtype == 'default') {
							$deny[$capability][$type] = [];
						} else {
							$deny[$capability][$type][] = get_subtype_id($type, $subtype);
						}
					}
				}
			}
		}

		$clauses = [];

		foreach ($deny['read:owned'] as $type => $subtypes) {
			if (!empty($subtypes)) {
				$subtypes_in = implode(',', $subtypes);
				$clauses[] = "(e.owner_guid != $user_guid OR e.subtype NOT IN ($subtypes_in))";
			} else {
				$clauses[] = "(e.owner_guid != $user_guid OR e.type != '$type')";
			}
		}

		foreach ($deny['read:unowned'] as $type => $subtypes) {
			if (!empty($subtypes)) {
				$subtypes_in = implode(',', $subtypes);
				$clauses[] = "(e.owner_guid = $user_guid OR e.subtype NOT IN ($subtypes_in))";
			} else {
				$clauses[] = "(e.owner_guid = $user_guid OR e.type != '$type')";
			}
		}

		if ($clauses) {
			$return['ands'][] = '(' . implode(' OR ', $clauses) . ')';
		}

		return $return;
	}
}
