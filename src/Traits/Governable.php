<?php namespace GeneaLabs\LaravelGovernor\Traits;

use GeneaLabs\LaravelGovernor\Role;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait Governable
{
    public function roles() : BelongsTo
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_key');
    }

    /**
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsSuperAdminAttribute() : bool
    {
        $superAdminRole = (new Role)->where('name', 'SuperAdmin')->first();
        $this->load('roles');

        return $this->roles->contains($superAdminRole->name);
    }
}
