<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMiniProgramTemplateTable extends Migration
{
    protected $tableName = 'mini_program_template';

    protected $tableComment = '小程序模板表';

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
            $table->integer('template_id')->default(0)->comment('模板id');
            $table->integer('draft_id')->default(0)->comment('草稿id');
            $table->string('wechat_version', 20)->default('')->comment('微信基础库版本');
            $table->string('user_version', 45)->default('')->comment('模版版本号，开发者自定义字段');
            $table->string('user_desc', 45)->default('')->comment('模版描述 开发者自定义字段');
            $table->string('create_time', 15)->default('')->comment('开发者上传草稿时间');
            $table->string('desc', 45)->default('')->comment('描述,备注');

            $table->string('field1', 45)->default('')->comment('备用字段');
            $table->string('field2', 45)->default('')->comment('备用字段2');

            $table->unique(['template_id', 'component_app_id'], 'uniq_component_app_template_id');

            $table->tinyInteger('deleted')->default(0)->comment('软删除标志');
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
