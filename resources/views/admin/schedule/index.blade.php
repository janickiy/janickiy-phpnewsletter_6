@extends('layouts.app')

@section('title', $title)

@section('css')

    {!! Html::style('/admin/js/plugin/fullcalendar/fullcalendar.min.css') !!}

    <style>

        .remove_schedule {
            position: relative;
            left: 10px;
            float: right;
            display: block;
            height: 32px;
            width: 32px;
            cursor: pointer
        }
    </style>

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
                                <a href="{{ URL::route('admin.schedule.create') }}" class="btn btn-info btn-sm pull-left">
                                    <span class="fa fa-plus"> &nbsp;</span>{{ trans('frontend.str.add_schedule') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    <br><br>

                    <div id='calendar'></div>

                </div>
                <!-- end widget content -->

            </div>
            <!-- end widget div -->

        </div>
        <!-- end widget -->

    </div>

@endsection

@section('js')

    {!! Html::script('/admin/js/plugin/moment/moment.min.js') !!}

    {!! Html::script('/admin/js/plugin/fullcalendar/fullcalendar.min.js') !!}

    {!! Html::script('/admin/js/plugin/fullcalendar/lang/ru.js') !!}

    <script>

        $(document).ready(function () {
            // page is now ready, initialize the calendar...
            $('#calendar').fullCalendar({
                // put your options and callbacks here
                defaultView: 'agendaWeek',
                locale: '{{ app()->getLocale() }}',
                timeFormat: "HH:mm",
                slotLabelFormat: "HH:mm",
                events: [
                    @foreach($schedule as $o)
                    {
                        title: '<span class="remove_schedule" id="schedule_{{ $o->id }}" data-id="{{ $o->id }}"><i class="text-danger fa fa-times"></i></span><span class="font-sm"><a class="text-warning font-md" href="{{ URL::route('admin.schedule.edit',['id' => $o->id]) }}"><ins>{{ $o->template->name }}</ins></a></span>',
                        start: '{{ $o->value_from_start_date }}',
                        end: '{{ $o->value_from_end_date }}',
                        content: '{{ $o->value_from_start_date }} - {{ $o->value_from_end_date }}',
                    }
                    ,
                    @endforeach
                ],

                eventRender: function (event, element) {
                    element.html('');
                    element.append(event.start.format('HH:mm') + ' ' +  event.title);
                }
            });

            $(document).on("click", ".remove_schedule", function () {
                $(this).closest('.fc-time-grid-event').remove();

                $.ajax({
                    url: '{{ URL::route('admin.ajax.action') }}',
                    type: "POST",
                    dataType: "json",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {
                        action: "remove_schedule",
                        id: $(this).attr('data-id'),
                    },
                });
            });
        });

    </script>

@endsection

