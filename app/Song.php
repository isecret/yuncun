<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client as Http;
use GuzzleHttp\Pool;
use QL\QueryList;

class Song extends Model 
{
    protected $guarded = [];
    // 线程数
    protected $concurrency = 5;

    protected $dates = [
        'published_date'
    ];

    public function hotcomment()
    {
        return $this->hasMany('App\HotComment', 'song_id', 'song_id');
    }

    /**
     * 获取热歌榜歌曲列表
     *
     * @return array
     */
    public function getTopList()
    {
        return $this->getList('https://music.163.com/discover/toplist?id=3778678');
    }

    /**
     * 通过歌单 Id 获取歌曲列表
     *
     * @param string $id
     * @return void
     */
    public function getPlayList($id)
    {
        return $this->getList('https://music.163.com/playlist?id='.$id);
    }

    /**
     * 获取歌单列表
     *
     * @param string $url
     * @return array
     */
    public function getList($url)
    {
        $http = new Http();

        $contents = $http->request('GET', $url, [
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8'
            ]
        ]);

        $hotSongs = QueryList::html($contents->getBody()->getContents())->rules([
            'id' => ['.f-hide a', 'href'],
            'title' => ['.f-hide a', 'text']
        ])->query()->getData(function($item) {
            return [
                'id' => str_after($item['id'], '='),
                'title' => $item['title']
            ];
        });

        return $hotSongs->toArray();
    }
}
