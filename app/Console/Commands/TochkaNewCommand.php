<?php

namespace App\Console\Commands;

use App\Services\TochkaPaymentProcessor\PaymentProcessor;
use App\Services\TochkaPaymentProcessor\BeneficiaryService;
use App\Services\TochkaPaymentProcessor\DealService;
use App\Services\TochkaPaymentProcessor\DocumentService;
use App\Services\EncryptionCardNumber;
use App\Services\TelegramService;
use App\Services\TochkaApiService;
use App\Services\TochkaPaymentProcessor\YagodaTipsPayments\TipsDealService;
use App\Services\TochkaService;
use App\Services\TochkaService\CommissionCalculator;
use Illuminate\Console\Command;

class TochkaNewCommand extends Command
{
    protected $signature = 'tochka:new';
    protected $description = 'Запуск обработки платежей и сделок через Tochka API';

    private $paymentProcessor;
    private $beneficiaryService;
    private $dealService;
    private $documentService;

    public function __construct(
        PaymentProcessor $paymentProcessor,
        BeneficiaryService $beneficiaryService,
        DealService $dealService,
        TipsDealService $tipsDealService,
        DocumentService $documentService,
        TochkaService $tochkaService,
        EncryptionCardNumber $encryptionCardNumber,
        TochkaApiService $tochkaApiService,
        CommissionCalculator $commissionCalculator,
        TelegramService $telegram
    ) {
        parent::__construct();
        $this->paymentProcessor = $paymentProcessor;
        $this->beneficiaryService = $beneficiaryService;
        $this->dealService = $dealService;
        $this->tipsDealService = $tipsDealService;
        $this->documentService = $documentService;

        // Привязка метода uploadDocuments через closure
        $this->dealService->uploadDocuments = function(string $dealId, ?float $tips, string $beneficiaryId, array $owner) {
//            $this->documentService->uploadDocumentDeal(
//                $beneficiaryId,
//                $dealId,
//                $this->documentService->getCommissionDocument($owner),
//                $owner['agencyAgreementDate'],
//                $owner['agencyAgreementNumber']
//            );
//            $this->documentService->uploadDocumentDeal(
//                $beneficiaryId,
//                $dealId,
//                $this->documentService->getPaymentContractDocument($owner),
//                $owner['agencyAgreementDate'],
//                $owner['agencyAgreementNumber']
//            );
            if ($tips > 0) {
                $this->documentService->uploadDocumentDeal(
                    $beneficiaryId,
                    $dealId,
                    $this->documentService->getPaymentContractToCardDocument($owner),
                    $owner['agencyAgreementDate'],
                    $owner['agencyAgreementNumber']
                );
            }
        };
    }

    public function handle()
    {
        $this->info('Запуск команды tochka:run');

        // Обработка неидентифицированных платежей
        $this->paymentProcessor->processForUnidentifiedPayments();
//        $this->paymentProcessor->processPaymentsDetailsTest(156, 'sbp');

//        $this->paymentProcessor->processPaymentTest = true;

//        dd($this->paymentProcessor->prepareIidentifyPayments);

        if (isset($this->paymentProcessor->prepareIidentifyPayments['orderStatisticsTips'])) {
            $this->tipsDealService->createDeal($this->paymentProcessor->prepareIidentifyPayments);
        }else{
            // Создание сделок на основе обработанных платежей
//            $this->dealService->createDeal($this->paymentProcessor->prepareIidentifyPayments);
        }

        $this->info('Команда tochka:run успешно выполнена');
    }
}
