<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscriptions extends Model
{

	protected $table = 'subscriptions';

    public $timestamps = false;

	protected $fillable = [
		'subscriberId',
        'categoryId'
	];

    /**
     * @return mixed
     */
	public function subscriber()
    {
        return $this->hasOne(Subscribers::class,'id','subscriberId');
    }

    /**
     * @return mixed
     */
    public function category()
    {
        return $this->hasOne(Category::class,'id','categoryId');
    }

}
