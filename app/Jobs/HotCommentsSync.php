<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use QL\QueryList;
use App\Song;
use App\HotComment;

class HotCommentsSync implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    // 工作线程数
    protected $concurrency = 5;
    // 传入的歌曲列表
    protected $songList = [];
    // 列表数量
    protected $total = 0;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($songList)
    {
        $this->songList = $songList;
        $this->total = count($songList);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $client = new Client();
        $songList = $this->songList;
        $requests = function ($total) use ($client, $songList) {
            foreach ($songList as $song) {
                $uri = 'http://music.163.com/api/song/detail?ids=['.$song['id'].']';
                yield function() use ($client, $uri) {
                    return $client->getAsync($uri);
                };
            }
        };

        $pool = new Pool($client, $requests($this->total), [
            'concurrency' => $this->concurrency,
            'fulfilled' => function ($response, $index) use ($client) {

                // 获取歌曲详情
                $songContents = json_decode($response->getBody()->getContents(), true);

                // 拆解组合歌曲详情
                $songInfo = [
                    'song_id' => array_get($songContents, 'songs.0.id'),
                    'title' => array_get($songContents, 'songs.0.name'),
                    'images' => 'https:'.ltrim(array_get($songContents, 'songs.0.album.picUrl'), 'http:'),
                    'author' => array_get($songContents, 'songs.0.artists.0.name'),
                    'description' => sprintf(
                        '歌手：%s。所属专辑：%s。', 
                        array_get($songContents, 'songs.0.artists.0.name'),
                        array_get($songContents, 'songs.0.album.name')
                    ),
                    'album' => array_get($songContents, 'songs.0.album.name'),
                    'published_date' => ((int)array_get($songContents, 'songs.0.album.publishTime') / 1000),
                ];

                $song = new Song($songInfo);

                // 如果歌曲已收录则跳过
                if ($song->where('song_id', $songInfo['song_id'])->count()) {
                    return false;
                }
                // 保存歌曲
                $song->save();

                // 获取热评
                $hotCommentsResponse = $client->request('GET', 'http://music.163.com/api/v1/resource/comments/R_SO_4_'. $songInfo['song_id'] .'?limit=15&offset=0');

                $_hotComments = json_decode($hotCommentsResponse->getBody()->getContents(), true);

                $hotComments = [];

                foreach ($_hotComments['hotComments'] as $hotc) {
                    $hotComments[] = [
                        'user_id' => $hotc['user']['userId'],
                        'nickname' => $hotc['user']['nickname'],
                        'avatar_url' => 'https:'.ltrim($hotc['user']['avatarUrl'], 'http:'),
                        'comment_id' => $hotc['commentId'],
                        'liked_count' => $hotc['likedCount'],
                        'content' => preg_replace('/\s+/', ' ', $hotc['content']),
                        'published_date' => date('Y/m/d H:i:s', ($hotc['time'] / 1000)),
                    ];
                }

                dump($hotComments);

                // 保存热评
                $song->hotcomment()->createMany($hotComments);

                if (++$index == $this->total) {
                    Log::info('同步完成,同步数: '. $this->total);
                }
            },
            'rejected' => function ($reason, $index){
                Log::error('同步失败,异常消息: '. $reason);
            },
        ]);

        $promise = $pool->promise();
        $promise->wait();
    }
}
