<?php

namespace App\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LiamWiltshire\LaravelJitLoader\Concerns\AutoloadsRelationships;

class Server extends Model
{
    protected $table = 'servers';

    protected $guarded = ['id'];

    protected $hidden = [
        'created_at',
        'updated_at',
        'pivot'
    ];

    protected $fillable = [
        'nameServer',
        'urlServer',
        'pathSource',
        'pathLog',
        'scriptStart',
        'scriptStop',
        'scriptTask',
        'urlGit',
        'lastRunTime',
        'lastSuccessTime',
        'lastFailTime',
        'status',
    ];
}
