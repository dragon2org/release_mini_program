<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComponentTable extends Migration
{
    protected $tableName = 'component';

    protected $tableComment = '三方平台表';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments($this->tableName . '_id')->unsigned()->comment('自增id');
            $table->string('inner_name', 45)->default('')->comment('内部名称');
            $table->string('inner_desc', 45)->default('')->comment('内部描述');
            $table->string('inner_key', 45)->default('')->comment('内部key');
            $table->string('name', 45)->default('')->comment('三方平台名称');
            $table->string('desc', 45)->default('')->comment('三方平台描述');

            $table->string('app_id', 32)->default('')->comment('三方平台AppID');
            $table->string('app_secret', 32)->default('')->comment('三方平台AppSecret');
            $table->string('verify_token', 45)->default('')->comment('三方平台消息验证token');
            $table->string('verify_ticket', 45)->default('')->comment('三方平台消息验证verify_ticket');
            $table->string('aes_key', 43)->default('')->comment('三方平台消息解密解密Key');
            $table->string('validate_filename', 43)->default('')->comment('三方平台验证域名-文件名');
            $table->string('validate_content', 43)->default('')->comment('三方平台验证域名-文件内容');

            $table->unique('inner_key', 'uniq_inner_key');
            $table->unique('app_id', 'uniq_app_id');

            $table->string('field1', 45)->default('')->comment('备用字段');
            $table->string('field2', 45)->default('')->comment('备用字段2');

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
