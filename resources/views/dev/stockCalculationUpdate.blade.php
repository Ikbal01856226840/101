
@extends('layouts.backend.app')
@section('title','stockInRate')
@push('css')
<link rel="stylesheet" type="text/css" href="{{asset('libraries/css/jquery-ui.theme.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('libraries/css/jquery-ui.min.css')}}">
@endpush
@section('admin_content')
@component('components.voucher', [
    'title' => "stockInRate",
    'background_color'=>'#e5e5cd!important',
    'opration'=>'Create',
]);
<!-- Page-header component -->
@slot('voucher_body')
<form  method="POST" action="{{ route('stockCalculationUpdate') }}">
    @csrf
    <div class="page-body">
        <h1>Maximum Id: {{$max_id??0}}</h1>
        <div class="row margin">
            <div class="col-md-6 m-0 p-0">
                <label>Start Id: </label>
                <input type="number" name="start_id" class="form-control fs-5 start_id" value="{{$start_id ?? 0}}">
            </div>
            <div class="col-md-6 m-0 p-0">
                <label>End Id: </label>
                <input type="number" name="end_id" class="form-control fs-5 end_id" value="{{$end_id ?? 0 }}">
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>

@endslot
@endcomponent
@push('js')


<script type="text/javascript" src="{{asset('libraries/js/jquery-ui.min.js')}}"></script>


@endpush
@endsection

