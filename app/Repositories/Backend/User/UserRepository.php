<?php

namespace App\Repositories\Backend\User;

use App\Models\User;
use App\Models\UserPrivilege;
use App\Models\UserPrivilegeInsertUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\User\UserTracking;
use App\Models\UserLog;
class UserRepository implements UserInterface
{
    private $userTracking;
    public function __construct(UserTracking $userTracking)
    {
        $this->userTracking = $userTracking;
    }
    
    public function getUserOfIndex()
    {
        return User::all('id','log_in_id', 'user_name', 'user_level', 'activity');
    }

    public function StoreUser(Request $request)
    {
        dd($request->all());
    }

    public function getUserId($id)
    {

    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->log_in_id = $request->log_in_id;
        $user->user_level = $request->user_level;
        $user->locked = $request->locked;
        $user->activity = $request->activity;
        $user->unit_or_branch = $request->unit_or_branch;
        $user->active_time_start = $request->active_time_start;
        $user->active_time_end = $request->active_time_end;
        $user->user_name = $request->user_name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->phone_2 = $request->phone_2;
        $user->phone_3 = $request->phone_3;
        $user->address = $request->address;
        $user->insert_date = $request->insert_date;
        $user->godown_id = implode(',', $request->godown_id_array);
        $user->stock_group_id_multiple = implode(',', $request->stock_group_id_multiple??'');
        if(!empty($request->dis_cen_id)){
            $user->dis_cen_id =implode(',', $request->dis_cen_id); ;
        }else{
            $user->dis_cen_id ='';
        }
        // $user->voucher_upadte=$request->voucher_upadte;
        $user->agar = implode(',', $request->group_id_array);
        if (! empty($request->password)) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return $user;
    }

    public function deleteUser($id)
    {
        return User::findOrFail($id)->delete();
    }

    public function userPrivilegeSet($id)
    {
        return UserPrivilege::where('table_user_id', $id)->get();
    }

