<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Item;

class LikeSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $rows = [];
        foreach (User::all() as $u) {
            $ids = Item::where('user_id', '!=', $u->id)->inRandomOrder()->limit(5)->pluck('id');
            foreach ($ids as $id) $rows[] = ['item_id' => $id, 'user_id' => $u->id, 'created_at' => $now, 'updated_at' => $now];
        }
        DB::table('likes')->upsert($rows, ['item_id', 'user_id'], ['updated_at']);
    }
}
