<div class="btn-group float-right" role="group" aria-label="{{ ucfirst($name) }} actions">
    @if(view()->exists(implode(".", [$name, 'edit'])))
        @if(isset($user) && Auth::id() == $user)
            <a href='/{{ $name }}/{{ $identifier }}/edit' class='btn btn-sm btn-light' role='button'>
                <i class="fa fa-pencil-square-o" data-toggle="tooltip" data-placement="top" title="Edit"></i>
            </a>
        @endif
        @if(!isset($user))
            <a href='/{{ $name }}/{{ $identifier }}/edit' class='btn btn-sm btn-light' role='button'>
                <i class="fa fa-pencil-square-o" data-toggle="tooltip" data-placement="top" title="Edit"></i>
            </a>
        @endif
    @endif
    @if(view()->exists(implode(".", [$name, 'show'])))
        <a href='/{{ $name }}/{{ $identifier }}' class='btn btn-sm btn-light' role='button'>
            <i class="fa fa-eye" data-toggle="tooltip" data-placement="top" title="Show"></i>
        </a>
    @endif
    @if(view()->exists(implode(".", [$name, 'profile'])))
        <a href='/{{ $name }}/{{ $identifier }}/profile' class='btn btn-sm btn-light' role='button'>
            <i class="fa fa-expand" data-toggle="tooltip" data-placement="top" title="Show"></i>
        </a>
    @endif
</div>