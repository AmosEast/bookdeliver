@extends('layouts.basePage')

@section('content')
    <div id = "insideBox" style="width: 100%;">
        <div class="page-header">
            <h1>练习事例<small>Subtext for header</small></h1>
        </div>
        <div style="width: 100%">
            @if(!empty($data))
                <ul class="list-group">
                    @foreach($data as $item)
                        <li class="list-group-item">{{ $item }}</li>
                    @endforeach
                </ul>
            @else
                <h1>there is nothing but me</h1>
            @endif
        </div>
    </div>
@endsection