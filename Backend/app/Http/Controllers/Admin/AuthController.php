<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Request\LoginRequest;
use App\Models\AdminModel\User as AdminUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Exception;
if (!defined('LOGINPATH')) define('LOGINPATH', '/admin/login');

class AuthController extends Controller
{
    /**
     * Show specified view.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function loginView()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');  // Or use your dashboard route name
        }
        return view('pages/login', [
            'layout' => 'login',
        ]);
    }

    /**
     * Authenticate login user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(LoginRequest $request)
    {
        if (!\Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ])) {
            return redirect()->back()->withErrors(['email' => 'Invalid login credentials.']);
        }
        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * Logout user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        \Auth::logout();
        session()->forget('token');
        return redirect(LOGINPATH);
    }

    public function editProfile()
    {
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            return view('pages.edit-profile', compact('user'));
        } else {
            return redirect(LOGINPATH);
        }
    }

    public function changePassword(Request $request)
    {
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            if ($user && !password_verify($request->old, $user->password)) {
                return response()->json([
                    'error' => ["Password doesn't match with old password"],
                ]);
            } else {
                $user->password = Hash::make($request->new);
                $user->update();
                return response()->json([
                    'success' => ['Update Password'],
                ]);
            }
        } else {
            return redirect(LOGINPATH);
        }
    }

    public function editProfileApi(Request $req)
    {
        try {
            // Validation
            $validator = Validator::make($req->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
                'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ], 422);
            }

            // Auth check
            if (!Auth::guard('web')->check()) {
                return response()->json([
                    'error' => ['Unauthorized'],
                ], 401);
            }

            $user = Auth::guard('web')->user();
            $user = AdminUser::find($user->id);

            // Handle profile image
            $image = null;
            if ($req->hasFile('profile')) {
                $file = $req->file('profile');
                $extension = $file->getClientOriginalExtension();
                $time = Carbon::now()->timestamp;

                // Directory inside public
                $directory = public_path('storage/images');

                // Create directory if not exists
                if (!File::exists($directory)) {
                    File::makeDirectory($directory, 0755, true);
                }

                $fileName = 'profile_' . $user->id . '_' . $time . '.' . $extension;
                $fullPath = $directory . '/' . $fileName;

                // Delete old profile image
                if ($user->profile && File::exists(public_path($user->profile))) {
                    File::delete(public_path($user->profile));
                }

                // Save new image
                $file->move($directory, $fileName);

                // Store relative public path in DB
                $image = 'public/storage/images/' . $fileName;
            } elseif ($user->profile) {
                $image = $user->profile;
            } else {
                $image = null;
            }
            $user->name = $req->name;
            $user->email = $req->email;
            $user->profile = $image;
            $user->save();

            return response()->json([
                'success' => 'Profile updated successfully!',
                'user' => $user,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
