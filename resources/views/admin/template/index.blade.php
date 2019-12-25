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
                                <a href="{{ URL::route('admin.template.create') }}"
                                   class="btn btn-info btn-sm pull-left"><span
                                        class="fa fa-plus"> &nbsp;</span>{{ trans('frontend.str.add_template') }}</a>
                            </div>
                        </div>
                    </div>

                    {!! Form::open(['url' => URL::route('admin.template.status'), 'method' => 'post', 'onSubmit' => 'if (this.action.value == \'0\') { return false; } if(this.action.value == \'\') { window.alert(\'' . trans('frontend.str.select_action') . '\'); return false; } if (this.action.value == \'1\') { return confirm(\'' . trans('frontend.str.confirm_remove') .'\') }']) !!}

                    <table id="itemList" class="table table-striped table-bordered table-hover" width="100%">
                        <thead>
                        <tr>
                            <th width="10px"><span><input type="checkbox"
                                                          title="{{ trans('frontend.str.check_uncheck_all') }}"
                                                          id="checkAll"></span></th>
                            <th width="15px">ID</th>
                            <th width="50%">{{ trans('frontend.str.template') }}</th>
                            <th>{{ trans('frontend.str.importance') }}</th>
                            <th>{{ trans('frontend.str.attachments') }}</th>
                            <th>{{ trans('frontend.str.date') }}</th>
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
                                    '0' => trans('frontend.str.send'),
                                    '1' => trans('frontend.str.remove')
                                    ],null,['class' => 'span3 form-control', 'id' => 'select_action','placeholder' => '--' . trans('frontend.str.action') . '--'],[0 => ['data-id' => 'sendmail', 'class' => 'open_modal']]) !!}

                                    <span class="help-inline">

                                        {!! Form::submit(trans('frontend.str.apply'), ['class' => 'btn btn-success', 'disabled' => "", 'id' => 'apply']) !!}

                                    </span>

                                </div>
                            </div>
                        </div>
                    </div>

                    {!! Form::close() !!}

                </div>

                <!-- end widget content -->

            </div>
            <!-- end widget div -->

        </div>
        <!-- end widget -->

        <div id="sendmail" class="modal_div">
            <span class="modal_close">X</span>
            <div id="onlinelog"></div>
            <h3>Онлайн журнал рассылки</h3>
            <p><span id="leftsend">0</span>% Осталось: <span id="timer2">00:00:00</span></p>
            <form>
                <input type="text" name="">
                <button>Отправить</button>
            </form>
        </div>

    </div>

@endsection

@section('js')

    <script>

        $(document).ready(function () {
            var overlay = $('#overlay');
            var open_modal = $('#apply');
            var close = $('.modal_close, #overlay');
            var modal = $('.modal_div');

            open_modal.click(function (event) {
                var idSelect = $('#select_action').val();

                if (idSelect == 0) {
                    event.preventDefault();
                    var div = $('.open_modal').attr('data-id');
                    overlay.fadeIn(400,
                        function () {
                            $('#' + div)
                                .css('display', 'block')
                                .animate({opacity: 1, top: '50%'}, 200);
                        });
                }
            });

            close.click(function () {
                modal
                    .animate({opacity: 0, top: '45%'}, 200,
                        function () {
                            $(this).css('display', 'none');
                            overlay.fadeOut(400);
                        }
                    );
            });

            $("#checkAll").click(function () {
                $('input:checkbox').not(this).prop('checked', this.checked);
                countChecked();
            });

            $("#checkAll").on('change', function () {
                countChecked();
            });

            $("#itemList").on('change', 'input.check', function () {
                countChecked();
            });

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
                aaSorting: [[1, 'asc']],
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ URL::route('admin.datatable.templates') }}'
                },
                columns: [
                    {data: 'checkbox', name: 'checkbox', orderable: false, searchable: false},
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'prior', name: 'prior', searchable: false},
                    {data: 'attach.id', name: 'attach.id', searchable: false},
                    {data: 'created_at', name: 'created_at'},
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
                            url: SITE_URL + "/template/destroy/" + rowid,
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

        function countChecked() {
            if ($('.check').is(':checked'))
                $('#apply').attr('disabled', false);
            else
                $('#apply').attr('disabled', true);
        }

    </script>

@endsection
