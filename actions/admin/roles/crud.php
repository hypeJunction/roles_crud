<?php

$crud = get_input('crud');

foreach ($crud as $role => $opts) {
	foreach ($opts as $type => $subtype_opts) {
		foreach ($subtype_opts as $subtype => $capabilities) {
			foreach ($capabilities as $capability => $permission) {
				if ($permission == 'inherit') {
					unset($opts[$type][$subtype][$capability]);
				}
			}
		}
	}

	elgg_set_plugin_setting("role:$role", serialize($opts), 'roles_crud');
}