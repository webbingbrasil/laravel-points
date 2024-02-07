<?php
/**
 *  Copyright (c) 2018 Webbing Brasil (http://www.webbingbrasil.com.br)
 *  All Rights Reserved
 *
 *  This file is part of the calculadora-triunfo project.
 *
 *  @project calculadora-triunfo
 *  @file HasPoints.php
 *  @author Danilo Andrade <danilo@webbingbrasil.com.br>
 *  @date 13/08/18 at 12:46
 *  @copyright  Copyright (c) 2018 Webbing Brasil (http://www.webbingbrasil.com.br)
 */

namespace WebbingBrasil\Points\Traits;

use WebbingBrasil\Points\Data\Models\Point;

trait HasPoints
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function points($amount = null)
    {
        return $this->morphMany(Point::class, 'pointable')->orderBy('created_at', 'desc')->take($amount);
    }

    public function rankPosition()
    {
        $position = Point::leaderboard($this->getMorphClass(), $this->getKey())->pluck('position')->first();

        return $position;

    }

    /**
     *
     * @return double|integer
     */
    public function avgPoints()
    {
        return $this->points()->avg('amount');
    }

    /**
     *
     * @return integer
     */
    public function countPoints()
    {
        return $this->points()->count();
    }

    /**
     *
     * @return double|integer
     */
    public function sumPoints()
    {
        return $this->points()->sum('amount');
    }

    /**
     * @param $max
     *
     * @return double|integer
     */
    public function pointPercent($max = 5)
    {
        $quantity = $this->countPoints();
        $total = $this->sumPoints();

        if($quantity == 0 || $max == 0) {
            return 0;
        }

        return $total / (($quantity * $max) / 100);
    }

    /**
     * @return float
     */
    public function currentPoints()
    {
        $currentPoint = $this->points(1)->latest()->pluck('current')->first();

        if (!$currentPoint) {
            $currentPoint = 0.0;
        }

        return $currentPoint;
    }

    /**
     * @param $amount
     * @param $message
     * @param $data
     *
     * @return Point
     */
    public function addPoints($amount, $message, $data = null)
    {
        $point = new Point();
        $point->amount = $amount;

        $point->current = $this->currentPoints() + $amount;

        $point->message = $message;
        if ($data) {
            $point->fill($data);
        }

        $this->points()->save($point);

        return $point;
    }
}
