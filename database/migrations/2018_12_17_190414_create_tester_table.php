<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTesterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    protected $tableName = 'tester';

    protected $tableComment = '小程序体验者';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments($this->tableName . '_id')->unsigned()->comment('自增id');
            $table->integer('mini_program_id')->default(0)->unsigned()->comment('小程序id');
            $table->string('app_id', 100)->default(0)->unsigned()->comment('小程序id');
            $table->string('userstr')->default('')->comment('wechat_id别名');
            $table->string('wechat_id')->default('')->comment('微信id');

            $table->string('field1', 45)->default('')->comment('备用字段');
            $table->string('field2', 45)->default('')->comment('备用字段2');

            $table->index('mini_program_id', 'idx_mini_program_id');
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
