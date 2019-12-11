<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\User::class)->create();
        factory(App\Models\Checklist::class, 10)->create();
        factory(App\Models\ChecklistTemplate::class, 10)->create();
        factory(App\Models\ChecklistItem::class, 10)->create();
    }
}
