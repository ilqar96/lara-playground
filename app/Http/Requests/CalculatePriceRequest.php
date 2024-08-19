<?php

namespace App\Http\Requests;

use App\Models\City;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class CalculatePriceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'addresses'           => 'required|array|min:2',
            'addresses.*.country' => 'required|string|size:2',
            'addresses.*.zip'     => 'required|string',
            'addresses.*.city'    => 'required|string',
        ];
    }


    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $addresses = (array)$this->input('addresses');

            //check if address not same
            for ($i = 1; $i < count($addresses); $i++) {
                if ($this->isSameAddress($addresses[$i - 1], $addresses[$i])) {
                    $validator->errors()->add('addresses', 'Consecutive addresses cannot be the same.');
                }
            }

            //validate cities
            foreach ($addresses as $address){
                if (!$this->addressExists($address)){
                    $city = $address['city'];
                    $validator->errors()->add('cities', "$city address are not found in the database.");
                }
            }

        });
    }

    private function isSameAddress($address1, $address2)
    {
        return $address1['country'] === $address2['country'] &&
            $address1['zip'] === $address2['zip'] &&
            $address1['city'] === $address2['city'];
    }


    /**
     * Check if the given address exists in the cities collection.
     *
     * @param array $address
     * @return bool
     */
    public function addressExists(array $address): bool
    {
        return City::where('country', $address['country'])
            ->where('zipCode', $address['zip'])
            ->where('name', $address['city'])
            ->exists();
    }

}
