<?php

namespace App;

use App\Exceptions\NewsException;
use Illuminate\Support\Facades\App;
use Psr\SimpleCache\CacheInterface;

class NewsProvider implements NewsProviderInterface
{
    protected $url = 'https://www.theregister.co.uk/software/headlines.atom';
    protected $cacheTimeoutSec = 600;
    protected $cache;
    
    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }
    
    protected function getCacheKey(): string
    {
        return 'App\NewsProvider#news#' . App::environment();
    }
    
    /**
     * {@inheritDoc}
     */
    public function getNews(): array
    {
        $feed = $this->cache->get($this->getCacheKey());
        
        if ($feed !== null) {
            assert(is_array($feed), '$feed (from cache) must be array, but is: ' . var_export($feed, true));
            return $feed;
        }
        
        $feedStr = $this->fetch();
        $feedArr = $this->parse($feedStr);
        
        $this->cache->set($this->getCacheKey(), $feedArr, $this->cacheTimeoutSec);
        
        return $feedArr;
    }
    
    private function fetch(): string
    {
        //ensuring that error handling is correct both when errors are being converted to exceptions and when not
        try {
            $wrappedExc = null;
            $feed = file_get_contents($this->url);
        } catch (\ErrorException $exc) {
            //do not log exception to error log because it indicates failure to retrieve data from external service and is not bug in code
            
            $feed = false;
            $wrappedExc = $exc;
        }
      
        if ($feed === false) {
            throw new NewsException('Cannot fetch feed from server.', null, $wrappedExc);
        } else {
            return $feed;
        }
    }
    
    private function parse(string $feedStr): array
    {
        try {
            $error = '';
            $wrappedExc = null;
            
            $feedObj = new \SimplePie();
            $feedObj->enable_cache(false);
            $feedObj->set_raw_data($feedStr);
            $success = $feedObj->init();
            
            if ($success) {
                $feedArr = $this->toArray($feedObj);
            } else {
                $feedArr = false;
                $error = $feedObj->error();
            }
        } catch (\ErrorException $exc) {
            //in case the lib does not gracefully handle invalid xml
            
            $feedArr = false;
            $wrappedExc = $exc;
            
            //that's bug in code, so report the exception
            report($exc);
        }
        
        if ($feedArr === false) {
            throw new NewsException(
                'Failed to parse feed.' . ($error == '' ? '' : (' Error msg: ' . $error)),
                null,
                $wrappedExc
            );
        } else {
            return $feedArr;
        }
    }
    
    private function toArray(\SimplePie $feedObj): array
    {
        $res = ['items' => []];
        foreach ($feedObj->get_items() as $item) {
            $res['items'] []= [
                'title' => $item->get_title(),
                'description' => $item->get_description(),
                'permalink' => $item->get_permalink(),
            ];
        }
        return $res;
    }
}
