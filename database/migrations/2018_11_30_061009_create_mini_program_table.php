<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMiniProgramTable extends Migration
{
    protected $tableName = 'mini_program';

    protected $tableComment = '已授权小程序表';

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
            $table->integer('company_id')->default(0)->unsigned()->comment('公司id');
            $table->string('inner_name', 45)->default('')->comment('内部名称');
            $table->string('inner_desc', 45)->default('')->comment('内部描述');
            $table->string('inner_key', 45)->default('')->comment('内部key');

            $table->string('nick_name', 45)->default('')->comment('授权方昵称');
            $table->string('head_img')->default('')->comment('小程序头像');
            $table->string('app_id', 45)->default('')->comment('小程序AppID');
            $table->string('user_name', 45)->default('')->comment('原始ID,审核推送要用');
            $table->string('principal_name', 45)->default('')->comment('小程序主体名称');
            $table->string('qrcode_url')->default('')->comment('二维码图片的URL');
            $table->string('desc', 45)->default('')->comment('小程序平台描述');
            $table->string('authorizer_refresh_token', 43)->default('')->comment('获取（刷新）授权公众号或小程序的接口调用凭据');
            $table->string('user_version', 43)->default('')->comment('当前版本');

            $table->unique('inner_key', 'uniq_inner_key');
            $table->index('app_id', 'idx_app_id');
            $table->index('user_name', 'idx_user_name');
            $table->index('user_name', 'authorizer_refresh_token');

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
