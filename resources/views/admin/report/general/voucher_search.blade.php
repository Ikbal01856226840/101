@extends('layouts.backend.app')
@section('title','Voucher Search')
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
<!-- add component-->
@component('components.report', [
    'title' => 'Voucher Search',
    'print_layout'=>'landscape',
    'print_header'=>'Voucher Search',
    'user_privilege_title'=>'VoucherSearch',
    'print_date'=>1,
]);

<!-- Page-header component -->
@slot('header_body')
<form id="VoucherSearchForm" method="POST">
    @csrf
    {{ method_field('POST') }}
    <div class="row searchOption">
        <div class="col-md-5 row">
            <div class="col-md-3">
                <label>Having :</label>
            </div>
            <div class="col-md-9">
                <select class="form-control SearchType js-example-basic-single" name="SearchTypeOne">
                    <option value="0">--Select One--</option>
                    <option value="1">Ledger / Particulars</option>
                    <option value="2">Voucher Type</option>
                    <option value="3">Voucher No / Invoice No</option>
                    <option value="4">Narration</option>
                </select>
            </div>
        </div>
        <div class="col-md-5 row">
            <div class="col-md-3">
                <label>Range of Info :</label>
            </div>
            <div class="col-md-9 SearchRange">
            </div>
        </div>
    </div>
    <div class="row searchOption">
        <div class="col-md-5 row">
            <div class="col-md-3">
                <label>Having :</label>
            </div>
            <div class="col-md-9">
                <select class="form-control SearchType js-example-basic-single" name="SearchTypeTwo">
                    <option value="0">--Select One--</option>
                    <option value="1">Ledger / Particulars</option>
                    <option value="2">Voucher Type</option>
                    <option value="3">Voucher No / Invoice No</option>
                    <option value="4">Narration</option>
                </select>
            </div>
        </div>
        <div class="col-md-5 row">
            <div class="col-md-3">
                <label>Range of Info :</label>
            </div>
            <div class="col-md-9 SearchRange">
            </div>
        </div>
    </div>
    <div class="row searchOption">
        <div class="col-md-5 row">
            <div class="col-md-3">
                <label>Having :</label>
            </div>
            <div class="col-md-9">
                <select class="form-control SearchType js-example-basic-single" name="SearchTypeThree">
                    <option value="0">--Select One--</option>
                    <option value="1">Ledger / Particulars</option>
                    <option value="2">Voucher Type</option>
                    <option value="3">Voucher No / Invoice No</option>
                    <option value="4">Narration</option>
                </select>
            </div>
        </div>
        <div class="col-md-5 row">
            <div class="col-md-3">
                <label>Range of Info :</label>
            </div>
            <div class="col-md-9 SearchRange">

            </div>

        </div>
        <div class="col-md-2">
            <div>
                <input class="form-check-input" type="checkbox" id="narration" name="narration" value="1">
                <label class="form-check-label fs-6" for="flexRadioDefault1">
                    Narration
                </label>
            </div>
        </div>
    </div>
    <div class="row searchOption">
        <div class="col-md-5 row">
            <div class="col-md-3">
                <label>Having :</label>
            </div>
            <div class="col-md-9">
                <select class="form-control SearchType js-example-basic-single" name="SearchTypeFour">
                    <option value="0">--Select One--</option>
                    <option value="1">Ledger / Particulars</option>
                    <option value="2">Voucher Type</option>
                    <option value="3">Voucher No / Invoice No</option>
                    <option value="4">Narration</option>
                </select>
            </div>
        </div>
        <div class="col-md-5 row">
            <div class="col-md-3">
                <label>Range of Info :</label>
            </div>
            <div class="col-md-9 SearchRange">
            </div>
        </div>
        <div class="col-md-2">
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
            </tr>
        </tfoot>
    </table>
    {{-- <table>
        <tr>
            <th style="width: 20%;">Period Types </th>
            <th style="width: 1%;">:</th>
            <th></th>
        </tr>
        <tr>
            <th style="width: 20%;">Apply From</th>
            <th style="width: 1%;">:</th>
            <th></th>
        </tr>
        <tr>
            <th style="width: 20%;">Starting Number</th>
            <th style="width: 1%;">:</th>
            <th></th>
        </tr>
        <tr>
            <th style="width: 20%;">Current Number</th>
            <th style="width: 1%;">:</th>
            <th></th>
        </tr>
        <tr>
            <th style="width: 20%;">Set Current Number to</th>
            <th style="width: 1%;">:</th>
            <th></th>
        </tr>
    </table> --}}
    {{-- <div class="row text-center update-button d-none">
        <div class="col-md-12">
            <button type="button" class="btn hor-grd btn-grd-info btn-block" style="width:200px; margin-bottom:5px;" id="back">Back</button>
            <button type="button" class="btn hor-grd btn-grd-primary btn-block" style="width:200px; margin-bottom:5px;" id="save">Save</button>
        </div>
    </div> --}}
