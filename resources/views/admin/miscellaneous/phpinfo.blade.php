@extends('layouts.app')

@section('title', $title)

@section('css')

@endsection

@section('content')

    <div class="row-fluid">

        <div class="col">

            <!-- Widget ID (each widget will need unique ID)-->
            <div class="jarviswidget jarviswidget-color-blueDark" data-widget-editbutto="false">

                <!-- widget div-->
                <div>
                    <div id="tree" style="padding-bottom: 15px;">

                    {!! \App\Helpers\StringHelpers::tree($phpinfo) !!}

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
            $('.tree-checkbox').treeview({
                collapsed: true,
                animated: 'medium',
                unique: false
            });
            $('#buttom_json').on('click', function () {
                if ($(this).attr('data-tree') == 'true') {
                    $(this).attr('data-tree', "false");
                    $('#tree').hide();
                    $('#json').show();
                } else {
                    $(this).attr('data-tree', "true");
                    $('#json').hide();
                    $('#tree').show();
                }
            });
        });
    </script>

@endsection
