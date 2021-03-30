@extends('layouts.app')
@section('body-bottom')
    <script src="{{asset("/js/vendor/jquery.dataTables.min.js")}}"></script>
    <script src="{{asset("/js/vendor/dataTables.bootstrap4.js")}}"></script>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @include('orders.information')

                        <div class="card">
                            <div class="card-body">
                                <h4 class="header-title mb-3">Zuständige Lehrkräfte</h4>
                                <ul>
                                    @foreach($order->belongingTeachers() as $teacher)
                                        <li>{{$teacher->displayName()}}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
