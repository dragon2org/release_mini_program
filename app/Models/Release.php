<?php

namespace App\Models;

use App\Models\Traits\SoftDeletes;
use App\ReleaseConfigurator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Release extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'release';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'release_id';

    public function item()
    {
        return $this->hasMany(ReleaseItem::class, 'release_id', release_id);
    }


    public function createReleaseTrans(MiniProgram $miniProgram, $templateId, $config)
    {
        $tradeNo = $this->genTradeNo($miniProgram->mini_program_id);
        $model = (new self());
        $model->component_id = $miniProgram->component_id;
        $model->mini_program_id = $miniProgram->mini_program_id;
        $model->template_id = $templateId;
        $model->trade_no = $tradeNo;
        $model->config = json_encode($config, JSON_UNESCAPED_UNICODE);
        $model->save();

        return $model;
    }

    protected function genTradeNo($id)
    {
        $id = str_pad($id, 4, 0, 0);
        return 'R' .  date('YmdHis') . $id . Str::random(5);
    }

    public function getReleaseConfigurator()
    {
        $config = json_decode($this->config, true);
        return new ReleaseConfigurator($config);
    }
}
