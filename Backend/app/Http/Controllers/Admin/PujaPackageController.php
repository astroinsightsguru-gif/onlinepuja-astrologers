<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pujafaq;
use App\Models\Pujapackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Exception;

class PujaPackageController extends Controller
{
    public $limit = 15;
    public $paginationStart;
    public $path;

    //
    public function addpujapackage()
    {
        return view('pages.add-package');
    }

    public function editpackage($id)
    {
        $package = PujaPackage::find($id);  // Fetch the package by ID
        if (!$package) {
            return redirect()->route('package-list')->with('error', 'Package not found.');
        }
        return view('pages.add-package', compact('package'));
    }

    // ------------------------------------------------------------

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'person' => 'required|integer',
                'package_price' => 'required|numeric',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ]);
            }

            // Concatenate all the package points
            $packagePoints = $request->package_points;

            // Create a new Pujapackage entry
            if (Auth::guard('web')->check()) {
                Pujapackage::create([
                    'title' => $request->title,
                    'person' => $request->person,
                    'package_price' => $request->package_price,
                    'package_price_usd' => $request->package_price_usd,
                    'description' => $packagePoints,  // Store the points in the description
                ]);
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }

        // Redirect or return success response
        return redirect()->route('package-list')->with('success', 'Package added successfully');
    }

    // ---------------------------------------------------------------------------------------------------------
    public function getpujapackage(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ?? 1;
                $paginationStart = ($page - 1) * $this->limit;

                $query = Pujapackage::query();

                $userCount = $query->count();
                $totalPages = ceil($userCount / $this->limit);
                $totalRecords = $userCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = min(($this->limit * $page), $totalRecords);

                $packeges = $query
                    ->skip($paginationStart)
                    ->take($this->limit)
                    ->get();
                return view('pages.package-list', compact('packeges', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ---------------------------------------------------------------------------------------------------------------------------

    public function deletePujaPackage(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $Pujapackage = Pujapackage::find($request->del_id);
                if ($Pujapackage) {
                    // $Pujapackage->isDelete = true;
                    $Pujapackage->delete();
                } else {
                    return redirect(LOGINPATH);
                }
                return redirect()->route('package-list');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ------------------------------------------------------------------------------------------------------------------------

    public function update(Request $request, $id)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'person' => 'required|integer',
                'package_price' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ]);
            }

            // Find the existing package
            $package = Pujapackage::find($id);

            if (!$package) {
                return redirect()->route('package-list')->with('error', 'Package not found.');
            }

            // Concatenate and update the package points
            $packagePoints = $request->package_points;

            // Update the package
            $package->update([
                'title' => $request->title,
                'person' => $request->person,
                'package_price' => $request->package_price,
                'package_price_usd' => $request->package_price_usd,
                'description' => $packagePoints,
            ]);
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }

        // Redirect or return success response
        return redirect()->route('package-list')->with('success', 'Package updated successfully');
    }

    // -----------------------------------------------------------------------------------------------------------------------
    public function PackageStatus(Request $request)
    {
        try {
            $astrologerCategory = Pujapackage::find($request->status_id);
            if (Auth::guard('web')->check()) {
                $astrologerCategory->package_status = !$astrologerCategory->package_status;
                $astrologerCategory->update();
                return redirect()->route('package-list');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ----------------------------------------------------------------------------------------------------------------------
    public function pujaFaqList(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $Pujafaq = Pujafaq::query();
                $categoryCount = $Pujafaq->count();
                $Pujafaq->orderBy('id', 'DESC');
                $Pujafaq->skip($paginationStart);
                $Pujafaq->take($this->limit);
                $Pujafaq = $Pujafaq->get();
                $totalPages = ceil($categoryCount / $this->limit);
                $totalRecords = $categoryCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords
                    ? ($this->limit * ($page - 1)) + $this->limit
                    : $totalRecords;
                return view(
                    'pages.puja-faq-list',
                    compact('Pujafaq', 'totalPages', 'totalRecords', 'start', 'end', 'page')
                );
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------------------------------------------------------
    public function addPujaFaq(Request $req)
    {
        try {
            $validator = Validator::make($req->all(), [
                'title' => 'required',
                'description' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ]);
            }
            if (Auth::guard('web')->check()) {
                $pujaFAQ = Pujafaq::create([
                    'title' => $req->title,
                    'description' => $req->description,
                ]);
                $pujaFAQ->update();
                return redirect()->route('puja-faq-list')->with('message', 'Data added Successfully');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ----------------------------------------------------------------------------------------------------------------------------------
    public function editPujaFaq(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $pujafaq = Pujafaq::find($request->filed_id);
                if ($pujafaq) {
                    $pujafaq->title = $request->title;
                    $pujafaq->description = $request->description;
                    $pujafaq->update();
                    return redirect()->route('puja-faq-list');
                }
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // -----------------------------------------------------------------------------------------------------------------------------------

    public function deletePujaFaq(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $Pujafaq = Pujafaq::find($request->faq_id);

                if ($Pujafaq) {
                    $Pujafaq->delete();
                }
                return redirect()->route('puja-faq-list');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ---------------------------------------------------------------------------------------------------------------------------
}
