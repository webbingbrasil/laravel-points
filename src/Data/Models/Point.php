<?php
/**
 *  Copyright (c) 2018 Webbing Brasil (http://www.webbingbrasil.com.br)
 *  All Rights Reserved
 *
 *  This file is part of the calculadora-triunfo project.
 *
 *  @project calculadora-triunfo
 *  @file Point.php
 *  @author Danilo Andrade <danilo@webbingbrasil.com.br>
 *  @date 13/08/18 at 12:09
 *  @copyright  Copyright (c) 2018 Webbing Brasil (http://www.webbingbrasil.com.br)
 */

namespace WebbingBrasil\Points\Data\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Point
 *
 * @package Webbingcms\Points\Data\Models
 */
class Point extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'points';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'message',
        'amount',
        'current'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function pointable()
    {
        return $this->morphTo();
    }

    /**
     * @param null $class
     * @param null $id
     * @return \Illuminate\Support\Collection
     */
    public static function leaderboard($class = null, $id = null)
    {
        $class = str_replace('\\', '\\\\', $class);
        $query = "(SELECT pointable_type, pointable_id, current, @rownum := @rownum + 1 AS position FROM (SELECT * FROM (SELECT p.pointable_type, p.pointable_id, max(p.current) current FROM points p GROUP BY p.pointable_type, p.pointable_id) as t " . (is_null($class) ? '' : "WHERE pointable_type = '{$class}'") . " GROUP BY pointable_type, pointable_id ORDER BY current DESC) l JOIN (SELECT @rownum := 0) r ) v";
        $rank = \DB::table(\DB::raw($query));

        if(!is_null($id)) {
            $rank->where('pointable_id', $id);
        }

        return collect($rank->get());
    }
}
