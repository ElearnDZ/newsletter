<?php

use Illuminate\Database\Seeder;

use App\NewsletterReason;
use Carbon\Carbon;

class NewsletterReasonTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $reason = new NewsletterReason;
        $reasons = [
        	'This newsletter is spammy',
        	'I\'m not interested anymore with this (and related) product',
        	'I don\'t know', 
        	'Other — Please provide reason below '
        ];

        foreach ($reasons as $reasonText) {
        	DB::table($reason->getTable())->insert([
        		'description' => $reasonText,
        		'created_at' => Carbon::now(),
        		'updated_at' => Carbon::now()
        	]);
        } 
    }
}
