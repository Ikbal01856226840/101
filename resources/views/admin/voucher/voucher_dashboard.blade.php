@extends('layouts.backend.app')
@section('title','Dashboard Voucher')
@push('css')
<style>
    @media only screen and (max-width: 991px) {
        .main-body .page-wrapper .align-items-end .page-header-title,
        .main-body .page-wrapper .align-items-end .page-header-breadcrumb {
            margin-top: 0px !important;
        }
        .main-body .page-wrapper .page-header {
            margin-bottom: 0px;
        }
    }
</style>
@endpush
@section('admin_content')
<div class="pcoded-main-container navChild">
  <div class="pcoded-content  ">
    <div class="pcoded-inner-content  ">
      <!-- Main-body start -->
      <div class="main-body  side-component">
        <div class="page-wrapper m-t-0 p-0">
          <!-- Page-header start -->
          <div class="page-header m-0 p-0">
            <div class="row align-items-end">
              <div class="col-lg-8">
                <div class="page-header-title">
                  <div class="d-inline ">
                    <h4 class="text-center mx-auto"></h4>
                  </div>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="page-header-breadcrumb">
                </div>
              </div>
            </div>
          </div>
          <!-- Page-header end -->
          <!-- Page body start -->
          <div class="page-body left-data">
            <!-- Basic Form Inputs card start -->
            <div class="card">
              <div class="row ms-2">
                <div class="col-md-4">
                  <h5 style="background-color:#CCCCCC" class="text-center inword">Inward Vouchers</h5>
                  @foreach ($purchases as $key=>$purchase )
                    @if(user_privileges_check('Voucher',$purchase->voucher_id,'display_role'))
                    @php $pu=1 @endphp
                      <li class="m-1 inword_voucher voucher_type {{Route::is('voucher-purchase.create') ? 'activedata' : ''}}"><a style=" text-decoration: none; font-size: 15px;color:#0B55C4;" href="{{route('voucher-purchase.show',$purchase->voucher_id)}}" data-turbolinks="false">{{$purchase->voucher_name}}</a></li>
                    @endif
                  </ul>
                  @endforeach
                    @if(isset($pu))
                     <p class="w3-margin-top w3-bottombar m-1" style="background-color:#eee; border-bottom: 6px solid #ccc!important;"></p>
                    @endif
                  @foreach ($order_requisitions as $key=>$order_requisition)
                        @if(user_privileges_check('Voucher',$order_requisition->voucher_id,'display_role'))
                          @php $or=1 @endphp
                            <li class="m-1 inword_voucher voucher_type {{Route::is('voucher-order-requisition.create') ? 'activedata' : ''}}"><a style=" text-decoration: none; font-size: 15px;color:#0B55C4;" href="{{route('voucher-order-requisition.show',$order_requisition->voucher_id)}}" data-turbolinks="false">{{$order_requisition->voucher_name}}</a></li>
                        @endif
                    </ul>
                  @endforeach

                  @if(isset($or))
                   <p class="w3-margin-top w3-bottombar m-1" style="background-color:#eee; border-bottom: 6px solid #ccc!important;"></p>
                  @endif
                  @foreach ($receives as $key=>$receve)
                    @if(user_privileges_check('Voucher',$receve->voucher_id,'display_role'))
                    @php $re=1 @endphp
                     <li class="m-1 voucher_type inword_voucher {{Route::is('voucher-purchase.create') ? 'activedata' : ''}}"><a style=" text-decoration: none; font-size: 15px;color:#0B55C4;" href="{{route('voucher-receipt.show',$receve->voucher_id)}}" data-turbolinks="false">{{$receve->voucher_name}}</a></li>
                    @endif
                  </ul>
                  @endforeach
                  @if(isset($re))
                   <p class="w3-margin-top w3-bottombar m-1" style="background-color:#eee; border-bottom: 6px solid #ccc!important;"></p>
                  @endif
                  @foreach ($grns as $key=>$grn)
                    @if(user_privileges_check('Voucher',$grn->voucher_id,'display_role'))
                    @php $gr=1 @endphp
                      <li class="m-1 voucher_type inword_voucher {{Route::is('voucher-grn.create') ? 'activedata' : ''}}"><a style=" text-decoration: none; font-size: 15px;color:#0B55C4;" href="{{route('voucher-grn.show',$grn->voucher_id)}}" data-turbolinks="false">{{$grn->voucher_name}}</a></li>
                    @endif
                  </ul>
                  @endforeach
                  @if(isset($gr))
                   <p class="w3-margin-top w3-bottombar m-1" style="background-color:#eee; border-bottom: 6px solid #ccc!important;"></p>
                  @endif
                  @foreach ($sales_return as $key=>$sale_return)
                    @if(user_privileges_check('Voucher',$sale_return->voucher_id,'display_role'))
                    @php $sr=1 @endphp
                      <li class="m-1 voucher_type inword_voucher {{Route::is('voucher-purchase.create') ? 'activedata' : ''}}"><a style=" text-decoration: none; font-size: 15px;color:#0B55C4;" href="{{route('voucher-sales-return.show',$sale_return->voucher_id)}}" data-turbolinks="false">{{$sale_return->voucher_name}}</a></li>
                    @endif
                  </ul>
                  @endforeach
                  @if(isset($sr))
                   <p class="w3-margin-top w3-bottombar m-1" style="background-color:#eee; border-bottom: 6px solid #ccc!important;"></p>
                  @endif
                </div>
                <div class="col-md-4">
                  <h5 style="background-color:#CCCCCC" class="text-center outwark">Outward Vouchers</h5>
                  @foreach ($payments as $key=>$payment )
                    @if(user_privileges_check('Voucher',$payment->voucher_id,'display_role'))
                    @php $pa=1 @endphp
                     <li class="m-1 outwark_voucher"><a style=" text-decoration: none; font-size: 15px;color:#0B55C4;" href="{{route('voucher-payment.show',$payment->voucher_id)}}" data-turbolinks="false">{{$payment->voucher_name}}</a></li>
                    @endif
                  </ul>
                  @endforeach
                  @if(isset($pa))
                   <p class="w3-margin-top w3-bottombar m-1" style="background-color:#eee; border-bottom: 6px solid #ccc!important;"></p>
                  @endif
                  @foreach ($sales as $key=>$sale)
                    @if(user_privileges_check('Voucher',$sale->voucher_id,'display_role'))
                    @php $sa=1 @endphp
                     <li class="m-1 outwark_voucher"><a style=" text-decoration: none; font-size: 15px;color:#0B55C4;" href="{{route('voucher-sales.show',$sale->voucher_id)}}" data-turbolinks="false">{{$sale->voucher_name}}</a></li>
                    @endif
                  </ul>
                  @endforeach
                  @if(isset($sa))
                   <p class="w3-margin-top w3-bottombar m-1" style="background-color:#eee; border-bottom: 6px solid #ccc!important;"></p>
                  @endif
                  @foreach ($sales_pos as $key=>$sales_pos)
                    @if(user_privileges_check('Voucher',$sales_pos->voucher_id,'display_role'))
                    @php $spos=1 @endphp
                     <li class="m-1 outwark_voucher"><a style=" text-decoration: none; font-size: 15px;color:#0B55C4;" href="{{route('voucher-pos.show',$sales_pos->voucher_id)}}">{{$sales_pos->voucher_name}}</a></li>
                    @endif
                  </ul>
                  @endforeach
                  @if(isset($spos))
                   <p class="w3-margin-top w3-bottombar m-1" style="background-color:#eee; border-bottom: 6px solid #ccc!important;"></p>
                  @endif
                  @foreach ($sales_exchanges as $key=>$sales_exchange)
                    @if(user_privileges_check('Voucher',$sales_exchange->voucher_id,'display_role'))
                    @php $epos=1 @endphp
                     <li class="m-1 outwark_voucher"><a style=" text-decoration: none; font-size: 15px;color:#0B55C4;" href="{{route('voucher-pos-exchange.show',$sales_exchange->voucher_id)}}">{{$sales_exchange->voucher_name}}</a></li>
                    @endif
                  </ul>
                  @endforeach
                  @if(isset($epos))
                   <p class="w3-margin-top w3-bottombar m-1" style="background-color:#eee; border-bottom: 6px solid #ccc!important;"></p>
                  @endif
                  @foreach ($gtns as $key=>$gtn)
                    @if(user_privileges_check('Voucher',$gtn->voucher_id,'display_role'))
                     @php $gtn1=1 @endphp
                     <li class="m-1 outwark_voucher"><a style=" text-decoration: none; font-size: 15px;color:#0B55C4;" href="{{route('voucher-gtn.show',$gtn->voucher_id)}}" data-turbolinks="false">{{$gtn->voucher_name}}</a></li>
                    @endif
                  </ul>
                  @endforeach
                  @if(isset($gtn1))
                    <p class="w3-margin-top w3-bottombar m-1" style="background-color:#eee; border-bottom: 6px solid #ccc!important;"></p>
                  @endif
                  @foreach ($purchase_returns as $key=>$purchase_return)
                    @if(user_privileges_check('Voucher',$purchase_return->voucher_id,'display_role'))
                    @php $pr=1 @endphp
                      <li class="m-1 outwark_voucher"><a style=" text-decoration: none; font-size: 15px;color:#0B55C4;" href="{{route('voucher-purchase-return.show',$purchase_return->voucher_id)}}" data-turbolinks="false">{{$purchase_return->voucher_name}}</a></li>
                    @endif
                  </ul>
                  @endforeach
                  @if(isset($pr))
                   <p class="w3-margin-top w3-bottombar m-1" style="background-color:#eee; border-bottom: 6px solid #ccc!important;"></p>
                  @endif
                  @foreach ($adjustments as $key=>$adjustment)
                    @if(user_privileges_check('Voucher',$adjustment->voucher_id,'display_role'))
                    @php $ad=1 @endphp
                    <li class="m-1 outwark_voucher"><a style=" text-decoration: none; font-size: 15px;color:#0B55C4;" href="{{route('voucher-transfer.show',$adjustment->voucher_id)}}" data-turbolinks="false">{{$adjustment->voucher_name}}</a></li>
                    @endif
                  </ul>
                  @endforeach
                  @if(isset($ad))
                   <p class="w3-margin-top w3-bottombar m-1" style="background-color:#eee; border-bottom: 6px solid #ccc!important;"></p>
                  @endif
                </div>
                <div class="col-md-4">
                  <h5 style="background-color:#CCCCCC" class="text-center journal">Journal & General Vouchers</h5>
                  @foreach ($contra as $key=>$con )
                    @if(user_privileges_check('Voucher',$con->voucher_id,'display_role'))
                    @php $co=1 @endphp
                     <li class="m-1 journal_voucher"><a style=" text-decoration: none; font-size: 15px;color:#0B55C4;" href="{{route('voucher-contra.show',$con->voucher_id)}}" data-turbolinks="false">{{$con->voucher_name}}</a></li>
                    @endif
                  </ul>
                  @endforeach
                  @if(isset($co))
                   <p class="w3-margin-top w3-bottombar m-1 " style="background-color:#eee; border-bottom: 6px solid #ccc!important;"></p>
                  @endif
                  @foreach ($journals as $key=>$journal)
                    @if(user_privileges_check('Voucher',$journal->voucher_id,'display_role'))
                    @php $jo=1 @endphp
                     <li class="m-1 journal_voucher"><a style=" text-decoration: none; font-size: 15px;color:#0B55C4;" href="{{route('voucher-journal.show',$journal->voucher_id)}}" data-turbolinks="false">{{$journal->voucher_name}}</a></li>
                    @endif
                  </ul>
                  @endforeach
                  @if(isset($jo))
                   <p class="w3-margin-top w3-bottombar m-1 " style="background-color:#eee; border-bottom: 6px solid #ccc!important;"></p>
                  @endif
                  @foreach ($jv as $key=>$data)
                    @if(user_privileges_check('Voucher',$data->voucher_id,'display_role'))
                    @php $jv1=1 @endphp
                      <li class="m-1 journal_voucher"><a style=" text-decoration: none; font-size: 15px;color:#0B55C4;" href="{{route('voucher-stock-journal.show',$data->voucher_id)}}" data-turbolinks="false">{{$data->voucher_name}}</a></li>
                    @endif
                  </ul>
                  @endforeach
                  @if(isset($jv1))
                   <p class="w3-margin-top w3-bottombar m-1 V" style="background-color:#eee; border-bottom: 6px solid #ccc!important;"></p>
                  @endif
                  @foreach ($commissions as $key=>$commission)
                    @if(user_privileges_check('Voucher',$commission->voucher_id,'display_role'))
                    @php $com=1 @endphp
                      <li class="m-1 journal_voucher"><a style=" text-decoration: none; font-size: 15px;color:#0B55C4;" href="{{route('voucher-commission.show',$commission->voucher_id)}}" data-turbolinks="false">{{$commission->voucher_name}}</a></li>
                    @endif
                    </ul>
                  @endforeach
                  @if(isset($com))
                    <p class="w3-margin-top w3-bottombar m-1" style="background-color:#eee; border-bottom: 6px solid #ccc!important;"></p>
                  @endif
                  @foreach ($sales_orders as $key=>$sales_order)
                    @if(user_privileges_check('Voucher',$sales_order->voucher_id,'display_role'))
                    @php $so=1 @endphp
                      <li class="m-1 journal_voucher"><a style=" text-decoration: none; font-size: 15px;color:#0B55C4;" href="{{route('voucher-sales-order.show',$sales_order->voucher_id)}}">{{$sales_order->voucher_name}}</a></li>
                    @endif
                  </ul>
                  @endforeach
                  @if(isset($so))
                    <p class="w3-margin-top w3-bottombar m-1" style="background-color:#eee; border-bottom: 6px solid #ccc!important;"></p>
                  @endif
                </div>
                <div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    @push('js')
    <script>
      let inword_voucher = document.getElementsByClassName('inword_voucher');
      let outwark_voucher = document.getElementsByClassName('outwark_voucher');
      let journal_voucher = document.getElementsByClassName('journal_voucher');
      if (inword_voucher.length == 0) {
        $(".inword").css("display", "none");
      }
      if (outwark_voucher.length == 0) {
        $(".outwark").css("display", "none");
      }
      if (journal_voucher.length == 0) {
        $(".journal").css("display", "none");
      }
    </script>

    @endpush
    @endsection
