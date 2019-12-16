@extends('layouts.app')

@section('title', $title)

@section('css')

@endsection

@section('content')

    <!-- START ROW -->
    <div class="row">

        <!-- NEW COL START -->
        <article class="col-sm-12 col-md-12 col-lg-12">

            <!-- Widget ID (each widget will need unique ID)-->
            <div class="jarviswidget" id="wid-id-0" data-widget-colorbutton="false" data-widget-editbutton="false"
                 data-widget-custombutton="false">
                <!-- widget options:
                usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">

                data-widget-colorbutton="false"
                data-widget-editbutton="false"
                data-widget-togglebutton="false"
                data-widget-deletebutton="false"
                data-widget-fullscreenbutton="false"
                data-widget-custombutton="false"
                data-widget-collapsed="true"
                data-widget-sortable="false"

                -->

                <!-- widget div-->
                <div>

                    <!-- widget edit box -->
                    <div class="jarviswidget-editbox">
                        <!-- This area used as dropdown edit box -->

                    </div>
                    <!-- end widget edit box -->

                    <!-- widget content -->
                    <div class="widget-body no-padding">

                        {!! Form::open(['url' => isset($schedule) ? URL::route('admin.schedule.update') : URL::route('admin.schedule.store'), 'method' => isset($schedule) ? 'put' : 'post', 'class' => "smart-form"]) !!}

                        {!! isset($schedule) ? Form::hidden('id', $schedule->id) : '' !!}

                        <header>
                            *-{{ trans('frontend.form.required_fields') }}
                        </header>

                        <fieldset>

                            <section>

                                {!! Form::label('templateId[]', trans('frontend.form.template') . '*', ['class' => 'label']) !!}

                                <label class="input">

                                    {!! Form::select('templateId', $options, old('templateId', isset($schedule) ? $schedule->templateId : null), ['placeholder' => trans('frontend.form.select'), 'class' => 'form-control custom-scroll']) !!}

                                </label>

                                @if ($errors->has('templateId'))
                                    <span class="text-danger">{{ $errors->first('templateId') }}</span>
                                @endif

                            </section>

                            <div class="row">
                                <section class="col col-2">
                                    <label class="input">
                                        {!! Form::text('value_from_start_date', old('value_from_start_date', isset($schedule) ? date("d.m.Y H:i", strtotime($schedule->value_from_start_date)) : null), ['placeholder' => 'DD.MM.YYYY HH:MM', 'class' => 'form-control', 'data-datepicker' => "separateRange"]) !!}
                                    </label>

                                    @if ($errors->has('value_from_start_date'))
                                        <span class="text-danger">{{ $errors->first('value_from_start_date') }}</span>
                                    @endif

                                </section>

                                <section class="col col-2">

                                    <label class="input">

                                        {!! Form::text('value_from_end_date', old('value_from_end_date', isset($schedule) ? date("d.m.Y H:i", strtotime($schedule->value_from_end_date)) : null), ['placeholder' => 'DD.MM.YYYY HH:MM', 'class' => 'form-control', 'data-datepicker' => "separateRange"]) !!}

                                    </label>

                                    @if ($errors->has('value_from_end_date'))
                                        <span class="text-danger">{{ $errors->first('value_from_end_date') }}</span>
                                    @endif
                                </section>

                            </div>

                            <section>

                                {!! Form::label('categoryId[]', trans('frontend.form.subscribers_category') . '*', ['class' => 'label']) !!}

                                <label class="input">

                                    {!! Form::select('categoryId[]', $category_options, old('categoryId', isset($schedule) ? $categoryId : null), ['multiple'=>'multiple', 'placeholder' => trans('frontend.form.select_category'), 'class' => 'form-control custom-scroll']) !!}

                                </label>

                                @if ($errors->has('categoryId'))
                                    <span class="text-danger">{{ $errors->first('categoryId') }}</span>
                                @endif

                            </section>

                        </fieldset>

                        <footer>
                            <button type="submit" class="btn btn-primary">
                                {{ trans('frontend.form.send') }}
                            </button>
                            <a class="btn btn-default" href="{{ URL::route('admin.schedule.index') }}">
                                {{ trans('frontend.form.back') }}
                            </a>
                        </footer>

                        {!! Form::close() !!}

                    </div>
                    <!-- end widget content -->

                </div>
                <!-- end widget div -->

            </div>
            <!-- end widget -->

        </article>
        <!-- END COL -->

    </div>

    <!-- END ROW -->

@endsection

@section('js')

    {!! Html::script('/admin/js/plugin/daterangepicker/moment.min.js') !!}

    {!! Html::script('/admin/js/plugin/daterangepicker/daterangepicker.js') !!}

    <script>

        var separator = ' - ', dateFormat = 'DD.MM.YYYY hh:mm';
        var options = {
            autoUpdateInput: false,
            autoApply: true,
            timePicker: true,
            timePicker24Hour: true,
            autoUpdateInput: false,
            locale: {
                format: dateFormat,
                separator: separator,
                applyLabel: 'Apply',
                cancelLabel: 'Cancel'
            },
            minDate: moment().add(1, 'days'),
            maxDate: moment().add(359, 'days'),
            opens: "right"
        };

        $('[data-datepicker=separateRange]')
            .daterangepicker(options)
            .on('apply.daterangepicker', function (ev, picker) {
                var boolStart = this.name.match(/value_from_start_/g) == null ? false : true;
                var boolEnd = this.name.match(/value_from_end_/g) == null ? false : true;

                var mainName = this.name.replace('value_from_start_', '');

                if (boolEnd) {
                    mainName = this.name.replace('value_from_end_', '');
                    $(this).closest('form').find('[name=value_from_end_' + mainName + ']').blur();
                }

                $(this).closest('form').find('[name=value_from_start_' + mainName + ']').val(picker.startDate.format(dateFormat));
                $(this).closest('form').find('[name=value_from_end_' + mainName + ']').val(picker.endDate.format(dateFormat));

                $(this).trigger('change').trigger('keyup');
            })
            .on('show.daterangepicker', function (ev, picker) {
                var boolStart = this.name.match(/value_from_start_/g) == null ? false : true;
                var boolEnd = this.name.match(/value_from_end_/g) == null ? false : true;
                var mainName = this.name.replace('value_from_start_', '');

                if (boolEnd) {
                    mainName = this.name.replace('value_from_end_', '');
                }

                var startDate = $(this).closest('form').find('[name=value_from_start_' + mainName + ']').val();
                var endDate = $(this).closest('form').find('[name=value_from_end_' + mainName + ']').val();

                $('[name=daterangepicker_start]').val(startDate).trigger('change').trigger('keyup');
                $('[name=daterangepicker_end]').val(endDate).trigger('change').trigger('keyup');

                if (boolEnd) {
                    $('[name=daterangepicker_end]').focus();
                }
            });

    </script>

@endsection
