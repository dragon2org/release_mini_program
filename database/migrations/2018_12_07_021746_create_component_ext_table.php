<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComponentExtTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    protected $tableName = 'component_ext';

    protected $tableComment = '三方平台extra配置信息';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('component_ext', function (Blueprint $table) {
            $table->increments($this->tableName . '_id')->unsigned()->comment('自增id');
            $table->integer('component_app_id')->default(0)->unsigned()->comment('关联的三方平台ID');

            $table->string('field1', 45)->default('')->comment('备用字段');
            $table->string('field2', 45)->default('')->comment('备用字段2');

            $table->index('component_app_id', 'idx_component_app_id');

            $table->timestamp('created_at')->default('1970-01-01 08:00:01')->comment('记录添加时间');
            $table->timestamp('updated_at')->default('1970-01-01 08:00:01')->comment('记录更新时间');
        });
        DB::statement("ALTER TABLE `{$this->tableName}`  comment  '{$this->tableComment}'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->tableName);
    }
}
