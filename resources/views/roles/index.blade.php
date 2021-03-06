@extends ('genealabs-laravel-governor::layout')

@section ('governorContent')
    <div class="panel panel-default">
        <div class="panel-heading">
            Roles Management

            @can ('create', $roles->first())
            {!! link_to_route('genealabs.laravel-governor.roles.create', 'Add New Role', null, ['class' => 'btn btn-default btn-xs pull-right']) !!}
            @endcan

        </div>

        @can ('view', $roles->first())
            <div class="list-group">

                @foreach ($roles as $role)
                    <a {!! ((Auth::user()->can('edit', $role) && ($role->name != 'SuperAdmin')) ? 'href="' . route('genealabs.laravel-governor.roles.edit', $role->name) . '"' : 'disabled') !!} class="list-group-item">
                        <h4 class="list-group-item-heading">{{ $role->name }}</h4>
                        <p class="list-group-item-text">{{ $role->description }}</p>
                    </a>
                @endforeach

            </div>
        @endcan

    </div>
@stop
