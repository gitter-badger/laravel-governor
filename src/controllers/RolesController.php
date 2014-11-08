<?php namespace GeneaLabs\Bones\Keeper\Controllers;

use GeneaLabs\Bones\Keeper\Action;
use GeneaLabs\Bones\Keeper\Entity;
use GeneaLabs\Bones\Keeper\Ownership;
use GeneaLabs\Bones\Keeper\Permission;
use GeneaLabs\Bones\Keeper\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class RolesController extends \BaseController
{
    public function __construct()
    {
        $this->beforeFilter('auth');
        $this->beforeFilter('csrf', ['on' => 'post']);
    }

    public function index()
    {
        if (Auth::user()->hasAccessTo('view', 'any', 'role')) {
            $roles = Role::orderBy('name')->get();

            return View::make('bones-keeper::roles.index', compact('roles'));
        }
    }

    public function create()
    {
        if (Auth::user()->hasAccessTo('create', 'any', 'role')) {
            return View::make('bones-keeper::roles.create');
        }
    }

    public function store()
    {
        if (Auth::user()->hasAccessTo('create', 'any', 'role')) {
            Role::create(Input::all());

            return Redirect::route('roles.index');
        }
    }

    public function edit($name)
    {
        if (Auth::user()->hasAccessTo('edit', 'any', 'role')) {
            $role = Role::with('permissions')->find($name);
            $entities = Entity::all();
            $actions = Action::all();
            $ownerships = Ownership::all();
            $permissionMatrix = [];
            foreach ($entities as $entity) {
                foreach ($actions as $action) {
                    $selectedOwnership = 'no';
                    foreach ($role->permissions as $permissioncheck) {
                        if (($permissioncheck->entity->name == $entity->name) &&
                            ($permissioncheck->action->name == $action->name)
                        ) {
                            $selectedOwnership = $permissioncheck->ownership->name;
                        }
                    }
                    $permissionMatrix[$entity->name][$action->name] = $selectedOwnership;
                }
            }
            $ownershipOptions = array_merge(['no' => 'no'], $ownerships->lists('name', 'name'));

            return View::make('bones-keeper::roles.edit', compact('role', 'permissionMatrix', 'ownershipOptions'));
        }
    }

    public function update($name)
    {
        if (Auth::user()->hasAccessTo('edit', 'any', 'role')) {
            $role = Role::find($name);
            $role->name = Input::has('name') ? Input::get('name') : $role->name;
            $role->description = Input::has('description') ? Input::get('description') : $role->description;
            $role->save();
            $role = Role::find(Input::get('name'));
            $allActions = Action::all();
            $allOwnerships = Ownership::all();
            $allEntities = Entity::all();
            if (Input::has('permissions')) {
                $role->permissions()->delete();
                foreach (Input::get('permissions') as $entity => $actions) {
                    foreach ($actions as $action => $ownership) {
                        if ('no' != $ownership) {
                            $currentAction = $allActions->find($action);
                            $currentOwnership = $allOwnerships->find($ownership);
                            $currentEntity = $allEntities->find($entity);
                            $currentPermission = new Permission();
                            $currentPermission->ownership()->associate($currentOwnership);
                            $currentPermission->action()->associate($currentAction);
                            $currentPermission->role()->associate($role);
                            $currentPermission->entity()->associate($currentEntity);
                            $currentPermission->save();
                        }
                    }
                }
            }

            return Redirect::route('roles.index');
        }
    }

    public function destroy($name)
    {
        if (Auth::user()->hasAccessTo('remove', 'any', 'role')) {
            Role::destroy($name);

            return Redirect::route('roles.index');
        }
    }
}
