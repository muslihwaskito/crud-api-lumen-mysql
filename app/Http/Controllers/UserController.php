<?php 

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * User Controller
 */
class UserController extends Controller
{
	
	public function index(Request $request)
	{
		$users = User::orderBy('created_at', 'desc')
				->when($request->q, function($users) use ($request) {
					$users = $users->where('name', 'like', '%'.$request->q.'%');
				})
				->paginate(10);

		return response()->json([
			'status' => 'success',
			'data' => $users
		]);
	}

	public function store(Request $request)
	{
		$this->validate($request, [
			'name' => 'required|string|max:50',
			'identity_id' => 'required|string|unique:users',
			'gender' => 'required',
			'address' => 'required|string',
			'photo' => 'nullable|image|mimes:jpg,jpeg,png',
			'email' => 'required|email|unique:users',
			'password' => 'required|string|min:6',
			'phone_number' => 'required|string',
			'role' => 'required',
			'status' => 'required',
		]);

		$filename = null;

		if ($request->hasFile('photo')) {
			$file = $request->file('photo');
			$filename = Str::random(7).$request->email.'.'.$file->extension();
			$file->move(base_path('public/images'),$filename);
		}

		$user = User::create([
			'name' => $request->name,
			'identity_id' => $request->identity_id,
			'gender' => $request->gender,
			'address' => $request->address,
			'photo' => $filename,
			'email' => $request->email,
			'password' => app('hash')->make($request->password),
			'phone_number' => $request->phone_number,
			'role' => $request->role,
			'status' => $request->status,
		]);

		return response()->json([
			'status' => 'success', 
			'data' => $user
		]);
	}

	public function view($id)
	{
		$user = User::find($id);
		$user->url = $user->photo?url('images/'. $user->photo):null;

		return response()->json([
			'status' => 'success',
			'data' => $user
		]);
	}

	public function update(Request $request, $id)
	{

		$this->validate($request, [
			'name' => 'required|string|max:50',
			'identity_id' => 'required|string|unique:users,identity_id,'.$id,
			'gender' => 'required',
			'address' => 'required|string',
			'photo' => 'nullable|image|mimes:jpg,jpeg,png',
			'email' => 'required|email|unique:users,email,'.$id,
			'password' => 'nullable|string|min:6',
			'phone_number' => 'required|string',
			'role' => 'required',
			'status' => 'required',
		]);

		$user = User::find($id);

		$password = $request->password != '' ? app('hash')->make($request->password) : $user->password;

		$filename = $user->photo;

		if ($request->hasFile('photo')) {
			$file = $request->file('photo');
			$filename = Str::random(7).$user->email.'.'.$file->extension();
			$file->move(base_path('public/images'),$filename);

			if ($user->photo) {
				unlink(base_path('public/images/'.$user->photo));
			}
		}

		$user->update([
			'name' => $request->name,
			'identity_id' => $request->identity_id,
			'gender' => $request->gender,
			'address' => $request->address,
			'photo' => $filename,
			'password' => $password,
			'phone_number' => $request->phone_number,
			'role' => $request->role,
			'status' => $request->status
		]);

		return response()->json([
			'status' => 'success'
		]);
	}

	public function destroy($id)
	{
		$user = User::find($id);

		if ($user->photo) {
			unlink(base_path('public/images/'.$user->photo));
		}
			
		$user->delete();

		return response()->json([
			'status' => 'success'
		]);
	}

	public function register(Request $request)
	{
		$this->validate($request, [
			'name' => 'required|string|max:50',
			'identity_id' => 'required|string|unique:users',
			'gender' => 'required',
			'address' => 'required|string',
			'photo' => 'nullable|image|mimes:jpg,jpeg,png',
			'email' => 'required|email|unique:users',
			'password' => 'required|string|min:6|confirmed',
			'password_confirmation' => 'required|string|min:6',
			'phone_number' => 'required|string'
		]);

		$user = User::create([
			'name' => $request->name,
			'identity_id' => $request->identity_id,
			'gender' => $request->gender,
			'address' => $request->address,
			'email' => $request->email,
			'password' => app('hash')->make($request->password),
			'phone_number' => $request->phone_number,
			'role' => 2,
			'status' => 0,
		]);

		return response()->json([
			'status' => 'success',
			'data' => $user
		]);
	}

	public function login(Request $request)
	{
		$this->validate($request, [
			'email' => 'required|email|exists:users,email',
			'password' => 'required|string|min:6'
		]);

		$user = User::where(['email' => $request->email, 'status' => 1])->first();

		if ($user && Hash::check($request->password, $user->password)) {
			$token = Str::random(40);
			$user->update(['api_token' => $token]);

			return response()->json([
				'status' => 'success',
				'access_token' => $token
			]);
		}

		return response()->json([
			'status' => 'error',
			'message' => 'Your account is not active'
		], 422);
	}

	public function sendResetToken(Request $request)
	{
		$this->validate($request, [
			'email' => 'required|email|exists:users'
		]);

		$user = User::where('email', $request->email)->first();

		$user->update(['reset_token' => Str::random(40)]);

		Mail::to($user->email)->send(new ResetPasswordMail($user));

		return response()->json([
			'status' => 'success',
			'data' => $user->reset_token
		]);
	}

	public function verivyResetPassword(Request $request, $token)
	{
		$this->validate($request,[
			'password' => 'required|string|min:6|confirmed',
			'password_confirmation' => 'required|string|min:6',
		]);

		$user = User::where('reset_token',$token)->first();

		if ($user) {
			$user->update([
				'password' => app('hash')->make($request->password),
				'reset_token' => null
			]);

			return response()->json(['status' => 'success']);
		}

		return response()->json([
			'status' => 'error',
			'message' => 'Wrong token'
		],422);
	}

	public function profile(Request $request)
	{
		$request->user()->url = $request->user()->photo ? url('images/' . $request->user()->photo) : null;

		return response()->json([
			'status' => 'success',
			'data' => $request->user()
		]);
	}

	public function logout(Request $request)
	{
		$user = $request->user();
		$user->update(['api_token' => null]);

		return response()->json(['status' => 'success']);
	}

	public function list(Request $request)
	{
		$users = User::orderBy('created_at', 'desc')->get();

		return response()->json([
			'status' => 'success',
			'data' => $users
		]);
	}
}