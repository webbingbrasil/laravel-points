<?php

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

    /**
     *
     * @return mix
     */
    public function averagePoint($round = null)
    {
        if ($round) {
            return $this->points()
                ->selectRaw('ROUND(AVG(amount), ' . $round . ') as averagePointTransaction')
                ->pluck('averagePointTransaction');
        }

        return $this->points()
            ->selectRaw('AVG(amount) as averagePointTransaction')
            ->pluck('averagePointTransaction');
    }

    /**
     *
     * @return mix
     */
    public function countPoint()
    {
        return $this->points()
            ->selectRaw('count(amount) as countTransactions')
            ->pluck('countTransactions');
    }

    /**
     *
     * @return mix
     */
    public function sumPoint()
    {
        return $this->points()
            ->selectRaw('SUM(amount) as sumPointTransactions')
            ->pluck('sumPointTransactions');
    }

    /**
     * @param $max
     *
     * @return mix
     */
    public function pointPercent($max = 5)
    {
        $points = $this->points();
        $quantity = $points->count();
        $total = $points->selectRaw('SUM(amount) as total')->pluck('total');
        return ($quantity * $max) > 0 ? $total / (($quantity * $max) / 100) : 0;
    }

    /**
     *
     * @return mix
     */
    public function countPoints()
    {
        return $this->points()->count();
    }

    /**
     * @return float
     */
    public function currentPoints()
    {
        $currentPoint = Point::where('pointable_id', $this->getKey())
            ->where('pointable_type', $this->getMorphClass())
            ->orderBy('created_at', 'desc')
            ->take(1)
            ->pluck('current')
            ->first();

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
