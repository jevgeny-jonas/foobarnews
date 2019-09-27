<?php

namespace App\Http\Controllers;

use App\NewsProviderInterface;

class NewsController extends Controller
{
    protected $newsProvider;
    
    public function __construct(NewsProviderInterface $newsProvider)
    {
        $this->newsProvider = $newsProvider;
    }
    
    /**
     * @throws App\Exceptions\NewsException
     */
    public function index()
    {
        $news = $this->newsProvider->getNews();
        return view('news')->with([
            'news' => $news,
        ]);
        //if NewsException is thrown, let it propagate to return 500
    }
}
