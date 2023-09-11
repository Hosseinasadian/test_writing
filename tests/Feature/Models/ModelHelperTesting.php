<?php

namespace Tests\Feature\Models;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

trait ModelHelperTesting
{
    public function testInsertData(): void
    {
        $model = $this->model();
        $table = $model->getTable();
        $data = $model::factory()->make()->toArray();

        if ($model instanceof User)
            $data['password'] = Hash::make('123456');

        $model::create($data);

        $this->assertDatabaseHas($table, $data);
    }

    abstract protected function model():Model;
}
