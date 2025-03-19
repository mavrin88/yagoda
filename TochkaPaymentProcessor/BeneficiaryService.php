<?php

namespace App\Services\TochkaPaymentProcessor;

use App\Models\Organization;
use App\Services\TochkaService;
use Illuminate\Support\Facades\Log;

class BeneficiaryService
{
    private $tochkaService;

    public function __construct(TochkaService $tochkaService)
    {
        $this->tochkaService = $tochkaService;
    }

    public function checkBeneficiary(Organization $organization)
    {
        try {
            dump('Поиск бенефициара по ИНН: ' . $organization->inn);
            $beneficiary = $this->tochkaService->listBeneficiarySearchToINN($organization->inn);
        } catch (\Exception $e) {
            dd("Ошибка при поиске бенефициара: " . $e->getMessage());
            return null;
        }

        $beneficiaries = $beneficiary['body']['result']['beneficiaries'] ?? [];

        return empty($beneficiaries) ?
            $this->handleNewBeneficiary($organization) :
            $this->activateExistingBeneficiary($beneficiaries[0]);
    }

    private function handleNewBeneficiary(Organization $organization)
    {
        try {
            $beneficiaryId = $this->createBeneficiary($organization);
            if ($beneficiaryId !== null) {
                $this->createVirtualAccount($beneficiaryId);
                $documentId = $this->uploadDocumentBeneficiary($beneficiaryId);
                if ($this->checkDocumentUpload($documentId)) {
                    return $beneficiaryId;
                }
            }
            return null;
        } catch (\Exception $e) {
            Log::error("Ошибка при создании бенефициара: " . $e->getMessage());
            return null;
        }
    }

    private function createBeneficiary(Organization $organization)
    {
        Log::info('Создание нового бенефициара для организации: ' . $organization->name);
        try {
            switch ($organization->form_id) {
                case 1: // ЮЛ
                    $beneficiaryUl = $this->tochkaService->createBeneficiaryUl($organization);
                    break;
                case 2: // ИП
                case 3: // Самозанятый
                    $beneficiaryUl = $this->tochkaService->createBeneficiaryIP($organization);
                    break;
                default:
                    throw new \Exception('Неизвестный тип организации: ' . $organization->form_id);
            }

            $beneficiaryId = $beneficiaryUl['body']['result']['beneficiary']['id'] ?? null;
            if (!$beneficiaryId) {
                throw new \Exception('Создание бенефициара не вернуло идентификатор.');
            }

            Log::info('Бенефициар успешно создан с ID: ' . $beneficiaryId);
            return $beneficiaryId;
        } catch (\Exception $e) {
            Log::error('Ошибка при создании бенефициара: ' . $e->getMessage());
            return null;
        }
    }

    private function activateExistingBeneficiary(array $beneficiary)
    {
        try {
            Log::info('Активация существующего бенефициара с ID: ' . $beneficiary['id']);
            $this->activateBeneficiary($beneficiary['id']);
            return $beneficiary['id'];
        } catch (\Exception $e) {
            Log::error("Ошибка при активации бенефициара: " . $e->getMessage());
            return null;
        }
    }

    private function activateBeneficiary(string $beneficiaryId)
    {
        $response = $this->tochkaService->activateBeneficiary($beneficiaryId);
        if (isset($response['body']) && $response['body']) {
            // todo: сделать активацию бенефициара, выдает ошибку Beneficiary already activated
            dump('Бенефициар успешно активирован: ' . $beneficiaryId);
            return true;
        }
        throw new \Exception('Не удалось активировать бенефициара.');
    }

    public function getVirtualAccount(string $id)
    {
        $virtualAccount = $this->tochkaService->listVirtualAccount($id);
        if (empty($virtualAccount['body']['result']['virtual_accounts'])) {
            $response = $this->createVirtualAccount($id);
            return $response['body']['result']['virtual_account'];
        }
        return $virtualAccount['body']['result']['virtual_accounts'][0];
    }

    private function createVirtualAccount(string $beneficiaryId)
    {
        try {
            Log::info("Создание виртуального счета для бенефициара с ID: " . $beneficiaryId);
            $response = $this->tochkaService->createVirtualAccount($beneficiaryId);
            if (empty($response['body']['result']['virtual_account'])) {
                throw new \Exception('Создание виртуального счета не вернуло идентификатор.');
            }
            Log::info('Виртуальный счет успешно создан с ID: ' . $response['body']['result']['virtual_account']['id']);
            return $response;
        } catch (\Exception $e) {
            Log::error("Ошибка при создании виртуального счета: " . $e->getMessage());
            throw $e;
        }
    }

    private function uploadDocumentBeneficiary(string $beneficiaryId)
    {
        try {
            Log::info("Загрузка документа для бенефициара: " . $beneficiaryId);
            $response = $this->tochkaService->uploadDocumentBeneficiary($beneficiaryId);
            $documentId = $response['body']['document_id'] ?? null;
            if (!$documentId) {
                throw new \Exception('Создание документа не вернуло идентификатор.');
            }
            return $documentId;
        } catch (\Exception $e) {
            Log::error("Ошибка при загрузке документа: " . $e->getMessage());
            throw $e;
        }
    }

    private function checkDocumentUpload(string $documentId): bool
    {
        try {
            Log::info("Проверка загрузки документа с ID: " . $documentId);
            $response = $this->tochkaService->getDocument($documentId);
            return !empty($response['body']['result']['document']) && $response['body']['result']['document']['success_added'];
        } catch (\Exception $e) {
            Log::error("Ошибка при проверке загрузки документа: " . $e->getMessage());
            return false;
        }
    }
}
