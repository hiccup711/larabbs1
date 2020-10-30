<?php
namespace App\Observers;

use App\Models\Link;
use Illuminate\Support\Facades\Cache;

class LinkObserver{
    public function __construct(Link $link)
    {
        Cache::forget($link->cache_key);
    }

    public function saved()
    {

    }

    public function deleted()
    {
        
    }
}
