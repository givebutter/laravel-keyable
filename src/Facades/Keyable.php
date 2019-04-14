<?php
	
namespace Givebutter\LaravelKeyable\Facades;

use Illuminate\Support\Facades\Facade;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\AuthorizationException;

use App\Models\Campaign\Campaign;
use App\Policies\KeyablePolicies\CampaignPolicy;

class Keyable extends Facade
{
	
	protected static function getFacadeAccessor() { return 'KeyableClass'; }
	
}