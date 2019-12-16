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



            $(document).on("click", ".robot", function () {
                var status = 'start';
                var code = $(this).attr('data-cod');
                var id = $(this).attr('data-id');

                $.ajax({
                    type: "POST",
                    url: "{{ URL::route('admin.ajax.action') }}",
                    data: {
                        action: 'process',
                        status: status,
                        code: code,
                        id: id,
                    },
                    dataType: "json",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success: function (data) {
                        $("#mailing_status").html('<span title="Рассылка остановлена" class="stopmailing"></span>');
                    }
                });
            });

            setInterval(function () {
                $.ajax({
                    type: "POST",
                    cache: false,
                    url: "{{ URL::route('admin.ajax.action') }}",
                    data: {action: 'daemonstat'},
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    dataType: "json",
                    success: function (data) {
                        $.each(data, function(i, val) {

                            alert(data[i].name);


                            if (data.id != '') {

                              //  alert(data[0].id);

                                if (data.status == 1) {
                                    $('#robot-'.data.id).removeClass("stop").addClass("start");
                                } else {
                                    $('#robot-'.data.id).removeClass("start").addClass("stop");
                                }
                            }
                        });



                    }
                });
            }, 5000);


            pageSetUp();

            /* // DOM Position key index //

            l - Length changing (dropdown)
            f - Filtering input (search)
            t - The Table! (datatable)
            i - Information (records)
            p - Pagination (paging)
            r - pRocessing
            < and > - div elements
            <"#id" and > - div with an id
            <"class" and > - div with a class
            <"#id.class" and > - div with an id and class

            Also see: http://legacy.datatables.net/usage/features
            */

            /* BASIC ;*/
            var responsiveHelper_dt_basic = undefined;

            var breakpointDefinition = {
                tablet: 1024,
                phone: 480
            };

            $('#itemList').dataTable({
                "sDom": "flrtip",
                "autoWidth": true,
                "oLanguage": {
                    "sSearch": '<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>'
                },
                "preDrawCallback": function () {
                    // Initialize the responsive datatables helper once.
                    if (!responsiveHelper_dt_basic) {
                        responsiveHelper_dt_basic = new ResponsiveDatatablesHelper($('#itemList'), breakpointDefinition);
                    }
                },
                "rowCallback": function (nRow) {
                    responsiveHelper_dt_basic.createExpandIcon(nRow);
                },
                "drawCallback": function (oSettings) {
                    responsiveHelper_dt_basic.respond();
                },
                'createdRow': function (row, data, dataIndex) {
                    $(row).attr('id', 'rowid_' + data['id']);
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

                var btn = this;
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
