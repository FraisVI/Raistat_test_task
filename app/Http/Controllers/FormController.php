<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Client\LongLivedAccessToken;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\CustomFieldsValues\CheckboxCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\CheckboxCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\CheckboxCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
use AmoCRM\Models\LeadModel;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FormController extends Controller
{
    public function index(): View
    {
        return view('form.index');
    }

    private function authorizationAmoCrm(): AmoCRMApiClient
    {

        $accessToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjQ1NWQxNDkwN2JkMWMzYjJiMDkzMWMwMDk4MzAwODcyOTI0ZDQ0MTMxYTA1ZWM2NDc1Nzg2NWM2MjhjZjcxYzcyYjU5OTJjODQ5NmU1MTE0In0.eyJhdWQiOiJmN2NjZWQwMC02OGY4LTQyODgtOWUwNy02MjY4OTIyYWUzMDYiLCJqdGkiOiI0NTVkMTQ5MDdiZDFjM2IyYjA5MzFjMDA5ODMwMDg3MjkyNGQ0NDEzMWEwNWVjNjQ3NTc4NjVjNjI4Y2Y3MWM3MmI1OTkyYzg0OTZlNTExNCIsImlhdCI6MTcxNzc3NDU0MSwibmJmIjoxNzE3Nzc0NTQxLCJleHAiOjE3MTk3OTIwMDAsInN1YiI6IjExMTI4MDE4IiwiZ3JhbnRfdHlwZSI6IiIsImFjY291bnRfaWQiOjMxNzg2MzgyLCJiYXNlX2RvbWFpbiI6ImFtb2NybS5ydSIsInZlcnNpb24iOjIsInNjb3BlcyI6WyJjcm0iLCJmaWxlcyIsImZpbGVzX2RlbGV0ZSIsIm5vdGlmaWNhdGlvbnMiLCJwdXNoX25vdGlmaWNhdGlvbnMiXSwiaGFzaF91dWlkIjoiY2NlNThmYWItMDVlZi00Zjg3LWEzY2UtMGMzZGNhYTllYWRjIn0.FG35glMF2YqZCFKHt7Ogp2JJYpESC2b3k8mmx1yhlqpWSYsCbLAQCuq5FWoirNEapyT2fENejyBJkkf3d7qdtm_Vpi6pNEofZ1NicnutC1xdm_RXFfV4FXRxrYNqXMkh8MYYTKyH1F6j7DfXSLY6eZdROpxn2f3n7mAcfyov6X0seeSGx7w1X8-Wum2tR94MHTvNBLvgfPzVOfU_Y3DdV4NBqPOYH8x39rLKdfL8OQsZJjGgWLMBj5V8YYN6Xm2Bgxjgj2py8RG7vt9f0isKYgg4RoQ6p4Ih_yZ2V94NO6U__IwC2pKODXFAMtHYlo7OiD-9r1wb3W4c359tkTamXA';
        $apiClient = new AmoCRMApiClient();
        $longLivedAccessToken = new LongLivedAccessToken($accessToken);

        $apiClient->setAccessToken($longLivedAccessToken)
            ->setAccountBaseDomain('fraisvii.amocrm.ru');

        return $apiClient;
    }

    /**
     * @throws ValidationException
     */
    public function send(Request $request)
    {
        if (!$this->validate($request, [
            'sessionStartTime' => 'required|integer',
            'name' => 'required|string',
            'email' => 'required|email',
            'phoneNumber' => 'required|string',
            'price' => 'required|integer',
        ])) {
            dump( 'ошибка в данных!');
        }

        $userSessionTime = (int)$request->get('sessionStartTime');

        $name = $request->get('name');
        $email = $request->get('email');
        $phoneNumber = $request->get('phoneNumber');
        $price = (int)$request->get('price');

        $apiClient = $this->authorizationAmoCrm();

        $lead = new LeadModel();
        $lead->setName('Сделка N')
            ->setPrice($price)
            ->setContacts(
                (new ContactsCollection())
                    ->add(
                        (new ContactModel())
                            ->setFirstName($name)
                            ->setCustomFieldsValues(
                                (new CustomFieldsValuesCollection())
                                    ->add(
                                        (new MultitextCustomFieldValuesModel())
                                            ->setFieldCode('PHONE')
                                            ->setValues(
                                                (new MultitextCustomFieldValueCollection())
                                                    ->add(
                                                        (new MultitextCustomFieldValueModel())
                                                            ->setValue($phoneNumber)
                                                    )
                                            )
                                    )
                                    ->add(
                                        (new MultitextCustomFieldValuesModel())
                                            ->setFieldCode('EMAIL')
                                            ->setValues(
                                                (new MultitextCustomFieldValueCollection())
                                                    ->add(
                                                        (new MultitextCustomFieldValueModel())
                                                            ->setValue($email)
                                                    )
                                            )
                                    )
                            )
                    )
            )
            ->setCustomFieldsValues(
                (new CustomFieldsValuesCollection())
                    ->add(
                        (new CheckboxCustomFieldValuesModel())
                            ->setFieldId(397259)
                            ->setValues(
                                (new CheckboxCustomFieldValueCollection())
                                    ->add(
                                        (new CheckboxCustomFieldValueModel())
                                            ->setValue($this->isUserSessionTimeMoreThan30Seconds($userSessionTime))
                                    )
                            )
                    )
            );

        $leadsCollection = new LeadsCollection();

        $leadsCollection->add($lead);

        try {
            $apiClient->leads()->addComplex($leadsCollection);

            return view('form.index', ['massage' => 1]);
        } catch (AmoCRMApiException $e) {

            return view('form.index', ['massage' => 0]);
        }
    }

    private function isUserSessionTimeMoreThan30Seconds(int $unixTimestamp): bool
    {
        return (time() - $unixTimestamp) >= 30;
    }
}
