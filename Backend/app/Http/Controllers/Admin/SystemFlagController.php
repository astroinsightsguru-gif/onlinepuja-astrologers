<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminModel\Language;
use App\Models\MstControl;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SystemFlagController extends Controller
{
    public function getSystemFlag(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                $flagGroup = DB::table('flaggroup')->whereNull('parentFlagGroupId')->get();

                for ($i = 0; $i < count($flagGroup); $i++) {
                    $subGroup = DB::table('flaggroup')
                        ->where('viewenable', 1)
                        ->where('parentFlagGroupId', $flagGroup[$i]->id)
                        ->get();

                    if ($subGroup && count($subGroup) > 0) {
                        for ($j = 0; $j < count($subGroup); $j++) {
                            $systemFlag = DB::table('systemflag')
                                ->where('isActive', 1)
                                ->where('flagGroupId', $subGroup[$j]->id)
                                ->get();
                            $subGroup[$j]->systemFlag = $systemFlag;
                        }

                        $flagGroup[$i]->subGroup = $subGroup;

                        $systemFlag = DB::table('systemflag')
                            ->where('flagGroupId', $flagGroup[$i]->id)
                            ->get();
                        $flagGroup[$i]->systemFlag = $systemFlag;
                    } else {
                        $systemFlag = DB::table('systemflag')
                            ->where('flagGroupId', $flagGroup[$i]->id)
                            ->get();
                        $flagGroup[$i]->systemFlag = $systemFlag;
                        $flagGroup[$i]->subGroup = [];
                    }
                }
                $language = Language::query()->get();
                $mstData = MstControl::query()->get();
                $astroApiCallType = isset($mstData[0]) ? $mstData[0]->astro_api_call_type : null;

                return view('pages.system-flag', compact('flagGroup', 'language', 'astroApiCallType'));
            } else {
                return redirect('/admin/login');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function editSystemFlag(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                // Handle flaggroups (enable/disable subgroups)
                $flaggroups = $req->input('flaggroups');
                if ($flaggroups) {
                    foreach ($flaggroups as $subGroupId => $data) {
                        $isActive = $data['isActive'] ?? 0;
                        DB::table('flaggroup')
                            ->where('id', '=', $data['id'])
                            ->update(['isActive' => $isActive]);
                    }
                }

                // Process main groups and their system flags
                foreach ($req->group as $flag) {
                    // Process main system flags
                    if (array_key_exists('systemFlag', $flag) && count($flag['systemFlag']) > 0) {
                        foreach ($flag['systemFlag'] as $flagvalue) {
                            if (array_key_exists('value', $flagvalue)) {
                                // Handle storage provider selection
                                if ($flagvalue['name'] === 'storege_provider') {
                                    DB::table('flaggroup')
                                        ->whereIn('flagGroupName', ['google_bucket', 'aws_bucket', 'digital_ocean'])
                                        ->update(['isActive' => 0]);

                                    DB::table('flaggroup')
                                        ->where('flagGroupName', $flagvalue['value'])
                                        ->update(['isActive' => 1]);
                                }

                                // Process based on valueType
                                if (array_key_exists('valueType', $flagvalue)) {
                                    if ($flagvalue['valueType'] == 'File') {
                                        if (!empty($flagvalue['value']) && is_uploaded_file($flagvalue['value'])) {
                                            $flagvalue['value'] = $this->handleFileUpload($flagvalue, 'png');
                                        }
                                    }

                                    if ($flagvalue['valueType'] == 'MultiSelect') {
                                        $flagvalue['value'] = implode(',', $flagvalue['value']);
                                    }

                                    if ($flagvalue['valueType'] == 'MultiSelectWebLang') {
                                        $flagvalue['value'] = json_encode($flagvalue['value']);
                                    }

                                    if ($flagvalue['valueType'] == 'Video') {
                                        if (!empty($flagvalue['value']) && is_uploaded_file($flagvalue['value'])) {
                                            $flagvalue['value'] = $this->handleFileUpload($flagvalue, 'mp4');
                                        } else {
                                            $sysFile = DB::table('systemflag')->where('name', $flagvalue['name'])->first();
                                            $flagvalue['value'] = $sysFile ? $sysFile->value : '';
                                        }
                                    }
                                }

                                // Update database
                                DB::table('systemflag')
                                    ->where('name', '=', $flagvalue['name'])
                                    ->update(['value' => $flagvalue['value']]);
                            }
                        }
                    }

                    // Process subgroups and their system flags
                    if (array_key_exists('subGroup', $flag) && count($flag['subGroup']) > 0) {
                        foreach ($flag['subGroup'] as $flagvalue) {
                            foreach ($flagvalue['systemFlag'] as $sys) {
                                if (array_key_exists('value', $sys)) {
                                    if (array_key_exists('valueType', $sys)) {
                                        if ($sys['valueType'] == 'File') {
                                            if (!empty($sys['value']) && is_uploaded_file($sys['value'])) {
                                                $sys['value'] = $this->handleFileUpload($sys, 'png');
                                            }
                                        }

                                        if ($sys['valueType'] == 'MultiSelect') {
                                            $sys['value'] = implode(',', $sys['value']);
                                        }

                                        if ($sys['valueType'] == 'MultiSelectWebLang') {
                                            $sys['value'] = json_encode($sys['value']);
                                        }

                                        if ($sys['valueType'] == 'Video') {
                                            if (!empty($sys['value']) && is_uploaded_file($sys['value'])) {
                                                $sys['value'] = $this->handleFileUpload($sys, 'mp4');
                                            } else {
                                                $sysFile = DB::table('systemflag')->where('name', $sys['name'])->first();
                                                $sys['value'] = $sysFile ? $sysFile->value : '';
                                            }
                                        }
                                    }

                                    // Update database
                                    DB::table('systemflag')
                                        ->where('name', '=', $sys['name'])
                                        ->update(['value' => $sys['value']]);
                                }
                            }
                        }
                    }
                }

                return response()->json([
                    'success' => 'SystemFlag Update',
                ]);
            } else {
                return redirect('/admin/login');
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle file/video upload and return the relative stored path.
     */
    private function handleFileUpload(array $flagvalue, string $extension): string
    {
        $destinationPath = public_path('storage/images/');

        // Ensure directory exists with proper permissions
        if (!is_dir($destinationPath)) {
            if (!mkdir($destinationPath, 0755, true) && !is_dir($destinationPath)) {
                throw new \RuntimeException("Failed to create directory: {$destinationPath}");
            }
        }

        // Ensure directory is writable
        if (!is_writable($destinationPath)) {
            chmod($destinationPath, 0755);
        }

        $sysFile = DB::table('systemflag')->where('name', $flagvalue['name'])->first();
        $time = Carbon::now()->timestamp;
        $fileName = $flagvalue['name'] . $time . '.' . $extension;
        $fullPath = $destinationPath . $fileName;

        // Delete old file if it exists
        if ($sysFile && !empty($sysFile->value)) {
            $oldFilePath = public_path($sysFile->value);
            if (File::exists($oldFilePath)) {
                File::delete($oldFilePath);
            }
        }

        // Read and write file content
        $fileContent = file_get_contents($flagvalue['value']);
        if ($fileContent === false) {
            throw new \RuntimeException("Failed to read uploaded file: {$flagvalue['value']}");
        }

        if (file_put_contents($fullPath, $fileContent) === false) {
            throw new \RuntimeException("Failed to write file to: {$fullPath}");
        }

        return 'public/storage/images/' . $fileName;
    }
}
