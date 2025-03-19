<?php

namespace App\Modules\YagodaTips\Database\Migrations;

use Illuminate\Database\Migrations\Migration as BaseMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

abstract class Migration extends BaseMigration
{
    protected function createWithPrefix($tableName, callable $callback)
    {
        Schema::create('tips_' . $tableName, $callback);
    }

    protected function tableWithPrefix($tableName, callable $callback)
    {
        Schema::table('tips_' . $tableName, $callback);
    }
}