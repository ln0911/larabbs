<?php
/**
 * Created by PhpStorm.
 * User: lining
 * Date: 2018/11/7
 * Time: 1:44 PM
 */
namespace  App\Models\Traits;

use App\Models\Reply;
use App\Models\Topic;
use Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait ActiveUserHelper
{

    //存放临时用户数据
    protected $users = [];

    //配置信息
    protected $topic_weight = 4; //话题权重
    protected $reply_weight = 1; //回复权重
    protected $pass_days    = 7; //时间段之内
    protected $user_number  = 6; //取用户基数

    //缓存配置
    protected $cache_key = 'larabbs_active_users';
    protected $cache_expire_in_minutes = 65;

    public function getActiveUsers()
    {
        return Cache::remember($this->cache_key,$this->cache_expire_in_minutes , function (){
            return $this->calculateActiveUsers();
        });
    }

    public function calculateAndCacheActiveUsers()
    {
        $active_users = $this->calculateActiveUsers();

        //放入缓存
        $this->cacheActiveUsers($active_users);
    }


    /**
     * 根据得分获取活跃用户数据
     * @return \Illuminate\Support\Collection
     */
    public function calculateActiveUsers()
    {
        $this->calculateTopicScore();
        $this->calculateReplyScore();

        $users = array_sort($this->users , function($user){
            return $user['score'];
        });
        // 我们需要的是倒序，高分靠前，第二个参数为保持数组的 KEY 不变
        $users = array_reverse($users, true);

        // 只获取我们想要的数量
        $users = array_slice($users, 0, $this->user_number, true);

        // 新建一个空集合
        $active_users = collect();

        foreach ($users as $user_id=>$user){
            // 找寻下是否可以找到用户
            $user = $this->find($user_id);

            if($user){
                $active_users->push($user);
            }
        }
        return $active_users;
    }

    /**
     * calculate topic score user
     */
    private function calculateTopicScore()
    {
        // 从话题数据表里取出限定时间范围（$pass_days）内，有发表过话题的用户
        // 并且同时取出用户此段时间内发布话题的数量
        $topic_users = Topic::select(DB::raw('user_id, count(*) as topic_count'))->where('created_at','>',Carbon::now()->subDays($this->pass_days))
            ->groupBy('user_id')->get();
        foreach ($topic_users as $value){
            $this->users[$value->user_id]['score'] = $value->topic_count * $this->topic_weight;
        }
    }

    /**
     * calculate reply score user
     */
    private function calculateReplyScore()
    {
        // 从回复数据表里取出限定时间范围（$pass_days）内，有发表过回复的用户
        // 并且同时取出用户此段时间内发布回复的数量
        $reply_users = Reply::select(DB::raw('user_id,count(*) as reply_count'))->where('created_at','>',Carbon::now()->subDays($this->pass_days))
            ->groupBy('user_id')->get();
        foreach ($reply_users as $value){
            $reply_score = $value->reply_count * $this->reply_weight;

            if(isset($this->users[$value->user_id])) {
                $this->users[$value->user_id]['score'] += $reply_score;
            }else{
                $this->users[$value->user_id]['score'] = $reply_score;
            }
        }
    }

    /**
     * cache active_user
     * @param $active_users
     */
    private function cacheActiveUsers($active_users)
    {
        Cache::put($this->cache_key,$active_users,$this->cache_expire_in_minutes);
    }
}