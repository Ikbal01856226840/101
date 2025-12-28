@extends('layouts.backend.app')
@section('title','Voucher Exchange')
@push('css')
<style>
    input[type=radio] {
        width: 20px;
        height: 20px;
    }

    input[type=checkbox] {
        width: 20px;
        height: 20px;
    }
    .td {
        border: 1px solid #ddd;
    }
    .font {
        font-size: 16px;
    }

    body {
        overflow: auto !important;
    }
</style>

@endpush
@section('admin_content')<br>
@component('components.modal', [
    'id' => 'VoucherExchange',
    'class' => 'modal fade',
    'form_id' => 'FormVoucherExchange',
    'method'=> 'POST',
    'title'=>'Voucher Exchange'
])
@slot('body')
<input type="hidden" name="transaction_id" id="transaction_id">
<div class="row" id="voucher_exchange_modal_body"></div>
@endslot
@slot('footer')
<div id="voucher_exchange_modal_footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary change_exchange_save">Duplicate</button>
</div>
@endslot
@endcomponent

<!-- add component-->
@component('components.report', [
    'title' => 'Voucher Exchange',
    'print_layout'=>'landscape',
    'print_header'=>'Voucher Exchange',
    'user_privilege_title'=>'VoucherExchange',
    'print_date'=>1,
]);

<!-- Page-header component -->
@slot('header_body')
<form id="VoucherExchangeForm" method="POST">
    @csrf
    {{ method_field('POST') }}
    <div class="row">
        <div class="col-md-2">
            <label>Voucher Type : </label>
            <select name="voucher_id" class="form-control js-example-basic-single voucher_id">
                @if (Auth()->user()->user_level==1)
                  <option value="0">--ALL--</option>

                @endif
                @php $voucher_type_id= 0; @endphp
                @foreach ($vouchers as $voucher)
                @if($voucher_type_id!=$voucher->voucher_type_id)
                    @php $voucher_type_id=$voucher->voucher_type_id; @endphp
                    @if (Auth()->user()->user_level==1)
                    <option style="color:red;" value="v{{$voucher->voucher_type_id??''}}">{{$voucher->voucher_type??''}}</option>
                    @else
                        @if($voucher->filtered_count==$voucher->total_count)
                        <option style="color:red;" value="v{{$voucher->voucher_type_id??''}}">{{$voucher->voucher_type??''}}</option>
                        @endif
                    @endif

                @endif
                <option value="{{$voucher->voucher_id}}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$voucher->voucher_name}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <div class="row  m-0 p-0 ">
                <div class="col-md-6 m-0 p-0 start_date">
                    <label>Date From: </label>
                    <input type="text" name="from_date" class="form-control setup_date fs-5 from_date" value="{{financial_end_date(date('Y-m-d'))}}" name="narratiaon">
                </div>
                <div class="col-md-6 m-0 p-0 end_date">
                    <label>Date To : </label>
                    <input type="text" name="to_date" class="form-control setup_date fs-5 to_date" value="{{financial_end_date(date('Y-m-d'))}}" name="narratiaon">
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <label></label>
            <div>
                <input class="form-check-input" type="checkbox" id="narration" name="narration" value="1">
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    Narration
                </label>
                @if (Auth()->user()->user_level==1)
                <input class="form-check-input" type="checkbox" id="userName" name="userName" value="1" >
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    User Info
                </label>
              @endif
            </div>
        </div>
        <div class="col-md-2">
            <br>
            <button type="submit" class="btn hor-grd btn-grd-primary btn-block submit" style="width:200px; margin-bottom:5px;"><span class="m-1 m-t-1"></span><span>Search</span></button>
        </div>

    </div>
</form>
@endslot

<!-- Main body component -->
@slot('main_body')
<div class="dt-responsive table-responsive cell-border sd tableFixHead_daybook_report ">
    <table id="tableId" style="border-collapse: collapse;" class="table table-striped customers">
        <thead>
            <tr>
                <th style="width: 1%;" class="td">SL.</th>
                <th style="width: 3%;" class="td">Date</th>
                <th style="width: 3%;" class="td">Particulars</th>
                <th style="width: 2%;" class="td">Voucher Type</th>
                <th style="width: 3%;" class="td">Voucher No</th>
                <th style="width: 3%;" class="td d-none narration">Narration</th>
                <th style="width: 3%;" class="td d-none userName" >User Name</th>
                <th style="width: 3%;" class="td">Duplicate</th>
            </tr>
        </thead>
        <tbody id="myTable" class="qw">
        </tbody>
        <tfoot>
            <tr>
                <th style="width: 1%;" class="td">SL.</th>
                <th style="width: 3%;" class="td">Date</th>
                <th style="width: 3%;" class="td">Particulars</th>
                <th style="width: 2%;" class="td">Voucher Type</th>
                <th style="width: 3%;" class="td">Voucher No</th>
                <th style="width: 3%;" class="td d-none narration">Narration</th>
                <th style="width: 3%;" class="td d-none userName" >User Name</th>
                <th style="width: 3%;" class="td">Duplicate</th>
            </tr>
        </tfoot>
    </table>
</div>

@endslot
@endcomponent
<br>
@push('js')

