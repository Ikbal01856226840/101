<?php

namespace App\Repositories\Backend\Master;

use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VoucherRepository implements VoucherInterface
{
    public function getVoucherOfIndex()
    {
        return Voucher::select('voucher_type_id', 'voucher_name', 'voucher_id', 'user_name', 'other_details')->with('voucher_type:voucher_type_id,voucher_type:voucher_type')->orderBy('voucher_type_id', 'ASC')->get();
    }

    public function StoreVoucher($request)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $data = new Voucher();
        $data->voucher_name = $request->voucher_name;
        $data->voucher_type_id = $request->voucher_type_id;
        $data->category = $request->category;
        $data->branch_id = $request->branch_id;
        $data->godown_id = $request->godown_id;
        $data->godown_motive = $request->godown_motive;
        $data->debit_group_id = implode(',', $request->debit_group_id_array);
        $data->credit_group_id = implode(',', $request->credit_group_id_array);
        $data->select_date = $request->select_date;
        if ($request->select_date == 'fix_date') {
            $data->fix_date_create = $request->fix_date_create;
        }
        $data->debit = $request->debit ?? '';
        $data->credit = $request->credit ?? '';
        $data->vouchernumbermethod = $request->vouchernumbermethod;
        $data->manual_text = $request->manual_text ?? '';
        $data->time_frame_year = $request->time_frame_year ?? '';
        $data->time_frame_month = $request->time_frame_month ?? '';
        $data->time_frame_day = $request->time_frame_day ?? '';
        $data->time_frame = ($request->text_year.$request->text_month.$request->text_time);
        $data->current_no = $request->current_no;
        $data->invoice = $request->vouchernumbermethod == 4 ? '' : $request->invoice;
        $data->auto_reset_period = $request->auto_reset_period;
        $data->starting_number = $request->starting_number;
        $data->dup_row = $request->dup_row ?? '0';
        $data->dc_amnt = $request->dc_amnt ?? '0';
        $data->dc_equl = $request->dc_equl ?? '0';
        $data->remark_is = $request->remark_is ?? '0';
        $data->commission_is = $request->commission_is ?? 0;
        $data->amnt_typeable = $request->amnt_typeable ?? '0';
        $data->stock_item_price_typeabe = $request->stock_item_price_typeabe ?? '0';
        $data->amount_typeabe = $request->amount_typeabe ?? '0';
        $data->price_type_id = $request->price_type_id ?? '0';
        $data->commission_type_id = $request->commission_type_id ?? 0;
        $data->total_qty_is = $request->total_qty_is ?? '0';
        $data->total_price_is = $request->total_price_is ?? '0';
        $data->place_na_top = $request->place_na_top ?? '0';
        $data->allow_stock_item = $request->allow_stock_item ?? '0';
        $data->ch_4_dup_vou_no = $request->ch_4_dup_vou_no ?? '0';
        $data->comm_4_each_item = $request->comm_4_each_item ?? '0';
        $data->multi_level_comm = $request->multi_level_comm ?? '0';
        $data->d_in_date = $request->d_in_date ?? '0';
        $data->is_pary_ledger=$request->is_pary_ledger ?? '0';
        $data->user_id = Auth::id();
        $data->other_details = json_encode('Created On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').', By: '.Auth::user()->user_name.', Ip: '.$ip);
        $data->user_name = Auth::user()->user_name;
        $data->destination_godown_id = $request->destination_godown_id ?? '0';
        $data->destrination_price_type_id = $request->destrination_price_type_id ?? '0';
        $data->save();
        DB::table('track_voucher_setup')->insert(
            [
                'user_id' => auth()->id(),
                'invoice' => $request->invoice,
                'voucher_id' => $data->voucher_id,
                'updated_date' => date('Y-m-d'),
            ]
        );

        return $data;
    }

    public function getVoucherId($id)
    {
        return Voucher::where('voucher_id', $id)->first();
    }

    public function updateVoucher(Request $request, $id)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $data = Voucher::findOrFail($id);
        $data->voucher_name = $request->voucher_name;
        $data->voucher_type_id = $request->voucher_type_id;
        $data->category = $request->category;
        $data->branch_id = $request->branch_id;
        $data->godown_id = $request->godown_id;
        $data->godown_motive = $request->godown_motive;
        $data->debit_group_id = implode(',', $request->debit_group_id_array);
        $data->credit_group_id = implode(',', $request->credit_group_id_array);
        $data->select_date = $request->select_date;
        if ($request->select_date == 'fix_date') {
            $data->fix_date_create = $request->fix_date_create;
        }
        $data->debit = $request->debit ?? '';
        $data->credit = $request->credit ?? '';
        $data->vouchernumbermethod = $request->vouchernumbermethod;
        $data->manual_text = $request->manual_text ?? '';
        $data->time_frame_year = $request->time_frame_year ?? '';
        $data->time_frame_month = $request->time_frame_month ?? '';
        $data->time_frame_day = $request->time_frame_day ?? '';
        $data->time_frame = ($request->text_year.$request->text_month.$request->text_time);
        $data->current_no = $request->current_no;
        $data->invoice = $request->vouchernumbermethod == 4 ? '' : $request->invoice;
        $data->auto_reset_period = $request->auto_reset_period;
        $data->starting_number = $request->starting_number;
        $data->dup_row = $request->dup_row ?? '0';
        $data->dc_amnt = $request->dc_amnt ?? '0';
        $data->dc_equl = $request->dc_equl ?? '0';
        $data->remark_is = $request->remark_is ?? '0';
        $data->commission_is = $request->commission_is ?? 0;
        $data->amnt_typeable = $request->amnt_typeable ?? '0';
        $data->stock_item_price_typeabe = $request->stock_item_price_typeabe ?? '0';
        $data->amount_typeabe = $request->amount_typeabe ?? '0';
        $data->price_type_id = $request->price_type_id ?? '0';
        $data->commission_type_id = $request->commission_type_id ?? 0;
        $data->total_qty_is = $request->total_qty_is ?? '0';
        $data->total_price_is = $request->total_price_is ?? '0';
        $data->place_na_top = $request->place_na_top ?? '0';
        $data->allow_stock_item = $request->allow_stock_item ?? '0';
        $data->ch_4_dup_vou_no = $request->ch_4_dup_vou_no ?? '0';
        $data->comm_4_each_item = $request->comm_4_each_item ?? '0';
        $data->multi_level_comm = $request->multi_level_comm ?? '0';
        $data->d_in_date = $request->d_in_date ?? '0';
        $data->is_pary_ledger=$request->is_pary_ledger ?? '0';
        $update_history = json_decode($data->other_details);
        $data->other_details = json_encode($update_history.'<br> Updated On: '.\Carbon\Carbon::now()->format('D, d M Y g:i:s A').', By: '.Auth::user()->user_name.', Ip: '.$ip);
        $data->destination_godown_id = $request->destination_godown_id ?? '0';
        $data->destrination_price_type_id = $request->destrination_price_type_id ?? '0';
        $data->save();
        DB::table('track_voucher_setup')->insert(
            [
                'user_id' => auth()->id(),
                'voucher_id' => $id,
                'invoice' => $request->vouchernumbermethod == 4 ? '' : $request->invoice,
                'updated_date' => date('Y-m-d'),
            ]
        );

        return $data;
    }

    public function deleteVoucher($id)
    {
        return Voucher::findOrFail($id)->delete();
    }

    public function current_invoice($id)
    {
        $transaction_voucher = DB::table('transaction_master')->where('voucher_id', $id)->orderBy('tran_id', 'DESC')->first();
        if ($transaction_voucher) {
            return preg_match('/\d+(?=\D*$)/', $transaction_voucher->invoice_no, $m) ? $m[0] : '';
        } else {
            $current_no = Voucher::where('voucher_id', $id)->first(['current_no']);

            return $current_no->current_no;
        }
    }

    public function next_invoice($id)
    {
        $transaction_voucher = DB::table('transaction_master')->where('voucher_id', $id)->orderBy('tran_id', 'DESC')->first();
        if ($transaction_voucher) {
            return $transaction_voucher->invoice_no;
        } else {
            $current_no = Voucher::where('voucher_id', $id)->first(['current_no']);

            return $current_no->current_no;
        }
    }

    public function voucher_specific_data()
    {
        return DB::table('voucher_setup')->select('voucher_setup.voucher_type_id', 'voucher_setup.voucher_name', 'voucher_setup.voucher_id', 'voucher_type.voucher_type')->leftJoin('voucher_type', 'voucher_type.voucher_type_id', '=', 'voucher_setup.voucher_type_id')->orderBy('voucher_setup.voucher_type_id', 'ASC')->get();
    }
}
