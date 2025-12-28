 
 function voucher_edit_redirect (day_book_arr){
    if (day_book_arr[1] == 14) {
        window.open(`{{url('voucher-receipt/edit')}}/${day_book_arr[0]}`, '_blank');
  } else if (day_book_arr[1] == 8) {
      window.open(`{{url('voucher-payment')}}/${day_book_arr[0]}/edit`, '_blank');
  } else if (day_book_arr[1] == 1) {
      window.open(`{{url('voucher-contra')}}/${day_book_arr[0]}/edit`, '_blank');
  } else if (day_book_arr[1] == 10) {
      window.open(`{{url('voucher-purchase')}}/${day_book_arr[0]}/edit`, '_blank');
  } else if (day_book_arr[1] == 24) {
      window.open(`{{url('voucher-grn')}}/${day_book_arr[0]}/edit`, '_blank');
  } else if (day_book_arr[1] == 19) {
      window.open(`{{url('voucher-sales')}}/${day_book_arr[0]}/edit`, '_blank');
  } else if (day_book_arr[1] == 23) {
      window.open(`{{url('voucher-gtn')}}/${day_book_arr[0]}/edit`, '_blank');
  } else if (day_book_arr[1] == 29) {
      window.open(`{{url('voucher-purchase-return')}}/${day_book_arr[0]}/edit`, '_blank');
  } else if (day_book_arr[1] == 22) {
      window.open(`{{url('voucher-transfer')}}/${day_book_arr[0]}/edit`, '_blank');
  } else if (day_book_arr[1] == 25) {
      window.open(`{{url('voucher-sales-return')}}/${day_book_arr[0]}/edit`, '_blank');
  } else if (day_book_arr[1] == 21) {
      window.open(`{{url('voucher-stock-journal')}}/${day_book_arr[0]}/edit`, '_blank');
  } else if (day_book_arr[1] == 6) {
      window.open(`{{url('voucher-journal')}}/${day_book_arr[0]}/edit`, '_blank');
  } else if (day_book_arr[1] == 28) {
      window.open(`{{url('voucher-commission')}}/${day_book_arr[0]}/edit`, '_blank');
  }
  else if (day_book_arr[1] == 20) {
      window.open(`{{url('voucher-sales-order')}}/${day_book_arr[0]}/edit`, '_blank');
  }
}