<script>
    const vouchers=@json($vouchers);
    const user_level="{{Auth()->user()->user_level}}";
    $(document).ready(function() {

        var amount_decimals = "{{company()->amount_decimals}}";

        // day book initial show
        function get_daybook_initial_show() {
            // print_date();
            // $(".modal").show();
            // $.ajax({
            //     url: "{{ route('report.voucher.exchange.data') }}",
            //     type: 'GET',
            //     dataType: 'json',
            //     data: {
            //         to_date: $('.to_date').val(),
            //         from_date: $('.from_date').val(),
            //         voucher_id: $('.voucher_id').val()
            //     },
            //     success: function(response) {
            //         $(".modal").hide();
            //         get_daybook_val(response)
            //     },
            //     error: function(data, status, xhr) {
            //         Unauthorized(data.status);
            //     }
            // })
        }

        // day book  show
        $("#VoucherExchangeForm").submit(function(e) {
            e.preventDefault();
            print_date();
            $(".modal").show();
            const fd = new FormData(this);
            $.ajax({
                url: '{{ route("report.voucher.exchange.data") }}',
                method: 'POST',
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    $(".modal").hide();
                    get_daybook_val(response)
                },
                error: function(data, status, xhr) {
                    Unauthorized(data.status);
                }
            });
        });

        get_daybook_initial_show();

        $(document).on('click','.voucher_exchange', function() {
            let voucher_list=[];
            let voucher_type_id=0;
            console.log(vouchers);
            vouchers?.forEach(x=>{
                if(x.voucher_type_id!=$(this).attr('voucher_type_id')){
                    if(voucher_type_id!=x?.voucher_type_id){
                        voucher_type_id=x?.voucher_type_id;
                        if(user_level==1){
                            voucher_list.push(`<div class="m-2"><h5>${x?.voucher_type}</h5></div>`)
                        }else if(x?.filtered_count==x?.total_count){
                            voucher_list.push(`<div class="m-2"><h5>${x?.voucher_type}</h5></div>`)
                        }
                    }
                    voucher_list.push(`<div class="m-2"><input type="radio" name="voucher_id" class=" new_voucher_id" value="${x.voucher_id}"><span class="ms-2">${x.voucher_name}</span></div>`)
                }
            });
            $("#transaction_id").val($(this).attr('tran_id'));
            $('#voucher_exchange_modal_body').html(voucher_list.join(''));
            $('#VoucherExchange').modal("show");

        })

        $('.change_exchange_save').click(function(e) {
            let voucher_id=$('.new_voucher_id:checked').val();
            let transaction_id=$("#transaction_id").val();
            let homeRoute=$("#homeRoute").attr("href");
            let url=`${homeRoute}/report/voucher/exchange/${voucher_id}/${transaction_id}`;
            $('#VoucherExchange').modal("hide");
            window.open(url, "_blank");

        })

    });
    //redirect route
    $(document).ready(function() {
        let display_height=$(window).height();
        $('.tableFixHead_daybook_report').css('height',`${display_height-120}px`);
    });


    function get_daybook_val(response) {
        let userName = $('#userName').is(':checked');
        let narration = $('#narration').is(':checked');

        if(userName){
            $('.userName').removeClass('d-none');
        }else{
            $('.userName').addClass('d-none');
        }

        if(narration){
            $('.narration').removeClass('d-none');
        }else{
            $('.narration').addClass('d-none');
        }
        const rowsPerPage = 500; // Number of rows to display at a time
        let currentPage = 0; // Track current page
        const totalRows = response?.data?.transaction.length; // Total number of rows

        if(totalRows==0){
            $('.update-button').addClass('d-none');
        }else{
            $('.update-button').removeClass('d-none');
        }

        // Function to render a chunk of data
        function renderTableChunk(startIndex) {
            $("#myTable").empty();
            let htmlFragments = [];
            for (let i = startIndex; i < Math.min(startIndex + rowsPerPage, totalRows); i++) {
                const v = response?.data?.transaction[i];
                htmlFragments.push(`
                    <tr class="left left-data editIcon table-row">
                        <td style="width: 1%;" class="td">${(i + 1)}</td>
                        <td style="width: 3%;" class="td">${join(new Date(v.transaction_date), options, ' ')}</td>
                        <td style="width: 3%;" class="td font text-wrap">${(v.ledger_name || '')}</td>
                        <td style="width: 2%;" class="td font text-wrap">
                            ${redirectVoucherIdWise(v.voucher_type_id,v.tran_id,v.voucher_name)}
                        </td>
                        <td style="width: 3%;" class="td font">${v.invoice_no}</td>
                        <td style="width: 3%;" class="td font ${narration ? '' : 'd-none'}">${v?.narration || ''}</td>
                        <td style="width: 3%;" class="td font ${userName ? '' : 'd-none'}">${v?.user_name || ''}</td>
                        <td style="width: 3%;" class="td font">
                            <button type="button" class="btn btn-sm btn-success voucher_exchange" value="${v.tran_id}" tran_id="${v.tran_id}" voucher_id="${v.voucher_id}" voucher_type_id="${v.voucher_type_id}">Duplicate</button>
                        </td>
                `);
                htmlFragments.push(`</tr>`);
            }

            $("#myTable").append(htmlFragments.join('')); // Append chunk to the table body

            // Load next chunk if there are more rows
            if (startIndex + rowsPerPage < totalRows) {
                setTimeout(() => renderTableChunk(startIndex + rowsPerPage), 0); // Use timeout for UI responsiveness
            }
            get_hover();
        }

        renderTableChunk(currentPage * rowsPerPage);

    }
</script>
@endpush
@endsection
