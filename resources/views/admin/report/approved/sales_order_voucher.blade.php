@extends('layouts.backend.app')
@section('title', 'Voucher Exchange')
@push('css')
@endpush
@section('admin_content')<br>
    <!-- add component-->
    @component('components.index', [
        'close_route' => route('report-sales-order'),
        'title' => 'Voucher Exchange',
        'print_layout' => 'landscape',
        'print_header' => 'Voucher Exchange',
        'print' => 'Print',
        'excel' => 'excel',
        'pdf' => 'pdf',
        'user_privilege_status_type' => 'master',
        'user_privilege_title' => 'Voucher Exchange',
        'user_privilege_type' => 'create_role',
    ])
        <!-- Main body component -->
        @slot('body')
            <div class="dt-responsive cell-border sd tableFixHead_report">
                <div class="card">
                    <div class="row ms-2">
                        <div class="col-md-4">
                            @foreach ($sales as $key => $sale)
                                @if (user_privileges_check('Voucher', $sale->voucher_id, 'display_role'))
                                    <li class="m-1"><a
                                            style=" text-decoration: none; font-size: 15px;color:#0B55C4;"href=" {{ route('voucher-exchange', ['id' => $sale->voucher_id, 'tran_id' => $id]) }}"
                                            data-turbolinks="false">{{ $sale->voucher_name }}</a></li>
                                @endif
                                </ul>
                            @endforeach
                        </div>
                        <div>
                        </div>
                    </div>
                @endslot
            @endcomponent
            @push('js')
            @endpush
        @endsection
