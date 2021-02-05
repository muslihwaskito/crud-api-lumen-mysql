<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Fleet
 */
class Fleet extends Model
{
	protected $guarded = [];

	protected $appends = ['photo_url'];

	public function getPhotoUrlAttribute()
	{
		return url('fleets/'.$this->photo);
	}

	public function user()
	{
		return $this->belongsTo(User::class)->select('id','name');
	}
}