<?php

namespace Vgplay\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class FacebookLoginRequest
 *
 * @property int $game_id
 * @property int $redirect_uri
 *
 * @package App\Http\Requests
 */
class FacebookLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'game_id'      => 'required|numeric',
            'redirect_uri' => 'required|string'
        ];
    }
}
