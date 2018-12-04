<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReleaseTable extends Migration
{
    protected $tableName = 'release';

    protected $tableComment = '发版记录表';

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
            $table->integer('template_id')->default(0)->unsigned()->comment('模板id');
            $table->string('user_version', 45)->default('')->comment('代码版本号');
            $table->string('user_desc', 45)->default('')->comment('代码描述');
            $table->string('ext_json', 45)->default('')->comment('自定义配置');
            $table->string('desc', 45)->default('')->comment('描述,备注');
            $table->string('status', 45)->default('')->comment('10已上传.11已提交审核.13审核未通过.20发布成功');
            $table->string('audit_id', 45)->default('')->comment('最新的微信审核id');

            $table->string('field1', 45)->default('')->comment('备用字段');
            $table->string('field2', 45)->default('')->comment('备用字段2');

            $table->index('template_id', 'idx_template_id');
            $table->index('mini_program_id', 'idx_mini_program_id');
            $table->index('component_app_id', 'idx_component_app_id');
            $table->index('status', 'idx_status');

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
