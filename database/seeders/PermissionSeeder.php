<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$permissions = [
			'role-list',
            'role-create',
            'role-edit',
            'role-delete',
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',
			'complaint-list',
			'complaint-edit',
			'complaint-delete',
			'complaint-view',
		];

		foreach ($permissions as $permission) {
			Permission::firstOrCreate(['name' => $permission]);
		}
	}
}
