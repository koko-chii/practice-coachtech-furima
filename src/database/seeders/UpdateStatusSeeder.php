<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

//すでにデータベースに登録されているすべての商品の『売り切れフラグ（is_sold）』を、
// まとめて一瞬で『販売中（0）』に書き換えてリセットするための記述です
class UpdateStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('items')->update(['is_sold' => 0]);
    }
}
