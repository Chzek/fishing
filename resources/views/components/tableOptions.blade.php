@if(view()->exists($table.'.edit'))
    <a href='/$table/{{ $table->id }}/edit' class='btn btn-sm btn-light' role='button'>
        <i class="fa fa-pencil-square-o" data-toggle="tooltip" data-placement="top" title="Edit"></i>
    </a>
@endif
@if(view()->exists($table.'.show'))
    <a href='/$table/{{ $table->id }}' class='btn btn-sm btn-light' role='button'>
        <i class="fa fa-eye" data-toggle="tooltip" data-placement="top" title="Show"></i>
    </a>
@endif