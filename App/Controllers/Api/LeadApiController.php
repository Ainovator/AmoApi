<?php

namespace App\Controllers\Api;

use App\Controllers\Services\AmoCRMService;

class LeadApiController
{
    private $amoCRMService;

    public function __construct()
    {
        $config = require __DIR__ . '/../../../config/amocrm.php';
        $this->amoCRMService = new AmoCRMService($config['access_token'], $config['subdomain']);
    }

    public function getAccountInfo()
    {
        try {
            $accountInfo = $this->amoCRMService->getAccountInfo();

            // Проверка структуры ответа
            if (!isset($accountInfo['name'], $accountInfo['subdomain'], $accountInfo['created_at'], $accountInfo['country'], $accountInfo['currency'], $accountInfo['currency_symbol'])) {
                throw new \Exception('Некорректная структура ответа от AmoCRM');
            }

            echo 'Account Name: ' . $accountInfo['name'] . '<br>';
            echo 'Subdomain: ' . $accountInfo['subdomain'] . '<br>';
            echo 'Created At: ' . date('Y-m-d H:i:s', $accountInfo['created_at']) . '<br>';
            echo 'Country: ' . $accountInfo['country'] . '<br>';
            echo 'Currency: ' . $accountInfo['currency'] . ' (' . $accountInfo['currency_symbol'] . ')<br>';
        } catch (\Exception $e) {
            echo 'Ошибка: ' . $e->getMessage();
        }
    }

    public function createDeal()
    {
        $lead_first_name = $_POST['lead_first_name'] ?? '';
        $lead_second_name = $_POST['lead_second_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $cost = (int) ($_POST['cost'] ?? 0);

        // Проверка обязательных данных
        if (empty($lead_first_name)) {
            throw new \Exception('Имя лидера обязательно для заполнения.');
        }

        if (empty($email)) {
            throw new \Exception('Email обязателен для заполнения.');
        }

        if (empty($phone)) {
            throw new \Exception('Телефон обязателен для заполнения.');
        }

        $dealData = $this->prepareDealData($cost);
        $contactData = $this->prepareContactData($lead_first_name, $lead_second_name, $email, $phone);

        try {
            $deal = $this->amoCRMService->createDeal($dealData);

            if (!isset($deal['_embedded']['leads'][0]['id'])) {
                throw new \Exception('Не удалось создать сделку: ID не найден.');
            }

            $dealId = $deal['_embedded']['leads'][0]['id'];
            echo "Сделка успешно создана. ID сделки: " . $dealId . PHP_EOL;
        } catch (\Exception $e) {
            echo 'Ошибка при создании сделки: ' . $e->getMessage();
            return;
        }

        try {
            $contact = $this->amoCRMService->createContact($contactData);

            if (!isset($contact['_embedded']['contacts'][0]['id'])) {
                throw new \Exception('Не удалось создать контакт: ID не найден.');
            }

            $contactId = $contact['_embedded']['contacts'][0]['id'];
            echo "Контакт успешно создан. ID контакта: " . $contactId . PHP_EOL;

            $this->amoCRMService->linkContactToDeal($dealId, $contactId);
            echo "Контакт успешно привязан к сделке." . PHP_EOL;
        } catch (\Exception $e) {
            echo 'Ошибка при создании контакта или привязке: ' . $e->getMessage();
        }
    }

    public function updateTimeOnSiteFlag(): void
    {
        try {
            // Получение данных из запроса
            $input = json_decode(file_get_contents('php://input'), true);

            $leadId = $input['lead_id'] ?? null;
            $userStayed = $input['user_stayed'] ?? false;

            if (!$leadId) {
                throw new \Exception('ID сделки обязателен.');
            }

            $customFieldId = 266937; // ID вашего кастомного поля

            // Обновляем кастомное поле в сделке
            $this->amoCRMService->updateDealCustomField((int)$leadId, $customFieldId, (bool)$userStayed);

            echo json_encode(['status' => 'success', 'message' => 'Флаг времени обновлён.']);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    private function prepareDealData($cost)
    {
        return [
            [
                'price' => $cost,

            ]
        ];
    }

    private function prepareContactData($firstName, $lastName, $email, $phone)
    {
        return [
            [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'custom_fields_values' => [
                    [
                        'field_code' => 'PHONE',
                        'values' => [
                            [
                                'value' => $phone,
                                'enum_code' => 'WORK'
                            ]
                        ]
                    ],
                    [
                        'field_code' => 'EMAIL',
                        'values' => [
                            [
                                'value' => $email,
                                'enum_code' => 'WORK'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

}


