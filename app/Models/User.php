<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements  JWTSubject
{
    use Traits\ActiveUserHelper;
    use Traits\LastActivedAtHelper;
    use HasRoles, Notifiable {
        notify as protected laravelNotify;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'phone','password','introduction','avatar','weixin_openid','weixin_unionid'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * user topics
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    /**
     * user's reply of topic
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }


    public function isAuthorOf($model)
    {
        return $this->id == $model->user_id;
    }

    /**
     * Reply Notify
     * @param $instance
     */
    public function notify($instance)
    {
        if($this->id == Auth::id()) {
            return;
        }
        $this->increment('notification_count');
        $this->laravelNotify($instance);
    }

    /**
     * Read Message
     */
    public function markAsRead()
    {
        $this->notification_count =0;
        $this->save();

        $this->unreadNotifications->markAsRead();

    }

    public function setPasswordAttribute($value)
    {
        if( strlen($value) != 60){
            $value = bcrypt($value);
        }
        $this->attributes['password'] = $value;

    }

    public function setAvatarAttribute($path)
    {
        if(!starts_with($path,'http')){
            //pinjie
            $path = config('app.url')."/uploads/images/avatars/$path";
        }
        $this->attributes['avatar'] = $path;
    }

    public function getJWTIdentifier()
    {
        // TODO: Implement getJWTIdentifier() method.
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        // TODO: Implement getJWTCustomClaims() method.
        return [];
    }
}
