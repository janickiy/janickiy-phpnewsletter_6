@extends('layouts.app')

@section('title', $title)

@section('css')

@endsection

@section('content')

    <div class="row-fluid">

        <div class="col">

            <!-- Widget ID (each widget will need unique ID)-->
            <div class="jarviswidget jarviswidget-color-blueDark" data-widget-editbutton="false">

                <!-- widget div-->
                <div>

                    <div class="box-header">
                        <div class="row">
                            <div class="col-md-12">
                                <a href="{{ URL::route('admin.category.create') }}" class="btn btn-info btn-sm pull-left">
                                    <span class="fa fa-plus"> &nbsp;</span>{{ trans('frontend.str.add_category') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="itemList" class="table table-striped table-bordered table-hover" width="100%">
                            <thead>
                            <tr>
                                <th>{{ trans('frontend.str.name') }}</th>
                                <th>{{ trans('frontend.str.subscribers_number') }}</th>
                                <th width="20px">{{ trans('frontend.str.action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>

                </div>
                <!-- end widget content -->

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
                "autoWidth": true,
                "oLanguage": {
                    "sSearch": '<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>'
                },
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ URL::route('admin.datatable.category') }}'
                },
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'subcount', name: 'subcount', searchable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
            });

            $('#itemList').on('click', 'a.deleteRow', function () {

                var rowid = $(this).attr('id');
                swal({
                        title: "{{ trans('frontend.msg.are_you_sure') }}",
                        text: "{{ trans('frontend.msg.will_not_be_able_to_ecover_information') }}",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        cancelButtonText: "{{ trans('frontend.str.cancel') }}",
                        confirmButtonText: "{{ trans('frontend.msg.yes_remove') }}",
                        closeOnConfirm: false
                    },
                    function (isConfirm) {
                        if (!isConfirm) return;
                        $.ajax({
                            url: SITE_URL + "/category/destroy/" + rowid,
                            type: "DELETE",
                            dataType: "html",
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            success: function () {
                                $("#rowid_" + rowid).remove();
                                swal("{{ trans('frontend.msg.done') }}", "{{ trans('frontend.msg.data_successfully_deleted') }}", "success");
                            },
                            error: function (xhr, ajaxOptions, thrownError) {
                                swal("{{ trans('frontend.msg.error_eleting') }}", "{{ trans('frontend.msg.try_again') }}", "error");
                            }
                        });
                    });
            });
        })

    </script>

@endsection
