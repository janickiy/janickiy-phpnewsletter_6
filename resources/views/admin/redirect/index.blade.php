@extends('layouts.app')

@section('title', $title)

@section('css')

@endsection

@section('content')

    @if(Helpers::has_permission(Auth::user()->role,'admin'))

    <div class="row">
        <div class="col-lg-12"><p class="text-center">
                <a class="btn btn-outline btn-danger btn-lg" title="{{ trans('frontend.str.log_clear') }}" onclick="confirmation()">
                    <span class="fa fa-trash-o fa-2x"></span> {{ trans('frontend.str.redirect_clear') }}
                </a>
            </p>
        </div>
    </div>

    @endif

    <div class="row-fluid">

        <div class="col">

            <!-- Widget ID (each widget will need unique ID)-->
            <div class="jarviswidget jarviswidget-color-blueDark" data-widget-editbutton="false">

                <!-- widget div-->
                <div>
                    <table id="itemList" class="table table-striped table-bordered table-hover" width="100%">
                        <thead>
                        <tr>
                            <th>URL</th>
                            <th>{{ trans('frontend.str.redirect_number') }}</th>
                            <th>{{ trans('frontend.str.excel_report') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- end widget div -->

        </div>
        <!-- end widget -->

    </div>

@endsection

@section('js')

    <script>

        $(document).ready(function () {

            pageSetUp();

            $('#itemList').dataTable({
                "sDom": "flrtip",
                "oLanguage": {
                    "sSearch": '<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>'
                },
                "autoWidth": true,
                aaSorting: [[0, 'asc']],
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ URL::route('admin.datatable.redirect') }}'
                },
                columns: [
                    {data: 'url', name: 'url'},
                    {data: 'count', name: 'count', searchable: false},
                    {data: 'report', name: 'report', orderable: false, searchable: false},
                ],
            });
        })

        function confirmation(event) {
            swal({
                title: "{{ trans('frontend.str.clear_confirmation') }}",
                text: "{{ trans('frontend.str.redirect_clear')  }}",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "{{ trans('frontend.str.yes') }}",
                cancelButtonText: "{{ trans('frontend.str.cancel') }}",
                closeOnConfirm: false
            }, function () {
                window.location.href = "{{ URL::route('admin.redirect.clear') }}";
            });
        }

    </script>

@endsection
