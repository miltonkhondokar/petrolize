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
    public function apiLogin(Request $request)
    {
        try {
            // 1️⃣ Validate request
            $validator = Validator::make($request->all(), [
                'email'       => 'required|email',
                'password'    => 'required|string|min:6',
                'device_name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            // 2️⃣ Attempt authentication (web provider)
            if (!Auth::attempt([
                'email'    => $request->email,
                'password' => $request->password,
            ])) {

                // Optional: audit failed login
                AuditLog::create([
                    'user_id'    => null,
                    'action'     => 'Failed API login attempt',
                    'type'       => 'auth',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'data'       => ['email' => $request->email],
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                ], 401);
            }

            // 3️⃣ Authenticated user
            $user = Auth::user();

            // 4️⃣ Revoke existing token for same device (optional but secure)
            $user->tokens()
                ->where('name', $request->device_name)
                ->delete();

            // 5️⃣ Create Passport personal access token
            $tokenResult = $user->createToken($request->device_name);
            $token       = $tokenResult->accessToken;
            $tokenModel  = $tokenResult->token;

            // 6️⃣ Set token expiry
            $tokenModel->expires_at = now()->addDays(15);
            $tokenModel->save();

            // 7️⃣ Audit successful login
            AuditLog::create([
                'user_id'    => $user->id,
                'action'     => 'API login successful',
                'type'       => 'auth',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'data'       => ['device' => $request->device_name],
            ]);

            // 8️⃣ Success response
            return response()->json([
                'success'      => true,
                'token_type'   => 'Bearer',
                'access_token' => $token,
                'expires_at'  => $tokenModel->expires_at->toISOString(),
                'expires_in'  => $tokenModel->expires_at->diffInSeconds(now()),
                'user' => [
                    'uuid'  => $user->uuid,
                    'name'  => $user->name,
                    'email' => $user->email,
                ],
            ], 200);
        } catch (\Throwable $e) {

            Log::error('API Login Error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Authentication server error',
            ], 500);
        }
    }

    // API Logout
    public function apiLogout(Request $request)
    {
        try {
            if (!$request->user()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authenticated',
                ], 401);
            }

            $user = $request->user();
            $currentToken = $user->token();

            if ($currentToken) {
                // Revoke current access token
                $currentToken->revoke();

                AuditLog::create([
                    'user_id' => $user->id,
                    'action' => 'API logout',
                    'type' => 'auth',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'data' => ['token_id' => $currentToken->id]
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out',
            ]);
        } catch (\Throwable $e) {
            Log::error('API Logout Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
            ], 500);
        }
    }

    // API Token Refresh (optional but recommended)
    public function apiRefreshToken(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'refresh_token' => 'required|string',
                'device_name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Note: For Passport, refresh tokens are handled via OAuth endpoints
            // This method assumes you're storing refresh tokens separately

            return response()->json([
                'success' => false,
                'message' => 'Use /oauth/token endpoint for refresh',
            ], 400);
        } catch (\Throwable $e) {
            Log::error('Token Refresh Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Token refresh failed',
            ], 500);
        }
    }

    // Get Current User (API)
    public function apiUser(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authenticated',
                ], 401);
            }

            return response()->json([
                'success' => true,
                'user' => [
                    'uuid' => $user->uuid,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at->toISOString(),
                ]
            ]);
        } catch (\Throwable $e) {
            Log::error('Get User Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user',
            ], 500);
        }
    }

    // Check Authentication (API)
    public function apiCheckAuth(Request $request)
    {
        return response()->json([
            'authenticated' => $request->user() ? true : false,
        ]);
    }
}
