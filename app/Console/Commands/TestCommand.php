<?phpnamespace App\Console\Commands;use App\Models\ActivityType;use App\Models\QrCode;use App\Models\Order;use App\Models\OrderStatistics;use App\Models\Setting;use App\Services\AtolService;use App\Services\CallService;use App\Services\PlusofonService;use App\Services\TochkaApiService;use App\Services\TochkaService\CommissionCalculator;use App\Services\VbrrService;use App\Services\YclientsService;use Illuminate\Console\Command;use App\Models\Role;use App\Models\Bill;use App\Models\Permission;use App\Models\User;use Illuminate\Support\Facades\Crypt;use Illuminate\Support\Facades\DB;use Illuminate\Support\Facades\Log;use Illuminate\Support\Str;use TelegramBot\Api\BotApi;use Illuminate\Support\Facades\Http;use Dadata\DadataClient;class TestCommand extends Command{    /**     * The name and signature of the console command.     *     * @var string     */    protected $signature = 't:t';    /**     * The console command description.     *     * @var string     */    protected $description = 'Command description';    /**     * Execute the console command.     */    public function handle(TochkaApiService $tochkaService, AtolService $atolService, CommissionCalculator $commissionCalculator)    {        // $orders = Order::where('full_amount', '>', 0)->where('id', '>=', 1000)->get();        // foreach ($orders as $key => $order) {        //     $orderStatistic = OrderStatistics::where('order_id', $order->id)->where('status', 'ok')->first();        //     if ($orderStatistic) {        //         $orderStatistic->full_amount = $order->full_amount;        //         $orderStatistic->save();        //     }        // }        // dd($orders);        // dd($commissionCalculator->calculateCommission(85.00, 'CARD'));        // $token = "032447cddd0d0042e3991012eaadbca93dc6ca73";        // $secret = "5db4f2893330e5d1ea3ae193287adf65bfd89303";        // $dadata = new DadataClient($token, $secret);        // $response = $dadata->findById("party", "772791944221");        // dd($response);//        Log::info('45654654654654654');//        $response = $this->decodeJwtWithoutSignature('eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJjdXN0b21lckNvZGUiOiAiMzA0NTkwNTg1IiwgIndlYmhvb2tUeXBlIjogImFjcXVpcmluZ0ludGVybmV0UGF5bWVudCIsICJhbW91bnQiOiAiMTI4LjAwIiwgInBheW1lbnRUeXBlIjogInNicCIsICJ0cmFuc2FjdGlvbklkIjogIjE2NWQ2YWEzLWZkMTQtM2E3MS01MjQ2LTI0NGZhOTgzYTE3NSIsICJwdXJwb3NlIjogIlx1MDQxN1x1MDQzMFx1MDQzYVx1MDQzMFx1MDQzNyA5NzogOWJmYmQzYjAtMjI5MC00MzdhLThmZDgtYmE3MTEwMjQyY2E3IiwgInFyY0lkIjogIkFEMjAwMDMwU1QzNU1OUjU4T1NPSEVVVDNSNU1CMVFNIiwgInBheWVyTmFtZSI6ICJcdTA0MTBcdTA0M2JcdTA0MzVcdTA0M2FcdTA0NDFcdTA0MzVcdTA0MzkgXHUwNDEyXHUwNDMwXHUwNDNiXHUwNDM1XHUwNDNkXHUwNDQyXHUwNDM4XHUwNDNkXHUwNDNlXHUwNDMyXHUwNDM4XHUwNDQ3IFx1MDQxYy4ifQ.CNjZEJy8YCDAIWaYJRdqyhCKN86N7pvxemPU-BgBbJoUe_oQpxocT24DkO3oLc_7aVGmBMnQ0n374XZNrG6n8DKwkb9zC2NMsAnZt0AnBlBZyS6p9VG1pP2mF1PK4Recy4n18oR1zeAzmkfPSTHE276aIEC4syozQKLwkaMwq7f44qtzZfRIbrImW-WjqP24WOmMjsdhOsOrYq3FgYDrcWJi7nChFu3RaSZYw1te79kvWBuQqujiLbFUzpFL8oY5x3ocpnL2KMNtMzzWBr4RI9MMuhWB8ZkZ90phccKkPd3FwZjfOC5sYpyHMDNl9Kfz1-pVrTbJd5URHHe7yohOo9y0jyHET6R66GyJPPBt3l843KHr-khjbc4SHhh4QvqAoqVmCpFhMFq4A0DUIGkELlRQLuFHTUXJwZ3ObnyvvLUYLJvabdI6JpRhmj3I-gbXBp3uBxISttDt0o_uKjR9kTF3_reuHnScUd52woYxkWIwpga49p44bgs-yqwcPVIX');//        $data = $response->getData(true);//        dd($data);//        dd($atolService->documentRegistrationMy());//        dd($atolService->getDocument("c87a25c9-c253-43e7-bcfb-9e304c1b7529"));//        dd($atolService->getTokenMy()['token']);//        $data = [//            'customerCode' => '304590585',//            'amount' => '1234.00',//            'purpose' => 'Перевод за оказанные услуги',//            'redirectUrl' => 'https://example.com',//            'failRedirectUrl' => 'https://example.com/fail',//            'paymentMode' => ['card'],//            'saveCard' => true,//            'consumerId' => Str::uuid()->toString(),//        ];//        dd($tochkaService->getPaymentOperationList());//        $users = User::all();////        foreach ($users as $user) {//            $user->encrypted_first_name = Crypt::encryptString($user->first_name);//            $user->encrypted_email = Crypt::encryptString($user->email);//            $user->encrypted_card_number = Crypt::encryptString($user->card_number);//            $user->save();//        }        //$roles = [//            [//                'slug' => 'super_admin',//                'name' => 'Суперадминистратор',//            ],//            [//                'slug' => 'admin',//                'name' => 'Администратор',//            ],            //[                //'slug' => 'employee',                //'name' => 'Персонал',            //],        //];        //foreach ($roles as $role) {            //$existingRole = Role::where('slug', $role['slug'])->first();            //if ($existingRole) {                //$existingRole->update(['name' => $role['name']]);//                $this->command->info('Role "' . $existingRole->name . '" renamed to "' . $role['name'] . '".');            //} else {//                $this->command->error('Role with slug "' . $role['slug'] . '" not found.');            //}        //}        // Удаляем вебхук//        $this->info('Deleting webhook...');//        $url = "https://api.telegram.org/7542140758:AAFCKRTRUILeOcMBmxG_Xiz-FpwaftkzYWc/deleteWebhook";//        $response = Http::post($url);//        $this->info('Webhook deleted.');       $this->info('Getting updates...');       $url = "https://api.telegram.org/bot7542140758:AAFCKRTRUILeOcMBmxG_Xiz-FpwaftkzYWc/getUpdates";       $response = Http::post($url);       $this->info('Updates received:');       dd($response->json()); // Дисплей полученных обновлений//        $bot = new BotApi('7542140758:AAFCKRTRUILeOcMBmxG_Xiz-FpwaftkzYWc');//////        $bot->sendMessage(272393976, 'Вам поступили чаевые: 859р.//Итого за сегодня: 4 543р.//Мастер. Барбершоп Бритва//© yagoda - сделаем мир счастливее//');//        $users = User::all();////        foreach ($users as $user) {//            $user->photo_path = 'CUPgaYsw4TNSKLGRbANvafkRMXm0PmhI22WSGbZe.jpg';//            $user->save();//        }//        $vbr = new VbrrService();//        $vbr->testPayment();//        Setting::truncate();//        $activityTypes = [//            'Цветочный салон'//        ];////        foreach ($activityTypes as $type) {//            ActivityType::create(['name' => $type]);//        }//        DB::table('organization_user')->insert([//            'user_id' => 2,//            'organization_id' => 1,//            'role_id' => 2,//        ]);////        DB::table('organization_user')->insert([//            'user_id' => 2,//            'organization_id' => 1,//            'role_id' => 3,//        ]);//////        DB::table('organization_user')->insert([//            'user_id' => 2,//            'organization_id' => 1,//            'role_id' => 4,//        ]);        // Bill::create([        //     'name' => 'Главный счёт',        //     'number' => 123456789,        //     'bik' => 987654321,        //     'organization_id' => 1,        // ]);        // $users = User::all();        // foreach ($users as $key => $value) {        //     dump($value);        // }        // $call = new PlusofonService();        // $call->sendFlashCall('79269442435');//        $ya = new YclientsService();//        dd($ya);        // $user = User::find(1);        // dd($user);        // $role = Role::create(['name' => 'Master']);        // // Создание новых разрешений        // $createPermission = Permission::create(['name' => 'create']);        // $readPermission = Permission::create(['name' => 'read']);        // $updatePermission = Permission::create(['name' => 'update']);        // $deletePermission = Permission::create(['name' => 'delete']);        // // Привязка разрешений к роли        // $role->permissions()->attach([$createPermission->id, $readPermission->id, $updatePermission->id, $deletePermission->id]);        // // Проверка разрешений роли        // $role->permissions->each(function ($permission) {        //     echo "Permission: " . $permission->name;        // });        // $user->organizations()->attach(3, ['role_id' => 2]);    }    public function decodeJwtWithoutSignature($jwt)    {        // Разделите JWT на части        $parts = explode('.', $jwt);        // Убедитесь, что у нас есть 3 части        if (count($parts) !== 3) {            throw new \Exception('Некорректный JWT');        }        // Получите закодированную в base64 часть payload        $payload = $parts[1];        // Декодируйте его из base64url        $payloadData = json_decode($this->base64url_decode($payload), true);        return response()->json($payloadData);    }    private function base64url_decode($data)    {        // Заменяем символы base64url        $data = str_replace(['-', '_'], ['+', '/'], $data);        // Добавляем недостающие символы для соответствия формату base64        switch (strlen($data) % 4) {            case 2: $data .= '=='; break;            case 3: $data .= '='; break;        }        // Декодируем из base64        return base64_decode($data);    }}