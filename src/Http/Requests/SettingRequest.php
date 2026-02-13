<?php
/**
 * JUZAWEB CMS - Laravel CMS for Your Project
 *
 * @package    juzaweb/cms
 * @author     The Anh Dang
 * @link       https://cms.juzaweb.com
 */

namespace Juzaweb\Modules\GameStore\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
			'igdb_client_id' => ['nullable', 'string', 'max:200'],
            'igdb_client_secret' => ['nullable', 'string', 'max:200'],
		];
    }
}
