<?php

namespace App\Http\Controllers;

use App\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Customer Controller
 */
class CustomerController extends Controller
{

    public function index(Request $request)
    {
        $users = Customer::orderBy('created_at', 'desc')
            ->when($request->q, function ($users) use ($request) {
                $users = $users->where('name', 'like', '%' . $request->q . '%');
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
            'point' => 'required',
            'deposit' => 'required',
            'address' => 'required|string',
            'phone_number' => 'required|string',
        ]);

        $customer = Customer::create([
            'name' => $request->name,
            'point' => $request->point,
            'deposit' => $request->deposit,
            'address' => $request->address,
            'phone_number' => $request->phone_number
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $customer
        ]);
    }

    public function view($id)
    {
        $customer = Customer::find($id);

        return response()->json([
            'status' => 'success',
            'data' => $customer
        ]);
    }

    public function update(Request $request, $id)
    {

        $this->validate($request, [
            'name' => 'required|string|max:50',
            'point' => 'required',
            'deposit' => 'required',
            'address' => 'required|string',
            'phone_number' => 'required|string',
        ]);

        $customer = Customer::find($id);

        $customer->update([
            'name' => $request->name,
            'point' => $request->point,
            'deposit' => $request->deposit,
            'address' => $request->address,
            'phone_number' => $request->phone_number
        ]);

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function destroy($id)
    {
        $customer = Customer::find($id);

        $customer->delete();

        return response()->json([
            'status' => 'success'
        ]);
    }
}
