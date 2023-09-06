<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TitleListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get(public_path().'/data/list.json');
        $objs = json_decode($json);

        foreach($objs as $obj){
            DB::table('title_lists')->insert([
                'user_id' => $obj->user_id,
                'title' => $obj->title,
                'author' => $obj->author,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }
    }
}
