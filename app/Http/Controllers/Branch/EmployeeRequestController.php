<?php

namespace App\Http\Controllers\Branch;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Models\Branch\EmployeeRequestHelper;
use App\Corporation;
use Yajra\Datatables\Datatables;
use App\User;
use Illuminate\Support\Facades\DB;

class EmployeeRequestController extends Controller
{
	public function index(EmployeeRequestHelper $employeeRequest, $id){
		return view("branchs.employeeRequest.index");
	}

	public function getEmployeeRequests(EmployeeRequestHelper $employeeRequest, Request $request){
		$employeeRequest->setCorpId(12);
		$databaseName = $employeeRequest->getDatabaseName();
		$query1 = DB::select('SELECT users.uname as "username", sysdata.ShortName as "from_branch", sysdata2.ShortName as "to_branch", employeeRequest.txn_no as id, employeeRequest.type, employeeRequest.date_start, employeeRequest.date_end, employeeRequest.approved, employeeRequest.executed,employeeRequest.sex from global.t_users as users JOIN '.$databaseName.'.t_cashr_rqst employeeRequest ON users.UserID = employeeRequest.userid JOIN global.t_sysdata as sysdata ON employeeRequest.from_branch = sysdata.Branch JOIN global.t_sysdata as sysdata2 ON employeeRequest.to_branch = sysdata2.Branch');
            return Datatables::of($query1)
                ->filter(function ($query) use ($request) {

                })
                ->editColumn("approved", function($employeeRequest){
                	$checked = "";
                	if($employeeRequest->approved) { $checked = "checked"; }
                	return '<input type=checkbox '. $checked .' disabled name=' .$employeeRequest->id. '>';
                })
                 ->editColumn("executed", function($employeeRequest){
                	$checked = "";
                	if($employeeRequest->executed) { $checked = "checked"; }
                	return '<input type=checkbox '. $checked .' disabled name=' .$employeeRequest->id. '>';
                })
                ->addColumn('action', function ($con) {
                    return '<img style="width:30px;" src="'.url("public/images/approve.png").'"><img style="width:30px;" src="'.url("public/images/delete.png").'">';
                })
                ->rawColumns(['approved', "action", "executed"])
                ->make('true');
	}

	public function getEmployeeRequests2(EmployeeRequestHelper $employeeRequest, Request $request){
		$employeeRequest->setCorpId(12);
		$databaseName = $employeeRequest->getDatabaseName();
		$query1 = DB::select('SELECT users.uname as "username", users.LastUnfrmPaid, sysdata.ShortName as "from_branch", sysdata2.ShortName as "to_branch", employeeRequest.txn_no as id, employeeRequest.type, employeeRequest.date_start, employeeRequest.date_end, employeeRequest.approved, employeeRequest.executed,employeeRequest.sex from global.t_users as users JOIN '.$databaseName.'.t_cashr_rqst employeeRequest ON users.UserID = employeeRequest.userid JOIN global.t_sysdata as sysdata ON employeeRequest.from_branch = sysdata.Branch JOIN global.t_sysdata as sysdata2 ON employeeRequest.to_branch = sysdata2.Branch');
		if(!is_null($request->branch_name) && $request->branch_name != "any"){
			$query1 = array_filter($query1, function ($arr) use ($request){
				return $arr->from_branch == $request->branch_name;
			});
		}
            return Datatables::of($query1)
                ->addColumn('action', function ($con) {
                    return '<img style="width:30px;" src="'.url("public/images/approve.png").'">';
                })
                ->rawColumns(["action"])
                ->make('true');
	}
}
