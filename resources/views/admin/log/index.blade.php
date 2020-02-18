@extends('layouts.app')

@section('title', $title)

@section('css')

@endsection

@section('content')

    @if(Helpers::has_permission(Auth::user()->role,'admin'))

    <div class="row">
        <div class="col-lg-12"><p class="text-center">
                <a class="btn btn-outline btn-danger btn-lg" title="{{ trans('frontend.str.log_clear') }}" href="{{ URL::route('admin.log.clear') }}" onclick="return confirm('{{ trans('frontend.str.want_to_log_clear') }}');">
                    <span class="fa fa-trash-o fa-2x"></span> {{ trans('frontend.str.log_clear') }}
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
                            <th>{{ trans('frontend.str.time') }}</th>
                            <th>{{ trans('frontend.str.total') }}</th>
                            <th>{{ trans('frontend.str.sent') }}</th>
                            <th>{{ trans('frontend.str.unsent') }}</th>
                            <th>{{ trans('frontend.str.read') }}</th>
                            <th>{{ trans('frontend.str.excel_report') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>


                <table id="logList" class="table table-striped table-bordered table-hover" width="100%">
                    <thead>
                    <tr>
                        <th>{{ trans('frontend.str.newsletter') }}</th>
                        <th>E-mail</th>
                        <th>{{ trans('frontend.str.time') }}</th>
                        <th>{{ trans('frontend.str.status') }}</th>
                        <th>{{ trans('frontend.str.read') }}</th>
                        <th>{{ trans('frontend.str.error') }}</th>
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
                "sDom": "lrtip",
                "autoWidth": true,
                'createdRow': function (row, data, dataIndex) {
                    $(row).attr('id', 'rowid_' + data['id']);
                },
                aaSorting: [[0, 'asc']],
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ URL::route('admin.datatable.log') }}'
                },

                columns: [
                    {data: 'value_from_start_date', name: 'value_from_start_date'},
                    {data: 'count', name: 'count', searchable: false},
                    {data: 'sent', name: 'sent', searchable: false},
                    {data: 'unsent', name: 'unsent', searchable: false},
                    {data: 'read_mail', name: 'read_mail', searchable: false},
                    {data: 'report', name: 'report', orderable: false, searchable: false},
                ],
            });

            $('#logList').dataTable({
                "sDom": "flrtip",
                "autoWidth": true,
                "oLanguage": {
                    "sSearch": '<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>'
                },
                'createdRow': function (row, data, dataIndex) {
                    $(row).attr('id', 'rowid_' + data['id']);
                    if (data['status'] == 0) $(row).attr('class', 'danger');
                    else if (data['status'] == 1) $(row).attr('class', 'success');
                },
                aaSorting: [[2, 'desc']],
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ URL::route('admin.datatable.info_log') }}'
                },
                columns: [
                    {data: 'template', name: 'template'},
                    {data: 'email', name: 'email'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'success', name: 'success', searchable: false},
                    {data: 'readMail', name: 'readMail', searchable: false},
                    {data: 'errorMsg', name: 'errorMsg', orderable: false, searchable: false},
                ],
            });
        })

    </script>

@endsection
