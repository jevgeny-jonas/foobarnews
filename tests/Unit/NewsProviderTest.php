<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\NewsProvider;

class NewsProviderTest extends TestCase
{
    public function getTestData_getWordStats()
    {
        return [
            'basic' => [
                'items' => [
                    [
                        'title' => '<h2>test</h2> <b>abc</b>',
                        'description' => 'abc <p>def def</p> def',
                        'permalink' => 'https://www.abc.com/',
                    ],
                ],
                'max_count' => 100,
                'filter' => [],
                'expected' => ['test' => 1, 'abc' => 2, 'def' => 3],
            ],
            'different_case' => [
                'items' => [
                    [
                        'title' => '<h2>test test abc <strong>DEF</strong></h2>',
                        'description' => '<p>def</p> <div>d<i>e</i>F</div>',
                        'permalink' => 'https://www.abc.com/',
                    ],
                ],
                'max_count' => 2,
                'filter' => [],
                'expected' => ['test' => 2, 'def' => 3],
            ],
            'part_of_word_enclosed_in_tags' => [
                'items' => [
                    [
                        'title' => 'def',
                        'description' => 'def <div>d<i>e</i>f</div>',
                        'permalink' => 'https://www.abc.com/',
                    ],
                ],
                'max_count' => 100,
                'filter' => [],
                'expected' => ['def' => 3],
            ],
            'comments' => [
                'items' => [
                    [
                        'title' => '<h2>test</h2>',
                        'description' => ' <!-- comment def -->',
                        'permalink' => 'https://www.abc.com/',
                    ],
                ],
                'max_count' => 100,
                'filter' => [],
                'expected' => ['test' => 1],
            ],
            'multiple_items' => [
                'items' => [
                    [
                        'title' => '<h2>test</h2>',
                        'description' => '<p>def</p>',
                        'permalink' => 'https://www.abc.com/',
                    ],
                    [
                        'title' => '<h2>test 2</h2>',
                        'description' => '<p>def 2</p> def',
                        'permalink' => 'https://www.abc.com/',
                    ],
                ],
                'max_count' => 2,
                'filter' => [],
                'expected' => ['test' => 2, 'def' => 3],
            ],
            'filter' => [
                'items' => [
                    [
                        'title' => '<h2>test</h2> def ghi jkl',
                        'description' => '<p>def</p> test ghi ghi def jkl jkl',
                        'permalink' => 'https://www.abc.com/',
                    ],
                ],
                'max_count' => 100,
                'filter' => ['ghi', 'jkl'],
                'expected' => ['test' => 2, 'def' => 3],
            ],
            'filter_different_case' => [
                'items' => [
                    [
                        'title' => 'tEsT TEST test',
                        'description' => 'def',
                        'permalink' => 'https://www.abc.com/',
                    ],
                ],
                'max_count' => 100,
                'filter' => ['TeSt'],
                'expected' => ['def' => 1],
            ],
            'apostrophes' => [
                'items' => [
                    [
                        'title' => "it's",
                        'description' => 'abc abc',
                        'permalink' => 'https://www.abc.com/',
                    ],
                ],
                'max_count' => 100,
                'filter' => [],
                'expected' => ["it's" => 1, 'abc' => 2],
            ],
        ];
    }
    
    /**
     * @dataProvider getTestData_getWordStats
     */
    public function test_getWordStats($items, $maxCount, $filter, $expected)
    {
        $newsProvider = new NewsProvider(resolve('Psr\SimpleCache\CacheInterface'), $maxCount, $filter);
        $stats = $this->invokeMethod($newsProvider, 'getWordStats', [$items]);
        
        $this->assertEquals($expected, $stats);
    }
    
    public function test_getWordStats_doesNotExceedMaxCountWhenThereAreMultipleWordsWithSameFrequency()
    {
        $items = [
            [
                'title' => 'test abc abc def def',
                'description' => 'ghi ghi jkl jkl mno mno',
                'permalink' => 'https://www.abc.com/',
            ],
        ];
        $maxCount = 3;
        $filter = [];
        
        $newsProvider = new NewsProvider(resolve('Psr\SimpleCache\CacheInterface'), $maxCount, $filter);
        $stats = $this->invokeMethod($newsProvider, 'getWordStats', [$items]);
        
        $this->assertEquals($maxCount, count($stats));
    }
}
