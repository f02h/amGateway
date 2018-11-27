<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class Msg extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $primaryKey = 'idGatewayMsg';
    protected $table = 'gatewayMsg';

    protected $fillable = [
        'idGateway','domain', 'msgAction', 'msgStatus', 'msgDate', 'msgId', 'msg', 'status', 'instance',
    ];

}
