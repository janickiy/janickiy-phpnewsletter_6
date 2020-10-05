@extends('layouts.app')

@section('title', $title)

@section('css')

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

                    <div class="widget-body-toolbar">

                        <div id="calendar-buttons">

                            <div class="btn-group">
                                <a href="javascript:void(0)" class="btn btn-default btn-xs" id="btn-prev"><i class="fa fa-chevron-left"></i></a>
                                <a href="javascript:void(0)" class="btn btn-default btn-xs" id="btn-next"><i class="fa fa-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>

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

    {{ app()->getLocale() != 'en' ? Html::script('/admin/js/plugin/fullcalendar/lang/' . app()->getLocale() . '.js') : '' }}

    <script>

        $(document).ready(function() {

            $('#calendar').fullCalendar({
                // put your options and callbacks here
                defaultView: 'agendaWeek',
                {!! app()->getLocale() != 'en' ? "locale: '" . app()->getLocale() . "',":"" !!}
                timeFormat: "HH:mm",
                slotLabelFormat: "HH:mm",
                allDaySlot: false,
                events: [
                        @foreach($schedule as $o)
                        @if (isset($o->template->name) && $o->template->name)
                    {
                        title: '<span class="remove_schedule" id="schedule_{{ $o->id }}" data-id="{{ $o->id }}"><i class="text-danger fa fa-times"></i></span><span class="font-sm"><a class="text-warning font-md" href="{{ URL::route('admin.schedule.edit',['id' => $o->id]) }}"><ins>{{ $o->template->name }}</ins></a></span>',
                        start: '{{ $o->value_from_start_date }}',
                        end: '{{ $o->value_from_end_date }}',
                        content: '{{ $o->value_from_start_date }} - {{ $o->value_from_end_date }}',
                            className: ["event", "bg-color-blue"],
                            icon: 'fa-clock-o'
                    }
                    ,
                    @endif
                    @endforeach
                ],

                eventRender: function (event, element, icon) {
                    element.html('');
                    element.append(event.start.format('HH:mm') + ' ' +  event.title);
                },
            });

            /* hide default buttons */
            $('.fc-right, .fc-center').hide();


            $('#calendar-buttons #btn-prev').click(function () {
                $('.fc-prev-button').click();
                return false;
            });

            $('#calendar-buttons #btn-next').click(function () {
                $('.fc-next-button').click();
                return false;
            });

            $('#calendar-buttons #btn-today').click(function () {
                $('.fc-today-button').click();
                return false;
            });

            $('#mt').click(function () {
                $('#calendar').fullCalendar('changeView', 'month');
            });

            $('#ag').click(function () {
                $('#calendar').fullCalendar('changeView', 'agendaWeek');
            });

            $('#td').click(function () {
                $('#calendar').fullCalendar('changeView', 'agendaDay');
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
        })

    </script>

@endsection
