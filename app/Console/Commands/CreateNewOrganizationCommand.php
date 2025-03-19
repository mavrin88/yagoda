<?php

namespace App\Console\Commands;

use App\Models\Organization;
use App\Models\OrganizationStatus;
use App\Models\Setting;
use Illuminate\Console\Command;
use App\Models\Category;
use App\Models\Product;

class CreateNewOrganizationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-new-organization-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $organization = Organization::create(
            [
                'account_id' => 1,
                'status' => OrganizationStatus::STATUS_ACTIVE,
                'name' => '',
                'full_name' => '',
                'phone' => '',
                'inn' => '',
                'contact_phone' => '',
                'contact_name' => '',
                'legal_address' => '',
                'email' => '',
                'registration_number' => '',
                'acquiring_fee' => '17',
                'tips_1' => Setting::where('key', 'tips_1')->first()->value,
                'tips_2' => Setting::where('key', 'tips_2')->first()->value,
                'tips_3' => Setting::where('key', 'tips_3')->first()->value,
                'tips_4' => Setting::where('key', 'tips_4')->first()->value,
                'activity_type_id' => 1,
                'form_id' => 1,
            ]
        );

        $categories = $organization->categories()->where('hide', '=', true)->get();

        if ($categories->isEmpty()) {
            $category = Category::create([
                'name' => 'Скрытая категория',
                'organization_id' => $organization->id,
                'bill_id' => 1,
                'hide' => true
            ]);

            Product::create([
                'name' => 'Товар/Услуга',
                'category_id' => $category->id,
                'price' => 0,
                'hide' => true
            ]);
        }
    }
}
