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
            $table->string('inner_desc')->default('')->comment('内部描述');
            $table->string('name', 45)->default('')->comment('三方平台名称');
            $table->string('desc')->default('')->comment('三方平台描述');

            $table->string('app_id', 100)->default('')->comment('三方平台AppID');
            $table->string('app_secret', 32)->default('')->comment('三方平台AppSecret');
            $table->string('verify_token', 45)->default('')->comment('三方平台消息验证token');
            $table->string('verify_ticket', 95)->default('')->comment('三方平台消息验证verify_ticket');
            $table->string('aes_key', 43)->default('')->comment('三方平台消息解密解密Key');
            $table->string('validate_filename', 43)->default('')->comment('三方平台验证域名-文件名');
            $table->string('validate_content', 43)->default('')->comment('三方平台验证域名-文件内容');

            $table->unique('app_id', 'uniq_app_id');
            $table->index('validate_filename', 'idx_validate_filename');
            $table->text('config')->comment('自定义发版配置');

            $table->string('field1', 45)->default('')->comment('备用字段');
            $table->string('field2', 45)->default('')->comment('备用字段2');

            $table->tinyInteger('is_deleted')->default(0)->comment('软删除标志');
            $table->string('create_user', 45)->default('')->comment('新建记录的用户');
            $table->string('update_user', 45)->default('')->comment('最后一次操作的用户');
            $table->dateTime('created_at')->default('1970-01-01 08:00:01')->comment('记录添加时间');
            $table->dateTime('updated_at')->default('1970-01-01 08:00:01')->comment('记录更新时间');
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
