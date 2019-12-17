@if (isset($infoAlert))
    <div class="alert alert-info">
        <i class="fa-fw fa fa-warning"></i>
        {!! $infoAlert !!}
    </div>
@endif

@if (Session::has('message'))
    <div class="alert alert-warning fade in">
        <button class="close" data-dismiss="alert">
            ×
        </button>
        <i class="fa-fw fa fa-warning"></i>
        {{ Session::get('message') }}.
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success fade in">
        <button class="close" data-dismiss="alert">
            ×
        </button>
        <i class="fa-fw fa fa-check"></i>
        {{ session('success') }}

    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger fade in">
        <button class="close" data-dismiss="alert">
            ×
        </button>
        <i class="fa-fw fa fa-times"></i>
        <strong>Ошибка!</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

