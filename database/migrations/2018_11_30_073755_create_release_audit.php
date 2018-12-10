<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReleaseAudit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    protected $tableName = 'release_audit';

    protected $tableComment = '发版审核申请结果表';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments($this->tableName . '_id')->unsigned()->comment('自增id');
            $table->integer('component_app_id')->default(0)->unsigned()->comment('关联的三方平台ID');
            $table->integer('mini_program_id')->default(0)->comment('小程序id');
            $table->string('release_id', 45)->default('')->comment('发版自增id');
            $table->string('status', 45)->default('')->comment('状态.1成功');
            $table->string('reason', 45)->default('')->comment('失败原因');

            $table->string('field1', 45)->default('')->comment('备用字段');
            $table->string('field2', 45)->default('')->comment('备用字段2');

            $table->index('release_id', 'idx_release_id');
            $table->index('mini_program_id', 'idx_mini_program_id');
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
