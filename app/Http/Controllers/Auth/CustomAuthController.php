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
use Laravel\Passport\Token;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Http;
use App\Services\ApiResponseService;

class CustomAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('throttle:5,1')->only(['login', 'apiLogin']);
    }

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

    // Handle Web Login Logic
    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');

            // Validate login input
            $validator = Validator::make($credentials, [
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
                    'action' => 'User logged in (Web)',
                    'type' => 'auth',
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
                'user_id' => null,
                'action' => 'Failed web login attempt',
                'type' => 'auth',
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
                'action' => 'Web login error: ' . substr($e->getMessage(), 0, 200),
                'type' => 'error',
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

    // Web Logout
    public function logout(Request $request)
    {
        try {
            $userId = Auth::id();

            // Logout user
            Auth::logout();

            // Invalidate session
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Audit log
            AuditLog::create([
                'user_id'    => $userId,
                'action'     => 'User logged out (Web)',
                'type'       => 'auth',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Redirect to login
            return redirect()
                ->route('login')
                ->with('success', 'Logged out successfully');

        } catch (\Throwable $e) {

            Log::error('Web Logout Error', [
                'message' => $e->getMessage(),
                'ip'      => $request->ip(),
            ]);

            return redirect()
                ->back()
                ->withErrors(['error' => 'Logout failed. Please try again.']);
        }
    }

    // API Logout
    public function apiLogout(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return ApiResponseService::error('Not authenticated', 40100, 401);
            }

            $currentToken = $user->token(); // Passport access token model

            if ($currentToken) {
                $currentToken->revoke();

                AuditLog::create([
                    'user_id'    => $user->id,
                    'action'     => 'API logout',
                    'type'       => 'auth',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'data'       => ['token_id' => $currentToken->id],
                ]);
            }

            return ApiResponseService::success(null, 'Successfully logged out');
        } catch (\Throwable $e) {
            Log::error('API Logout Error', ['message' => $e->getMessage()]);
            return ApiResponseService::serverError('Logout failed');
        }
    }



    public function apiLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'       => 'required|email',
            'password'    => 'required|string|min:6',
            'device_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return ApiResponseService::validation($validator->errors(), 'Validation failed');
        }

        try {
            $response = Http::asForm()->post(url('/oauth/token'), [
                'grant_type'    => 'password',
                'client_id'     => config('services.passport.client_id'),
                'client_secret' => config('services.passport.client_secret'),
                'username'      => $request->email,
                'password'      => $request->password,
                'scope'         => '*',
            ]);

            if ($response->failed()) {
                AuditLog::create([
                    'user_id'    => null,
                    'action'     => 'Failed API login attempt',
                    'type'       => 'auth',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'data'       => [
                        'email' => $request->email,
                        'passport_error' => $response->json(),
                    ],
                ]);

                // Passport usually returns 400/401 with "invalid_grant"
                return ApiResponseService::error('Invalid credentials', 40101, 401, $response->json());
            }

            // We want user info also
            $user = User::where('email', $request->email)->first();

            AuditLog::create([
                'user_id'    => $user?->id,
                'action'     => 'API login successful',
                'type'       => 'auth',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'data'       => ['device' => $request->device_name],
            ]);

            return ApiResponseService::success([
                'token' => $response->json(), // access_token, refresh_token, expires_in, token_type
                'user'  => $user ? [
                    'uuid'  => $user->uuid,
                    'name'  => $user->name,
                    'email' => $user->email,
                ] : null,
            ], 'Login successful');
        } catch (\Throwable $e) {
            Log::error('API Login Error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return ApiResponseService::serverError('Authentication server error');
        }
    }


    public function apiRefreshToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'refresh_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ApiResponseService::validation($validator->errors(), 'Validation failed');
        }

        try {
            $response = Http::asForm()->post(url('/oauth/token'), [
                'grant_type'    => 'refresh_token',
                'refresh_token' => $request->refresh_token,
                'client_id'     => config('services.passport.client_id'),
                'client_secret' => config('services.passport.client_secret'),
                'scope'         => '*',
            ]);

            AuditLog::create([
                'user_id'    => optional($request->user())->id, // may be null if access token expired
                'action'     => 'API token refresh attempt ' . ($response->failed() ? 'failed' : 'successful'),
                'type'       => 'auth',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            if ($response->failed()) {
                return ApiResponseService::error('Invalid refresh token', 40102, 401, $response->json());
            }

            return ApiResponseService::success([
                'token' => $response->json(),
            ], 'Token refreshed');
        } catch (\Throwable $e) {
            Log::error('Token Refresh Error', ['message' => $e->getMessage()]);
            return ApiResponseService::serverError('Token refresh failed');
        }
    }


    public function apiUser(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return ApiResponseService::error('Not authenticated', 40100, 401);
            }

            return ApiResponseService::success([
                'uuid'       => $user->uuid,
                'name'       => $user->name,
                'email'      => $user->email,
                'created_at' => optional($user->created_at)->toISOString(),
            ], 'User fetched');
        } catch (\Throwable $e) {
            Log::error('Get User Error', ['message' => $e->getMessage()]);
            return ApiResponseService::serverError('Failed to retrieve user');
        }
    }

    public function apiCheckAuth(Request $request)
    {
        return ApiResponseService::success([
            'authenticated' => $request->user() ? true : false,
        ], 'Auth check');
    }


}








// 3) Request formats (mobile)
// Login

// POST /api/v1/auth/login

// {
//   "email": "admin@example.com",
//   "password": "123456",
//   "device_name": "android"
// }

// Refresh

// POST /api/v1/auth/refresh

// {
//   "refresh_token": "REFRESH_TOKEN_HERE"
// }

// Logout

// POST /api/v1/auth/logout
// Headers:

// Authorization: Bearer <access_token>

// User

// GET /api/v1/auth/user
// Headers:

// Authorization: Bearer <access_token>
