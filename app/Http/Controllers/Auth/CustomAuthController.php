<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Str;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use App\Models\AuditLog;

class CustomAuthController extends Controller
{
    // Show Registration Form
    public function showRegistrationForm()
    {
        abort(403, 'Unauthorized action.');
        return view('auth.register');
    }

    // Handle Registration Logic
    public function register(Request $request)
    {
        abort(403, 'Unauthorized action.');
        // Validate input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // Create new user with auto-generated UUID
            $user = User::create([
                'uuid' => Str::uuid(),
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Log in the user
            Auth::login($user);

            // Success alert
            Alert::success('Success', 'Registration successful!')->persistent('OK');
            return redirect()->getDashboardRoute();
        } catch (\Exception $e) {
            // Log the error
            Log::error('Registration failed: ' . $e->getMessage());

            // Error alert
            Alert::error('Error', 'Registration failed. Please try again.')->persistent('OK');
            return redirect()->back()->withInput();
        }
    }

    // Show Login Form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Handle Login Logic
    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');

            // Validate login input
            $validator = Validator::make($credentials, [
                // 'email' => 'required|email:rfc,dns',
                'email' => 'required|email',
                'password' => 'required|string|min:6|max:100',
            ]);

            if ($validator->fails()) {
                $response = ['errors' => $validator->errors()];

                return $request->expectsJson()
                    ? response()->json($response, 422)
                    : redirect()->back()->withErrors($response['errors'])->withInput();
            }

            $remember = $request->boolean('remember', false);

            if (Auth::attempt($credentials, $remember)) {
                $request->session()->regenerate(); // prevent session fixation

                AuditLog::create([
                        'user_id' => Auth::id(),
                        'action' => 'User logged in',
                        'type' => 'auth',
                        'item_id' => null,
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]);

                // Redirect to the route
                $redirectUrl = route("/");

                return $request->expectsJson()
                    ? response()->json(['message' => 'Login successful', 'redirect' => $redirectUrl], 200)
                    : redirect()->intended($redirectUrl);
            }

            // Invalid credentials

            AuditLog::create([
                'user_id' => null, // unknown user
                'action' => 'Failed login attempt',
                'type' => 'auth',
                'item_id' => null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $errorMessage = ['email' => ['Invalid credentials']];

            return $request->expectsJson()
                ? response()->json(['errors' => $errorMessage], 422)
                : redirect()->back()->withErrors($errorMessage)->withInput();
        } catch (ThrottleRequestsException $e) {
            throw $e; // allow Laravel to handle too many attempts (429)

        } catch (\Throwable $e) {

            AuditLog::create([
                'user_id' => null,
                'action' => 'Login error: ' . $e->getMessage(),
                'type' => 'error',
                'item_id' => null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Log::error('Login failed', [
                'message' => $e->getMessage(),
                'ip' => $request->ip(),
                'email' => $request->input('email'),
            ]);

            $fallbackMessage = ['error' => ['Something went wrong. Please try again.']];

            return $request->expectsJson()
                ? response()->json(['errors' => $fallbackMessage], 500)
                : redirect()->back()->withErrors($fallbackMessage)->withInput();
        }
    }

    // Logout Logic
    public function logout(Request $request)
    {
        if (Auth::check()) {
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'User logged out',
                'type' => 'auth',
                'item_id' => null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        Auth::logout();

        // Prevent browser cache for dashboard page after logout
        return redirect()->route('login')->withHeaders([
            'Cache-Control' => 'no-store, no-cache, must-revalidate, proxy-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
