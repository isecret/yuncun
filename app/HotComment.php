<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HotComment extends Model 
{
    protected $guarded = [];

    public function song()
    {
        return $this->belongsTo('App\Song', 'song_id', 'song_id');
    }

    public static function getRandHotComment($length=1)
    {
        $comments = [];
        for ($i=0; $i<$length; $i++) {
            $comments[] = self::getOneRandHotComment();
        }

        return collect($comments);
    }

    public static function getOneRandHotComment()
    {
        $comment = collect(DB::select("
            SELECT 
                t3.song_id, 
                t3.title, 
                t3.images, 
                t3.author, 
                t3.album, 
                t3.description, 
                '' as 'mp3_url',
                t3.published_date,
                t1.comment_id, 
                t1.user_id AS comment_user_id, 
                t1.nickname AS comment_nickname, 
                t1.avatar_url AS comment_avatar_url, 
                t1.liked_count AS comment_liked_count, 
                t1.content AS comment_content, 
                t1.published_date AS comment_published_date
            FROM hot_comments t1
            JOIN (
                SELECT ROUND(RAND() * ((
                        SELECT MAX(id)
                        FROM hot_comments
                    ) - (
                        SELECT MIN(id)
                        FROM hot_comments
                    )) + (
                        SELECT MIN(id)
                        FROM hot_comments
                    )) AS id
            ) t2
            JOIN songs t3 ON t1.song_id = t3.song_id
            WHERE t1.id >= t2.id
            LIMIT 0, 1;
        "))->first();

        // 插入音乐链接
        $comment->mp3_url = 'https://api.comments.hk/music/'.$comment->song_id;

        // TODO 歌词链接 http://music.163.com/api/song/media?id=

        return $comment;
    }
}
