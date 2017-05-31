<?php

return [
	'admin:roles:crud' => 'CRUD',

	'roles:crud:capability' => 'Permission',
	'roles:crud:capability:create' => 'Create',
	'roles:crud:capability:update' => 'Update',
	'roles:crud:capability:read' => 'Read',
	'roles:crud:capability:delete' => 'Delete',

	'roles:crud:capability:create:self_container' => 'Create an owned entity',
	'roles:crud:capability:create:user_container' => 'Create an entity contained by another user',
	'roles:crud:capability:create:object_container' => 'Create an entity contained by another object',
	'roles:crud:capability:create:group_container' => 'Create an entity contained by a group (if group permissions allow)',
	'roles:crud:capability:create:site_container' => 'Create an entity contained by the site',

	'roles:crud:capability:update:owned' => 'Update an owned entity',
	'roles:crud:capability:update:unowned' => 'Update an entity owned by someone else',

	'roles:crud:capability:read:owned' => 'View an owned entity',
	'roles:crud:capability:read:unowned' => 'View an entity owned by someone else (if entity access level allows)',

	'roles:crud:capability:delete:owned' => 'Delete an owned entity',
	'roles:crud:capability:delete:unowned' => 'Delete an entity owned by someone else',

	'roles:crud:allow' => 'Allow',
	'roles:crud:deny' => 'Deny',
	'roles:crud:inherit' => 'Inherit',

];