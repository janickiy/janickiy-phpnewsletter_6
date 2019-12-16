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

}