    public function userPrivilegeStore($request)
    {
        Cache::flush();
        if (! empty($request->p_id)) {
            // $user_privilege_insert_update_p = UserPrivilegeInsertUpdate::findOrFail($request->p_id);
            // $user_privilege_insert_update_p->user_id = $request->user_id;
            // $user_privilege_insert_update_p->status_type = 'insert';
            // $user_privilege_insert_update_p->create_or_update = $request->p_create_or_update ?? 0;
            // if ($request->p_create_or_update == 1) {
            //     if (! empty($request->p_specific_date)) {
            //         $user_privilege_insert_update_p->specific_date = date('Y-m-d', strtotime($request->p_specific_date));
            //     } else {
            //         $user_privilege_insert_update_p->number = $request->p_number;
            //     }
            // }
            // $user_privilege_insert_update_p->allow = $request->p_allow_date;
            // $user_privilege_insert_update_p->save();
        } else {
            // $user_privilege_insert_update_p = new UserPrivilegeInsertUpdate();
            // $user_privilege_insert_update_p->user_id = $request->user_id;
            // $user_privilege_insert_update_p->status_type = 'insert';
            // $user_privilege_insert_update_p->create_or_update = $request->p_create_or_update ?? 0;
            // if ($request->p_create_or_update == 1) {
            //     if (! empty($request->p_specific_date)) {
            //         $user_privilege_insert_update_p->specific_date = date('Y-m-d', strtotime($request->p_specific_date));
            //     } else {
            //         $user_privilege_insert_update_p->number = $request->p_number;
            //     }
            // }
            // $user_privilege_insert_update_p->allow = $request->p_allow_date;
            // $user_privilege_insert_update_p->save();
        }
        if (! empty($request->f_id)) {
            // $user_privilege_insert_update_f = UserPrivilegeInsertUpdate::findOrFail($request->f_id);
            // $user_privilege_insert_update_f->user_id = $request->user_id;
            // $user_privilege_insert_update_f->status_type = 'insert';
            // $user_privilege_insert_update_f->create_or_update = $request->f_create_or_update ?? 0;
            // if ($request->f_create_or_update == 2) {
            //     if (! empty($request->f_number)) {
            //         $user_privilege_insert_update_f->number = $request->f_number;
            //     } else {
            //         $user_privilege_insert_update_f->specific_date = date('Y-m-d', strtotime($request->f_specific_date));
            //     }
            // }
            // $user_privilege_insert_update_f->allow = $request->f_allow_date;
            // $user_privilege_insert_update_f->save();
        } else {
            // $user_privilege_insert_update_f = new UserPrivilegeInsertUpdate();
            // $user_privilege_insert_update_f->user_id = $request->user_id;
            // $user_privilege_insert_update_f->status_type = 'insert';
            // $user_privilege_insert_update_f->create_or_update = $request->f_create_or_update ?? 0;
            // if ($request->f_create_or_update == 2) {
            //     if (! empty($request->f_number)) {
            //         $user_privilege_insert_update_f->number = $request->f_number;
            //     } else {
            //         $user_privilege_insert_update_f->specific_date = date('Y-m-d', strtotime($request->f_specific_date));
            //     }
            // }
            // $user_privilege_insert_update_f->allow = $request->f_allow_date;
            // $user_privilege_insert_update_f->save();
        }
         if(! empty($request->f_id_update)){
            $user_privilege_insert_update_m = UserPrivilegeInsertUpdate::findOrFail($request->f_id_update);
            $user_privilege_insert_update_m->user_id = $request->user_id;
            $user_privilege_insert_update_m->status_type = 'update';
            $user_privilege_insert_update_m->create_or_update = $request->m_create_or_update ?? 0;
            if ($request->m_create_or_update == 2) {
                if (!empty($request->m_specific_date)) {
                    $user_privilege_insert_update_m->specific_date = date('Y-m-d', strtotime($request->m_specific_date));
                } 
            }elseif ($request->m_create_or_update == 3) {
                if (!empty( $request->m_number)) {
                    $user_privilege_insert_update_m->number = $request->m_number;
                } 
            }
            $user_privilege_insert_update_m->allow = $request->m_allow_date;
            $user_privilege_insert_update_m->save();
        }else{
            $user_privilege_insert_update_m = new UserPrivilegeInsertUpdate();
            $user_privilege_insert_update_m->user_id = $request->user_id;
            $user_privilege_insert_update_m->status_type = 'update';
            $user_privilege_insert_update_m->create_or_update = $request->m_create_or_update ?? 0;
            if ($request->m_create_or_update == 2) {
                if (!empty($request->m_specific_date)) {
                    $user_privilege_insert_update_m->specific_date = date('Y-m-d', strtotime($request->m_specific_date));
                } 
            }elseif ($request->m_create_or_update == 3) {
                if (!empty( $request->m_number)) {
                    $user_privilege_insert_update_m->number = $request->m_number;
                } 
            }
            $user_privilege_insert_update_m->allow = $request->m_allow_date;
            $user_privilege_insert_update_m->save();
        }
        if (! empty($request->m_privilege_id)) {
            $user_privilege_m = UserPrivilege::findOrFail($request->m_privilege_id);
            $user_privilege_m->table_user_id = $request->user_id;
            $user_privilege_m->status_type = $request->m_status;
            $user_privilege_m->title_details = $request->m_title;
            $user_privilege_m->create_role = $request->m_create_role;
            $user_privilege_m->save();
        } else {
            $user_privilege_m = new UserPrivilege();
            $user_privilege_m->table_user_id = $request->user_id;
            $user_privilege_m->status_type = $request->m_status;
            $user_privilege_m->title_details = $request->m_title;
            $user_privilege_m->create_role = $request->m_create_role;
            $user_privilege_m->save();
        }
        if (! empty($request->v_privilege_id)) {
            $user_privilege_v = UserPrivilege::findOrFail($request->v_privilege_id);
            $user_privilege_v->table_user_id = $request->user_id;
            $user_privilege_v->status_type = $request->v_status;
            $user_privilege_v->title_details = $request->v_title;
            $user_privilege_v->create_role = $request->v_create_role;
            $user_privilege_v->save();
        } else {
            $user_privilege_v = new UserPrivilege();
            $user_privilege_v->table_user_id = $request->user_id;
            $user_privilege_v->status_type = $request->v_status;
            $user_privilege_v->title_details = $request->v_title;
            $user_privilege_v->create_role = $request->v_create_role;
            $user_privilege_v->save();
        }
        if (! empty($request->o_privilege_id)) {
            $user_privilege_o = UserPrivilege::findOrFail($request->o_privilege_id);
            $user_privilege_o->table_user_id = $request->user_id;
            $user_privilege_o->status_type = $request->o_status;
            $user_privilege_o->title_details = $request->o_title;
            $user_privilege_o->create_role = $request->o_create_role;
            $user_privilege_o->save();
        } else {
            $user_privilege_o = new UserPrivilege();
            $user_privilege_o->table_user_id = $request->user_id;
            $user_privilege_o->status_type = $request->o_status;
            $user_privilege_o->title_details = $request->o_title;
            $user_privilege_o->create_role = $request->o_create_role;
            $user_privilege_o->save();
        }
        if (! empty($request->r_privilege_id)) {
            $user_privilege_r = UserPrivilege::findOrFail($request->r_privilege_id);
            $user_privilege_r->table_user_id = $request->user_id;
            $user_privilege_r->status_type = $request->r_status;
            $user_privilege_r->title_details = $request->r_title;
            $user_privilege_r->create_role = $request->r_create_role;
            $user_privilege_r->save();
        } else {
            $user_privilege_r = new UserPrivilege();
            $user_privilege_r->table_user_id = $request->user_id;
            $user_privilege_r->status_type = $request->r_status;
            $user_privilege_r->title_details = $request->r_title;
            $user_privilege_r->create_role = $request->r_create_role;
            $user_privilege_r->save();
        }
        if (! empty($request->c_privilege_id)) {
            $user_privilege_c = UserPrivilege::findOrFail($request->c_privilege_id);
            $user_privilege_c->table_user_id = $request->user_id;
            $user_privilege_c->status_type = $request->c_status;
            $user_privilege_c->title_details = $request->c_title;
            $user_privilege_c->create_role = $request->c_create_role;
            $user_privilege_c->save();
        } else {
            $user_privilege_c = new UserPrivilege();
            $user_privilege_c->table_user_id = $request->user_id;
            $user_privilege_c->status_type = $request->c_status;
            $user_privilege_c->title_details = $request->c_title;
            $user_privilege_c->create_role = $request->c_create_role;
            $user_privilege_c->save();
        }
        if (array_filter($request->privilege_id)) {
            $user_privilege_update = [];
            for ($i = 0; $i < count($request->status); $i++) {
                if (! empty($request->privilege_id[$i])) {
                    $user_privilege = UserPrivilege::find($request->privilege_id[$i]);
                    $user_privilege->table_user_id = $request->user_id;
                    $user_privilege->status_type = $request->status[$i];
                    $user_privilege->title_details = $request->title[$i];
                    $user_privilege->create_role = $request->creat_[$i] == 'Yes' ? 1 : 0;
                    $user_privilege->display_role = $request->dsply_[$i] == 'Yes' ? 1 : 0;
                    $user_privilege->alter_role = $request->alter_[$i] == 'Yes' ? 1 : 0;
                    $user_privilege->delete_role = $request->delet_[$i] == 'Yes' ? 1 : 0;
                    $user_privilege->print_role = $request->print_[$i] == 'Yes' ? 1 : 0;
                    $user_privilege->authorized_dsply = $request->authorized_dsply_[$i] == 'Yes' ? 1 : 0;
                    $user_privilege->save();
                } else {
                    if(($request->creat_[$i] == 'Yes')||($request->dsply_[$i] == 'Yes')||($request->alter_[$i] == 'Yes')||($request->delet_[$i] == 'Yes')||($request->print_[$i] == 'Yes')||($request->authorized_dsply_[$i] == 'Yes')){
                        $user_privilege_update[] = [
                            'table_user_id' => $request->user_id,
                            'status_type' => $request->status[$i],
                            'title_details' => $request->title[$i],
                            'create_role' => $request->creat_[$i] == 'Yes' ? 1 : 0,
                            'display_role' => $request->dsply_[$i] == 'Yes' ? 1 : 0,
                            'alter_role' => $request->alter_[$i] == 'Yes' ? 1 : 0,
                            'delete_role' => $request->delet_[$i] == 'Yes' ? 1 : 0,
                            'print_role' => $request->print_[$i] == 'Yes' ? 1 : 0,
                            'authorized_dsply' =>$request->authorized_dsply_[$i] == 'Yes' ? 1 : 0,
                        ];
                    }
                }
            }
            UserPrivilege::insert($user_privilege_update);
        } else {
            $user_privilege = [];
            for ($i = 0; $i < count($request->status); $i++) {
                if(($request->creat_[$i] == 'Yes')||($request->dsply_[$i] == 'Yes')||($request->alter_[$i] == 'Yes')||($request->delet_[$i] == 'Yes')||($request->print_[$i] == 'Yes')||($request->authorized_dsply_[$i] == 'Yes')){
                    $user_privilege[] = [
                        'table_user_id' => $request->user_id,
                        'status_type' => $request->status[$i],
                        'title_details' => $request->title[$i],
                        'create_role' => $request->creat_[$i] == 'Yes' ? 1 : 0,
                        'display_role' => $request->dsply_[$i] == 'Yes' ? 1 : 0,
                        'alter_role' => $request->alter_[$i] == 'Yes' ? 1 : 0,
                        'delete_role' => $request->delet_[$i] == 'Yes' ? 1 : 0,
                        'print_role' => $request->print_[$i] == 'Yes' ? 1 : 0,
                        'authorized_dsply' =>$request->authorized_dsply_[$i] == 'Yes' ? 1 : 0,
                    ];
                }
            }
            UserPrivilege::insert($user_privilege);
        }

    }
    public function reportAccess(){
      return  DB::select(
        "SELECT report_title
        FROM   reports
        WHERE  reports.report_name!='Reserved' AND (reports.report_status = 1
                OR EXISTS (SELECT report_unit_setup.reports_id
                           FROM   unit_branch_setup
                                  INNER JOIN report_unit_setup
                                          ON report_unit_setup.id_report =
                                             unit_branch_setup.id_report
                           WHERE  report_unit_setup.reports_id = reports.report_id) )");


    }

    public function userLog($status,$user_id){
        $user_log = new UserLog();
        $user_log->authenticatable_type  = 'App\Models\User' ??'';
        $user_log->authenticatable_id = $user_id ??'';
        $user_log->ip_address =$this->userTracking->IpAddress();
        $user_log->user_agent = $this->userTracking->UserBrowser();
        $user_log->login_successful =$status;
        $user_log->location = json_encode($this->userTracking->IpLocation());
        $user_log->mac_address = $this->userTracking->MacAddress();
        $user_log->bowser =$this->userTracking->UserBrowser();

       return $user_log->save();
    }
    public function userRefresh(){
       if(Auth::user()->user_level==1){
        return User::query()->update(['locked' => '1']);
       }else{
          User::where('id',Auth::id())->update(['locked' => '1']);
          Auth::logout();
          return true;
       }


    }
}
