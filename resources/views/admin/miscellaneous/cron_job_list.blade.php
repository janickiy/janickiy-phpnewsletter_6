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

                    <table id="itemList" class="table table-striped table-bordered table-hover" width="100%">
                        <thead>
                        <tr>
                            <th>Cronjob</th>
                            <th>{{ trans('frontend.str.description') }}</th>
                        </tr>
                        </thead>

                        <tbody>

                        @foreach($cronJob as $job)
                            <tr>
                                <td>{{ $job['cron'] }}</td>
                                <td>{{ $job['description'] }}</td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                </div>
                <!-- end widget content -->

            </div>
            <!-- end widget div -->

        </div>
        <!-- end widget -->

    </div>

@endsection


@section('include')


@endsection
