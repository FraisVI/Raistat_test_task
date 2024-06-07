<?php

namespace App\Http\Controllers;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\EntitiesServices\Leads;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
use AmoCRM\Models\LeadModel;
use League\OAuth2\Client\Token\AccessTokenInterface;


class FormController extends Controller
{
    public function index()
    {
        return view('form.index');
    }


    private function authorizationAmoCrm()
    {

        $subdomain = 'test'; //Поддомен нужного аккаунта
        $link = 'https://' . $subdomain . '.amocrm.ru/oauth2/access_token'; //Формируем URL для запроса

        /** Соберем данные для запроса */
        $data = [
            "client_id" => "f7cced00-68f8-4288-9e07-6268922ae306",
            "client_secret" => "h48816gHZj9G77qVw6SwPDMPyf96BOOigOapeWHreuQMsLocLaVvxSwcyk4fgEni",
            "grant_type" => "authorization_code",
            "code" => "'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImI3M2IxMzIxMTAwNDc2ZjRlZGIyNTkyNGUwOTc5MWNmYmU3MTNkNjIyNTBmYzk5MDA0MDViYzUyMTQ5NGM2MGZhZjg2YjVjOWVjOWQ0NDgyIn0.eyJhdWQiOiJmN2NjZWQwMC02OGY4LTQyODgtOWUwNy02MjY4OTIyYWUzMDYiLCJqdGkiOiJiNzNiMTMyMTEwMDQ3NmY0ZWRiMjU5MjRlMDk3OTFjZmJlNzEzZDYyMjUwZmM5OTAwNDA1YmM1MjE0OTRjNjBmYWY4NmI1YzllYzlkNDQ4MiIsImlhdCI6MTcxNzY1OTc5NSwibmJmIjoxNzE3NjU5Nzk1LCJleHAiOjE3MTk3MDU2MDAsInN1YiI6IjExMTI4MDE4IiwiZ3JhbnRfdHlwZSI6IiIsImFjY291bnRfaWQiOjMxNzg2MzgyLCJiYXNlX2RvbWFpbiI6ImFtb2NybS5ydSIsInZlcnNpb24iOjIsInNjb3BlcyI6WyJjcm0iLCJmaWxlcyIsImZpbGVzX2RlbGV0ZSIsIm5vdGlmaWNhdGlvbnMiLCJwdXNoX25vdGlmaWNhdGlvbnMiXSwiaGFzaF91dWlkIjoiMTEyMjAwZjUtOTllOC00ZDNlLTk1YjEtMTc3MjQ0MDlmMDQ2In0.pR7uOpKh-P5IMBVcVDmyNU-N3P0boA4AbWxzH7308I6dh0ivV4g7oaHMbgdLaBI341RdSXtS9JfVK-1nyvgAsXzDH0NiAA84v8JfLOV_W90yv59bzNROMHFr8hqiOLpXwFykgf7loZ9StpFjG6X8SRGQfrw4VMp9GMwgMiyICbY_gZv7qi7CgRUF2ZtaRv2Ye113ui-5X61iywvXVlxl8vpreFLfl6wd5osVL-71HCjmT1X-7IbXKG3NaWXkpdCz6sK3SyBmIPBaoKlfIrnmYLvraBkYGcwgSgkitJ2nqidSpun6KkCiqRkhcB2RRF58P2T5kT8X3Wjr5CuustD2jw'",
            "redirect_uri" => ""
        ];

        /**
         * Нам необходимо инициировать запрос к серверу.
         * Воспользуемся библиотекой cURL (поставляется в составе PHP).
         * Вы также можете использовать и кроссплатформенную программу cURL, если вы не программируете на PHP.
         */
        $curl = curl_init(); //Сохраняем дескриптор сеанса cURL
        /** Устанавливаем необходимые опции для сеанса cURL  */
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-oAuth-client/1.0');
        curl_setopt($curl, CURLOPT_URL, $link);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        $out = curl_exec($curl); //Инициируем запрос к API и сохраняем ответ в переменную
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        /** Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
        $code = (int)$code;
        $errors = [
            400 => 'Bad request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not found',
            500 => 'Internal server error',
            502 => 'Bad gateway',
            503 => 'Service unavailable',
        ];

        try {
            /** Если код ответа не успешный - возвращаем сообщение об ошибке  */
            if ($code < 200 || $code > 204) {
                throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undefined error', $code);
            }
        } catch (\Exception $e) {
            die('Ошибка: ' . $e->getMessage() . PHP_EOL . 'Код ошибки: ' . $e->getCode());
        }

        /**
         * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
         * нам придётся перевести ответ в формат, понятный PHP
         */
        $response = json_decode($out, true);

        $access_token = $response['access_token']; //Access токен
        $refresh_token = $response['refresh_token']; //Refresh токен
        $token_type = $response['token_type']; //Тип токена
        $expires_in = $response['expires_in']; //Через сколько действие токена истекает

        echo '<pre>';
        print_r($token_type);

    }

    public function store()
    {


        /*        {
                    "client_id": "xxxx",
          "client_secret": "xxxx",
          "grant_type": "authorization_code",
          "code": "xxxxxxx",
          "redirect_uri": "https://test.test"
        }*/

        /*        $data = \request([
                    'name' => 'name',
                    'email' => 'email',
                    'phone_number' => 'phone_number',
                    'price' => 'price',
                ]);
                $data['custom_fields_values'] = [
                    'field_id' => 397259,
                    'values' => ['value' => 'true']
                ];

                $lead = new LeadModel();
                $lead->setName('Сделка N')
                    ->setPrice(450);

                $leadsService = new AmoCRMApiClient();

                $leadsCollection = new LeadsCollection();
                $leadsCollection->add($lead);

                try {
                    $leadsCollection = $leadsService->add($leadsCollection);
                } catch (AmoCRMApiException $e) {
                    printError($e);
                    die;
                }*/
        $this->authorizationAmoCrm();
    }


}
