<h5 class='display-5 d-inline align-middle'>{{ ucfirst($name) }} Index</h5>
<div class="btn-group float-right" role="group" aria-label="{{ ucfirst($name) }} collectoin actions">
    @if(view()->exists(implode(".", [$name, 'create'])))
        <a href='/{{ $name }}/create' class='card-link btn btn-sm btn-dark' role='button'>Add</a>
    @endif
    <a href='/' class='card-link btn btn-sm btn-dark' role='button'>Return</a>
</div>