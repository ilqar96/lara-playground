<?php

namespace App\Http\Requests;

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
            $addresses = $this->input('addresses');
            for ($i = 1; $i < count($addresses); $i++) {
                if ($this->isSameAddress($addresses[$i - 1], $addresses[$i])) {
                    $validator->errors()->add('addresses', 'Consecutive addresses cannot be the same.');
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


}
