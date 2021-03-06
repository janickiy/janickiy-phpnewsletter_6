@extends('layouts.app')

@section('title', $title)

@section('css')

@endsection

@section('content')

    <div class="row">
        <div class="col-lg-12"><p class="text-center">
                <a class="btn btn-outline btn-default btn-lg" title="{{ trans('frontend.str.import_subscribers') }}"
                   href="{{ URL::route('admin.subscribers.import') }}">
                    <span class="fa fa-download fa-2x"></span> {{ trans('frontend.str.import') }}
                </a>
                <a class="btn btn-outline btn-default btn-lg" title="{{ trans('frontend.str.export_subscribers') }}"
                   href="{{ URL::route('admin.subscribers.export') }}">
                    <span class="fa fa-upload fa-2x"></span> {{ trans('frontend.str.export') }}
                </a>
                <a class="btn btn-outline btn-danger btn-lg" title="{{ trans('frontend.str.delete_all_subscribers') }}"
                   onclick="confirmation()">
                    <span class="fa fa-trash-o fa-2x"></span> {{ trans('frontend.str.delete_all') }}
                </a>
            </p>
        </div>
    </div>

    <div class="row-fluid">

        <div class="col">

            <!-- Widget ID (each widget will need unique ID)-->
            <div class="jarviswidget jarviswidget-color-blueDark" data-widget-editbutton="false">

                <!-- widget div-->
                <div>

                    <div class="box-header">
                        <div class="row">
                            <div class="col-md-12">
                                <a href="{{ URL::route('admin.subscribers.create') }}"  class="btn btn-info btn-sm pull-left">
                                    <span class="fa fa-plus"> &nbsp;</span>{{ trans('frontend.str.add_subscriber') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    {!! Form::open(['url' => URL::route('admin.subscribers.status'), 'method' => 'post']) !!}

                    <table id="itemList" class="table table-striped table-bordered table-hover" width="100%">
                        <thead>
                        <tr>
                            <th width="10px">
                                <span>
                                    <input type="checkbox"  title="{{ trans('frontend.str.check_uncheck_all') }}" id="checkAll">
                                </span>
                            </th>
                            <th>{{ trans('frontend.str.name') }}</th>
                            <th>E-mail</th>
                            <th>{{ trans('frontend.str.category') }}</th>
                            <th>{{ trans('frontend.str.status') }}</th>
                            <th>{{ trans('frontend.str.added') }}</th>
                            <th width="20px">{{ trans('frontend.str.action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                    <div class="row">
                        <div class="col-sm-12 padding-bottom-10">
                            <div class="form-inline">
                                <div class="control-group">

                                    {!! Form::select('action',[
                                    '1' => trans('frontend.str.activate'),
                                    '0' => trans('frontend.str.deactivate'),
                                    '2' => trans('frontend.str.remove')
                                    ],null,['class' => 'span3 form-control', 'id' => 'select_action','placeholder' => '--' . trans('frontend.str.action') . '--']) !!}

                                    <span class="help-inline">
                                       {!! Form::submit(trans('frontend.str.apply'), ['class' => 'btn btn-success', 'disabled' => "", 'id' => 'apply']) !!}
                                    </span>

                                </div>
                            </div>
                        </div>
                    </div>

                    {!! Form::close() !!}

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
            $("#apply").click(function (event) {
                var idSelect = $('#select_action').val();

                if (idSelect == '') {
                    event.preventDefault();
                    swal({
                        title: "Error",
                        text: "{{ trans('frontend.str.select_action') }}",
                        type: "error",
                        showCancelButton: false,
                        cancelButtonText: "{{ trans('frontend.str.cancel') }}",
                        confirmButtonColor: "#DD6B55",
                        closeOnConfirm: false
                    });
                } else {
                    if (idSelect == 2) {
                        event.preventDefault();
                        var form = $(this).parents('form');
                        swal({
                            title: "{{ trans('frontend.str.delete_confirmation') }}",
                            text: "{{ trans('frontend.str.confirm_remove') }}",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "{{ trans('frontend.str.yes') }}",
                            cancelButtonText: "{{ trans('frontend.str.cancel') }}",
                            closeOnConfirm: false
                        }, function(isConfirm){
                            if (isConfirm) form.submit();
                        });
                    }
                }
            });

            $("#checkAll").click(function () {
                $('input:checkbox').not(this).prop('checked', this.checked);
                countChecked();
            });

            $("#checkAll").on('change',function () {
                countChecked();
            });

            $("#itemList").on('change', 'input.check', function () {
                countChecked();
            });

            pageSetUp();

            $('#itemList').dataTable({
                "sDom": "flrtip",
                "autoWidth": true,
                "oLanguage": {
                    "sSearch": '<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>'
                },
                'createdRow': function (row, data, dataIndex) {
                    $(row).attr('id', 'rowid_' + data['id']);
                    if (data['activeStatus'] == 0) $(row).attr('class', 'danger');
                },
                aaSorting: [[1, 'asc']],
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ URL::route('admin.datatable.subscribers') }}'
                },
                columns: [
                    {data: 'checkbox', name: 'checkbox', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'categories', name: 'categories', orderable: false, searchable: false},
                    {data: 'active', name: 'active', searchable: false},
                    {data: 'created_at', name: 'created_at'},
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
                        confirmButtonText: "{{ trans('frontend.msg.yes_remove') }}",
                        closeOnConfirm: false
                    },
                    function (isConfirm) {
                        if (!isConfirm) return;
                        $.ajax({
                            url: SITE_URL + "/ubscribers/destroy/" + rowid,
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

        function countChecked()
        {
            if ($('.check').is(':checked'))
                $('#apply').attr('disabled',false);
            else
                $('#apply').attr('disabled',true);
        }

        function confirmation(event) {
            swal({
                title: "{{ trans('frontend.str.delete_all_subscribers') }}",
                text: "{{ trans('frontend.str.want_to_delete_all_subscribers')  }}",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "{{ trans('frontend.str.yes') }}",
                cancelButtonText: "{{ trans('frontend.str.cancel') }}",
                closeOnConfirm: false
            }, function () {
                window.location.href = "{{ URL::route('admin.subscribers.remove_all') }}";
            });
        }

    </script>

@endsection
