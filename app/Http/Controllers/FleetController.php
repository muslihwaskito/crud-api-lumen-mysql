<?php 

namespace App\Http\Controllers;

use App\Fleet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

/**
 * FleetsController
 */
class FleetController extends Controller
{
	public function index(Request $request)
	{
		$fleets = Fleet::when($request->q,function($fleets) use ($request){
			$fleets->where('plat_number', 'LIKE', '%'.$request->q.'%');
		})->orderBy('created_at', 'desc')->with(['user'])->paginate(10);

		return response()->json([
			'status' => 'success',
			'data' => $fleets
		]);
	}

	public function store(Request $request)
	{
		$this->validate($request, [
			'plat_number' => 'required|string|unique:fleets,plat_number',
			'type' => 'required',
			'user_id' => 'required',
			'photo' => 'required|image|mimes:jpg,jpeg,png'
		]);

		$file = $request->file('photo');

		$filename = $request->plat_number.'-'.time().'.'.$file->getClientOriginalExtension();
		$file->move('fleets', $filename);

		Fleet::create([
			'plat_number' => $request->plat_number,
			'photo' => $filename,
			'type' => $request->type,
			'user_id' => $request->user_id
		]);

		return response()->json([
			'status' => 'success'
		]);
	}

	public function detail($id)
	{
		$fleet = Fleet::find($id);

		return response()->json([
			'status' => 'success',
			'data' => $fleet
		]);
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'plat_number' => 'required|string|unique:fleets,plat_number,'.$id,
			'type' => 'required',
			'user_id' => 'required',
			'photo' => 'nullable|image|mimes:jpg,jpeg,png'
		]);

		$fleet = Fleet::find($id);
		$filename = $fleet->photo;

		if ($request->hasFile('photo')) {
			$file = $request->file('photo');
			$filename = $request->plat_number.'-'.time().'.'.$file->getClientOriginalExtension();
			$file->move('fleets', $filename);

			File::delete(base_path('public/fleets/' . $fleet->photo));
		}

		$fleet->update([
			'plat_number' => $request->plat_number,
			'type' => $request->type,
			'user_id' => $request->user_id,
			'photo' => $filename
		]);

		return response()->json([
			'status' => 'success'
		]);
	}

	public function destroy($id)
	{
		$fleet = Fleet::find($id);

		if ($fleet) {
			File::delete(base_path('public/fleets/'.$fleet->photo));
			$fleet->delete();

			return response()->json([
				'status' => 'success'
			]);
		}

		return response()->json([
			'status' => 'error'
		]);
	}
}