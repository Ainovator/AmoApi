<?php

namespace App\Controllers\Services;

class AmoCRMService
{
    private $access_token;
    private $subdomain;

    public function __construct($access_token, $subdomain)
    {
        $this->access_token = $access_token;
        $this->subdomain = $subdomain;
    }

    /**
     * Выполняет запрос к API AmoCRM
     *
     * @param string $url URL для запроса
     * @param string $method Метод HTTP (GET, POST, PUT, DELETE)
     * @param array $data Данные для POST или PUT запроса
     * @return mixed Ответ от API
     * @throws \Exception
     */
    // Метод makeRequest в сервисе AmoCRMService
    public function makeRequest($url, $method = 'GET', $data = [])
    {
        $link = 'https://' . $this->subdomain . '.amocrm.ru/api/v4/' . $url;
        $headers = [
            'Authorization: Bearer ' . $this->access_token,
            'Content-Type: application/json',
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-oAuth-client/1.0');
        curl_setopt($curl, CURLOPT_URL, $link);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);

        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $out = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $response = json_decode($out, true);

        if ($code < 200 || $code > 204) {
            // Логируем HTTP-код и полный ответ
            error_log('HTTP Code: ' . $code);
            error_log('Response Body: ' . $out);

            // Извлекаем подробности ошибки
            $errorDetails = $response['validation-errors'] ?? ($response['detail'] ?? 'No details available');
            if (is_array($errorDetails)) {
                $errorDetails = json_encode($errorDetails, JSON_PRETTY_PRINT);
            }

            throw new \Exception("Ошибка API: $errorDetails", $code);
        }

        return $response;
    }


    public function getAccountInfo()
    {
        // Получаем информацию о аккаунте через метод GET
        return $this->makeRequest('account');
    }

    public function createDeal($dealData)
    {
        // Для создания сделки используем метод POST
        return $this->makeRequest('leads', 'POST', $dealData);
    }

    public function createContact($contactData)
    {
        return $this->makeRequest('contacts', 'POST', $contactData);
    }

    public function linkContactToDeal($dealId, $contactId)
    {
        $linkData = [
            [
                "to_entity_id" => $contactId,
                "to_entity_type" => "contacts"
            ]
        ];

        return $this->makeRequest("leads/$dealId/link", 'POST', $linkData);
    }

    public function updateDealCustomField(int $leadId, int $fieldId, bool $value): array
    {
        $data = [
            'custom_fields_values' => [
                [
                    'field_id' => $fieldId,
                    'values' => [
                        ['value' => $value]
                    ]
                ]
            ]
        ];

        return $this->makeRequest("leads/{$leadId}", 'PATCH', $data);
    }



    /**
     * Удаление сделки
     *
     * @param int $dealId ID сделки
     * @return array Результат удаления
     * @throws \Exception
     */
    public function deleteDeal($dealId)
    {
        return $this->makeRequest('leads/' . $dealId, 'DELETE');
    }

    /**
     * Пример другого метода для работы с API
     *
     * @param array $data Данные
     * @return array Результат
     * @throws \Exception
     */
    public function updateDeal($dealId, $data)
    {
        return $this->makeRequest('leads/' . $dealId, 'PUT', $data);
    }
}
