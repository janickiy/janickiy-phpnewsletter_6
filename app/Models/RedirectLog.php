<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RedirectLog extends Model
{
	protected $table = 'redirect_log';

    protected $primaryKey = 'id';

	protected $fillable = [
		'url',
        'email'
	];
}