</div>

@endslot
@endcomponent
<br>
@push('js')

<script>
    const vouchers = {!! json_encode($vouchers) !!};

    $(document).ready(function() {
        let voucher_type_id= 0;
        let user_level="{{Auth()->user()->user_level}}";
        const voucher_options=vouchers?.map(x=>{
            if(voucher_type_id!=x?.voucher_type_id){
                voucher_type_id=x?.voucher_type_id;
                if(user_level==1){
                    return `<option style="color:red;" value="v${x?.voucher_type_id}">${x?.voucher_type}</option>`
                }else if(x?.filtered_count==x?.total_count){
                    return `<option style="color:red;" value="v${x?.voucher_type_id}">${x?.voucher_type}</option>`
                }
            }
            return `<option value="${x?.voucher_id}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;${x?.voucher_name}</option>`
        }).join('');
        var amount_decimals = "{{company()->amount_decimals}}";
        $('.SearchType').change(function() {
            let range = '';
            let name=$(this).attr('name')=='SearchTypeOne'?'SearchRangeOne':
                        $(this).attr('name')=='SearchTypeTwo'?'SearchRangeTwo':
                            $(this).attr('name')=='SearchTypeThree'?'SearchRangeThree':
                                $(this).attr('name')=='SearchTypeFour'?'SearchRangeFour':'';
            if($(this).val() == 1) {
                range =`
                    <input type="text" class="form-control ledger_id" placeholder="Enter Ledger Name"/>
                        <input
                            type="number"
                            name="${name}"
                            class=" d-none party_ledger_auto_completed_id rangeValue"
                        />
                        `;
            }else if($(this).val() == 2) {
                range =`<select class="form-control rangeValue js-example-basic-single"  name="${name}">
                            <option value="0">--Select One--</option>
                            ${voucher_options}
                        </select>`;
            }else if($(this).val() == 3) {
                range =`<input type="text"  name="${name}" class="form-control rangeValue"  placeholder="Enter Voucher No / Invoice No">`
            }else if($(this).val() == 4) {
                range =`<textarea class="form-control rangeValue"  name="${name}" placeholder="Enter Narration"></textarea>`
            }
            $(this).closest('.searchOption').find('.SearchRange').html(range);
            if($(this).val() == 2) {
                $(this).closest('.searchOption').find('.js-example-basic-single').select2();
            }else if($(this).val() == 1) {
                initializeAutocomplete.call($(this).closest('.searchOption').find('.ledger_id'));
            }
        })

        // day book initial show
        function get_daybook_initial_show() {
            print_date();
            $(".modal").show();
            $.ajax({
                url: "{{ route('report.voucher.search.data') }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    to_date: $('.to_date').val(),
                    from_date: $('.from_date').val(),
                    voucher_id: $('.voucher_id').val()
                },
                success: function(response) {
                    $(".modal").hide();
                    get_daybook_val(response)
                },
                error: function(data, status, xhr) {
                    $(".modal").hide();
                    Unauthorized(data.status);
                },
                complete: function() {
                    $(".modal").hide();
                }
            })
        }

        // day book  show
        $("#VoucherSearchForm").submit(function(e) {
            e.preventDefault();
            print_date();
            $(".modal").show();
            const fd = new FormData(this);
            $.ajax({
                url: '{{ route("report.voucher.search.data") }}',
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

        // get_daybook_initial_show();

    });
    //redirect route
    $(document).ready(function() {
        let display_height=$(window).height();
        $('.tableFixHead_daybook_report').css('height',`${display_height-120}px`);
    });


    function get_daybook_val(response) {

        const rowsPerPage = 500; // Number of rows to display at a time
        let currentPage = 0; // Track current page
        const totalRows = response?.data?.transaction.length; // Total number of rows
        let narration = $('#narration').is(':checked');
        if(totalRows==0){
            $('.update-button').addClass('d-none');
        }else{
            $('.update-button').removeClass('d-none');
        }

        if(narration){
            $('.narration').removeClass('d-none');
        }else{
            $('.narration').addClass('d-none');
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
                        <td style="width: 3%;" class="td font ${narration? '' : 'd-none'}">${v?.narration || ''}</td>
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
