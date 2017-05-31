<?php

$roles = roles_get_all_roles();
unset($roles['admin']);

$types = get_registered_entity_types();

$capabilities = [];
$values = [];

foreach ($roles as $role) {
	$setting = elgg_get_plugin_setting("role:$role->name", 'roles_crud');
	if ($setting) {
		$values[$role->name] = unserialize($setting);
	} else {
		$values[$role->name] = [];
	}
}

foreach ($roles as $role) {
	$capability_types = \hypeJunction\Roles\Crud\Policy::getCapabilityTypes($role);
	foreach ($capability_types as $capability_type => $enabled) {
		$capabilities[$capability_type][$role->name] = $enabled;
	}
}

foreach ($types as $type => $subtypes) {
	if ($type == 'user') {
		continue;
	}
	if (empty($subtypes)) {
		$subtypes = ['default'];
	}
	foreach ($subtypes as $subtype) {

		echo '<h2 class="roles-crud-title">' . elgg_echo("item:$type:$subtype", ['class' => 'roles-crud-title']) . '</h2>';

		?>
        <table class="elgg-table-alt roles-crud-table">
            <tr>
                <th><?= elgg_echo('roles:crud:capability') ?></th>
				<?php
				foreach ($roles as $role) {
					?>
                    <th><?= $role->getDisplayName() ?></th>
					<?php
				}
				?>
            </tr>
            <tbody>
			<?php
			$known_capabilities = array_keys($capabilities);
			foreach ($known_capabilities as $capability) {
				?>
                <tr>
                    <td><?= elgg_echo("roles:crud:capability:$capability") ?></td>
					<?php
					foreach ($roles as $role) {
						if (!$capabilities[$capability][$role->name]) {
							echo '<td>---</td>';
							continue;
						}
						?>
                        <td>
							<?php
							$value = 'inherit';
							if (isset($values[$role->name][$type][$subtype][$capability])) {
								$value = $values[$role->name][$type][$subtype][$capability];
							}
							echo elgg_view_field([
								'#type' => 'select',
								'name' => "crud[$role->name][$type][$subtype][$capability]",
								'value' => $value,
								'options_values' => [
									'inherit' => elgg_echo('roles:crud:inherit'),
									'allow' => elgg_echo('roles:crud:allow'),
									'deny' => elgg_echo('roles:crud:deny'),
								],
							]);
							?>
                        </td>
						<?php
					}
					?>
                </tr>
				<?php
			}
			?>
            </tbody>
        </table>
		<?php
	}
}

$footer = elgg_view_field(['#type' => 'submit',
	'value' => elgg_echo('save'),]);

elgg_set_form_footer($footer);