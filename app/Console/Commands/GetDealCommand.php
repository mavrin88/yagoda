<?php

namespace App\Console\Commands;

use App\Models\TochkaDeal;
use App\Services\TochkaService;
use App\Services\TochkaService\DealHandler;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GetDealCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:deal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(TochkaService $tochkaService)
    {
        $this->tochkaService = $tochkaService;
        // return $this->createTestDeal();
        // $deals = $this->getAllDeals();
        // $this->processDeals($deals);
        // dd($this->tochkaService->listBeneficiarySearchToINN('0800019730'));
        // dd($this->tochkaService->uploadDocumentBeneficiary('37d3665b-a39c-49dc-a7a3-3d5d452d37cd'));

        // $this->dopMethod();
        // $this->identificationPayment();
        // $this->updateDeal();
        // $this->getallInfoVirtualAccounts();
        // $this->createDeal(1787.58);
    }

    protected function getAllDeals()
    {
        return TochkaDeal::where('id', 27)->get();
    }

    protected function processDeals($deals)
    {
        foreach ($deals as $deal) {
            $this->handleDeal($deal);
        }
    }

    protected function handleDeal($deal)
    {
        try {
            $dealInfo = $this->tochkaService->getDeal($deal->uuid);
            $this->dumpDealInfo($dealInfo);
        } catch (\Exception $e) {
            $this->handleError($deal, $e);
        }
    }

    protected function handleError($deal, \Exception $e)
    {
        \Log::error('Ошибка при получении информации о сделке', [
            'deal_uuid' => $deal->uuid,
            'message' => $e->getMessage(),
        ]);

        echo "Ошибка при обработке сделки с UUID {$deal->uuid}: {$e->getMessage()}\n";
    }

    protected function dumpDealInfo($dealInfo)
    {
        $recipients = $dealInfo["body"]["result"]["deal"]["recipients"];

        foreach ($recipients as $key => $recipient) {
            dump($recipient['status'] . ' - ' . $recipient['type'] . ' - ' . $recipient['amount']);
        }
    }

    public function createTestDeal()
    {

    }


    private function dopMethod()
    {

        // $data = [
        //     "payment_id" => 'cbs-tb-92-2104004627',
        // ];

        // $data['owners'][] = [
        //     'virtual_account' => '5f0213d8-25a8-439f-8331-2b2deeef670f',
        //     'amount' => 2482.75,
        // ];

        // dd($this->tochkaService->identificationPayment($data));




        // dd($this->tochkaService->listBeneficiarySearchToINN('421212485105'));
        // dd($this->tochkaService->listVirtualAccount('0b844aeb-ccfc-49cb-aa59-0a4af66c1fbb'));
        // dd($this->tochkaService->uploadDocumentBeneficiary('293c0136-1df9-4e0f-be2f-6692ec10b29f'));

        // 501209737017
        // Отмена сделки
        // dd($this->tochkaService->rejectedDeal("4a316881-6918-431f-b4c3-5b87a4326ec7"));

        // Список сделок
        dd($this->tochkaService->listDeals('correction'));

        // Информация о виртуальном аккаунте
        // dd($this->tochkaService->getVirtualAccount('9d9c7c4b-b047-4353-8aff-f25f5c7e5500'));

        // Получение информации о сделке

        dd($this->tochkaService->getDeal('5ec79d0d-ca0d-4268-bb51-dc7fb96fc953'));
    }

    private function identificationPayment()
    {
        $identifyPayment = [
            "owners" => [
                [
                    "amount" => 993.1,
                    "virtual_account" => "1ade8509-e500-4850-aa90-3616b8f26ddc"
                ]
            ],
            "payment_id" => "cbs-tb-92-2232950884"
        ];

        $response = $this->tochkaService->identificationPayment($identifyPayment);

        dd($response);
    }

    private function getallInfoVirtualAccounts()
    {
        $listBeneficiaryUl = $this->tochkaService->listBeneficiaryUl();

        foreach ($listBeneficiaryUl['body']['result']['beneficiaries'] as $key => $value) {
            $listVirtualAccount = $this->tochkaService->listVirtualAccount($value['id']);
            dump($this->tochkaService->getVirtualAccount($listVirtualAccount['body']['result']['virtual_accounts'][0]));
        }
    }

    public function createDeal($amount)
    {
        $prepareDealData = [
            'ext_key' => Str::uuid()->toString(),
            'amount' => $amount,
            'payers' => [
                [
                    'virtual_account' => '1ade8509-e500-4850-aa90-3616b8f26ddc',
                    'amount' => $amount,
                ]
            ],
            'recipients' => [
                // [
                //         "number" => 1,
                //         "type" => "commission",
                //         "amount" => 0.8
                // ],
                // [
                //     "number" => 2,
                //     "type" => "payment_contract",
                //     "amount" => 0.98,
                //     "account" => "40802810702140000422",
                //     "bank_code" => "044525593",
                //     "name" => "Индивидуальный предприниматель Язев Игорь Викторович",
                //     "inn" => "773005434924"
                // ],
                [
                    "number" => 3,
                    "type" => "payment_contract",
                    "amount" => 1787.58,
                    "account" => "40817810200030798989",
                    "bank_code" => "044525974",
                    "name" => "Давыдова Кристина Алексеевна",
                    "inn" => "421212485105"
                ],
                // [
                //         "number" => 3,
                //         'type' => 'payment_contract_to_card',
                //         "amount" => 4,
                //         "card_number_crypto_base64" => "rK91XvHkEnP9t0gpnRbeFxkrYdNCwcXDwHjWZYljtfAPojyEE9j9x7DiMoP23/Sq4sCa1Yc8BAaMiRLymv55wWXQ/y8kEAjJ3i0pTDw4c9kNMpxwkxb0TtxwaW6X7ahZVJ2WvPry61IQp9svBkk/LxLwIqJrD4+xpCA84qrq9bJHJ8srQVH5omb27fySWMeSRtEmFD2tKDGGgdvF0lavW3sYGihIq+Mg4dCq3gBzb9jlVNVjBQM/QgUJHYLtpOu1WvLyGP4UFUD8kcO/I5Def2t/bKQdH8DnClkiHJfoXVvIX8t7Xcc/TU7+ZqyEdH0O+KZ1zuHl1GmKuCfds2oBASpsTM90tJSfDq2TV7D1iH7GFQla0nt30t3mY7p2hid/A4Nsits9oBhw14XZ8cwFavqrwYstrsy4IjZ1EV4+ToMOZYSceG1HTGtNdvauFc2mZ4QF28PFG05eU7fGg6kgAXC3dQ0/eGR9DAM+ymkYgyMUH0h2syb2Sj40Bcz2c7Fmjir1HcKjb7xaJkd3EiyAyeKIWqOrl6JH/kTBPrklgo90ITcYKJXnBX3XcY2XWLTpbht5gBlg9tu30Wl2GyVwts6hz+xQaRvgqLjOxxqfg8uJTfujDZdS2cm3R73cpNyAaKfe3lPMbFvC3c91/ZXcrZicEzQ2aUG04PSH22F2xOE="
                // ],
            ],
        ];

        // бенефициар - 293c0136-1df9-4e0f-be2f-6692ec10b29f
        // virtual account - 5f0213d8-25a8-439f-8331-2b2deeef670f

        //           0 => array:6 [
        //     "id" => "293c0136-1df9-4e0f-be2f-6692ec10b29f"
        //     "inn" => "772791944221"
        //     "is_active" => true
        //     "legal_type" => "I"
        //     "nominal_account_bic" => "044525104"
        //     "nominal_account_code" => "40702810020000169454"
        //   ]
        // ]


        $deal = $this->tochkaService->createDeal($prepareDealData);

        if (isset($deal['body']['error']['code'])) {
            dd($deal);
        }

        // $dealId = "d2a81a26-3182-43d8-b813-76b956df424f";
        $dealId = $deal['body']['result']['deal_id'];

        $this->tochkaService->uploadDocumentDeal('8d9ee009-b855-4d41-bf0f-e93b08031607', $dealId, 'payment_contract_2025-01-28_853.pdf', '2025-01-25', '1');

        dd($this->tochkaService->executeDeal($dealId));

    }

    /**
     * Обновление сделки
     *
     * @param $arrayData
     * @return array
     * @throws \Exception
     */

    private function updateDeal()
    {
        $arrayData = [
            "deal_id" => "5ec79d0d-ca0d-4268-bb51-dc7fb96fc953",
            "deal_data" => [
                "amount" => 11.0,
                "payers" => [
                    [
                        "virtual_account" => "62decc01-1593-4bbe-9798-bc0156c76d7a",
                        "amount" => 11.0
                    ]
                ],
                "recipients" => [
                    [
                        "number" => 1,
                        'type' => 'commission',
                        "amount" => 1.0,
                    ],
                    // [
                    //     "number" => 2,
                    //     "type" => "payment_contract",
                    //     "amount" => 993.1,
                    //     "account" => "40817810200030798989",
                    //     "bank_code" => "044525974",
                    //     "name" => "ИП Давыдова Кристина Алексеевна",
                    //     "inn" => "421212485105"
                    // ],
                    [
                        "number" => 10,
                        'type' => 'payment_contract_to_card',
                        "amount" => 10.0,
                        "card_number_crypto_base64" => "iQFlUZpUiGPwyxW6K0OoKL1+VdA08MXDpnWuxOTVTndeth1MnjjaCtovBhj/OhaqRdamnTrGEOtsB2Cf1b1ZPneimde8/Ik1XGvOWs6j7PIF3BmAVopSaXH6iVP44uPtltlucg789HuV1ae+4P+oHGSoT6i293EqLXVXxQ61WqcjVMjQtohJZ1oHAleJ9uDIw9YbJyfIQC9enSVJDk5vYHG5G64mUyAvXR95ZzuA8A0k4lvunQWLRVV4rI1RZZLNbJnnCtcHCwB3e89xUSYZtq429HqiY4bdL3wzQsfwkoTrABD28a7xJmwuiF00dEItgmW6yRCzWKCnUsVuxJWt5spoKAGrT0/r5+LD8zMWBrtcWV/nnvxhwMhfVmqz0v6hSVtxDffUca15VjMgsoKcw0ZTktotD5ZOFYqjaFXwIWDSiuuAmU1qBd/+9pfUFFJw9fbjM2aaTNlq+WQtktDuIrEXXQNb+W+fLE/imy0WTc8CA9iMLb0P/PSEQ6FmT8sQ7MyXhUu1ScpJiwdBHKvIUHknXHFD9msFsjzBJ7MFzPgwOP7qs0lt3aB65rlF2zcv8GRkepMiXdVUdUKvMrvUzf/tt0I5P//21Zj9FKuBWjuDFWxx13TZiQwxxV6sjeGsK86SE1WxBCIaZ/n2I4ex+G+c3TFdnRrSWtOQw8kyrO0="
                    ],
                    // [
                    //     "number" => 14,
                    //     'type' => 'payment_contract_to_card',
                    //     "amount" => 22.5,
                    //     "card_number_crypto_base64" => "iQFlUZpUiGPwyxW6K0OoKL1+VdA08MXDpnWuxOTVTndeth1MnjjaCtovBhj/OhaqRdamnTrGEOtsB2Cf1b1ZPneimde8/Ik1XGvOWs6j7PIF3BmAVopSaXH6iVP44uPtltlucg789HuV1ae+4P+oHGSoT6i293EqLXVXxQ61WqcjVMjQtohJZ1oHAleJ9uDIw9YbJyfIQC9enSVJDk5vYHG5G64mUyAvXR95ZzuA8A0k4lvunQWLRVV4rI1RZZLNbJnnCtcHCwB3e89xUSYZtq429HqiY4bdL3wzQsfwkoTrABD28a7xJmwuiF00dEItgmW6yRCzWKCnUsVuxJWt5spoKAGrT0/r5+LD8zMWBrtcWV/nnvxhwMhfVmqz0v6hSVtxDffUca15VjMgsoKcw0ZTktotD5ZOFYqjaFXwIWDSiuuAmU1qBd/+9pfUFFJw9fbjM2aaTNlq+WQtktDuIrEXXQNb+W+fLE/imy0WTc8CA9iMLb0P/PSEQ6FmT8sQ7MyXhUu1ScpJiwdBHKvIUHknXHFD9msFsjzBJ7MFzPgwOP7qs0lt3aB65rlF2zcv8GRkepMiXdVUdUKvMrvUzf/tt0I5P//21Zj9FKuBWjuDFWxx13TZiQwxxV6sjeGsK86SE1WxBCIaZ/n2I4ex+G+c3TFdnRrSWtOQw8kyrO0="
                    // ],
                ]
            ]
        ];

        // $deal = $this->tochkaService->updateDeal($arrayData);

        // dd($deal);

        // $deal_id = '5ec79d0d-ca0d-4268-bb51-dc7fb96fc953';

        // $this->tochkaService->executeDeal($deal_id);
    }
}
