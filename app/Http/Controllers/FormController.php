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

        $accessToken = 'longLiveToken';
        $apiClient = new AmoCRMApiClient();
        $longLivedAccessToken = new LongLivedAccessToken($accessToken);

        $apiClient->setAccessToken($longLivedAccessToken)
            ->setAccountBaseDomain('subdomain.amocrm.ru');

        return $apiClient;
    }

    /**
     * @throws ValidationException
     */
    public function send(Request $request): View
    {
        if (!$this->validate($request, [
            'sessionStartTime' => 'required|integer',
            'name' => 'required|string',
            'email' => 'required|email',
            'phoneNumber' => 'required|string',
            'price' => 'required|integer',
        ])) {
            return view('form.index', ['massage' => 'Введены не правильные данные']);
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

            return view('form.index', ['massage' => 'Сделка отправлена']);
        } catch (AmoCRMApiException $e) {

            return view('form.index', ['massage' => 'Сделка не отправлена']);
        }
    }

    private function isUserSessionTimeMoreThan30Seconds(int $unixTimestamp): bool
    {
        return (time() - $unixTimestamp) >= 30;
    }
}
