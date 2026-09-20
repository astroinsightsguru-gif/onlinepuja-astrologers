<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppDesign;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class AppDesignController extends Controller
{

    public $path;
    public $limit = 6;
    public $paginationStart;

    public function getAppdesign(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $appdesign = AppDesign::query();
                $appdesignCount = $appdesign->count();
                $appdesign->skip($paginationStart);
                $appdesign->take($this->limit);
                $appdesign = $appdesign->get();
                $totalPages = ceil($appdesignCount / $this->limit);
                $totalRecords = $appdesignCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ?
                ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view(
                    'pages.app-design',
                    compact('appdesign', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }


    public function appDesignStatus(Request $request)
    {
        try {
            if (!Auth::guard('web')->check()) {
                return redirect('/admin/login');
            }

            // Start a database transaction
            DB::beginTransaction();

            // Get the selected app design
            $appstatus = AppDesign::find($request->status_id);

            if (!$appstatus) {
                throw new Exception("App design not found");
            }

            // If we're activating this one, deactivate all others first
            if (!$appstatus->is_active) {
                AppDesign::where('id', '!=', $request->status_id)
                         ->update(['is_active' => false]);
            }

            // Toggle the status of the selected record
            $appstatus->is_active = !$appstatus->is_active;
            $appstatus->save();

            // Always store/update the design_id in systemflag
            DB::table('systemflag')
                ->updateOrInsert(
                    ['name' => 'appDesignId'],
                    ['value' => $appstatus->design_id]
                );

            // Commit the transaction
            DB::commit();

            return redirect()->back();

        } catch (Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }

}
