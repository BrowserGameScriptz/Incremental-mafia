<?php

namespace App;

use Carbon\Carbon;

use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * App\User
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Soldier[] $soldiers
 * @mixin \Eloquent
 */
class User extends Authenticatable
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $dates = [
        'lastIPUpdate'
    ];

    public function soldiers(){
        return $this->hasMany(Soldier::class);
    }

    public function updateIP(){
        $dateParsed = Carbon::parse($this->lastIPUpdate);
        $diffInMinutes = Carbon::now()->diffInSeconds($dateParsed);
        $totalInfluencePerMinute = $this->getTotalInfluencePerSecond();
        $modifier = floor($diffInMinutes * $totalInfluencePerMinute);
        if($modifier > 0)
            $this->influence += $modifier;
        $this->save();
        return $this->influence;
    }

    public function decreaseIP($amount){
        $this->influence -= $amount;
        if($this->influence < 0)
            $this->influence = 0;
        $this->save();
    }

    //

    /**
     * @return the total influence per minute of this user.
     */
    private function getTotalInfluencePerMinute()
    {
        $totalInfluencePerMinute = 0;
        foreach ($this->soldiers as $soldier)
            $totalInfluencePerMinute += $soldier->influence_per_minute;
        return $totalInfluencePerMinute;
    }

    public function getTotalInfluencePerSecond()
    {
        return $this->getTotalInfluencePerMinute()/60;
    }

    /**
     * @return the user already redeemd its daily bonus ip.
     */
    public function bonusIpRedeemed()
    {
        return Carbon::now()->day == Carbon::parse($this->bonusIpRedeemDate)->day;
    }

}
