<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OpenAI API Key
    |--------------------------------------------------------------------------
    |
    | Here you may specify the OpenAI API key that will be used to authenticate
    | requests to the OpenAI API. You should set this to your API key which
    | can be found at https://platform.openai.com/api-keys
    |
    */

    'api_key' => env('OPENAI_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Organization
    |--------------------------------------------------------------------------
    |
    | Here you may specify which organization is used for API requests.
    | This is optional and can be omitted if you only belong to one organization.
    |
    */

    'organization' => env('OPENAI_ORGANIZATION'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout may be used to specify the maximum number of seconds to wait
    | for a response. By default, the client will timeout after 30 seconds.
    |
    */

    'request_timeout' => env('OPENAI_REQUEST_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Default Model
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default model that will be used for requests.
    | You can override this on a per-request basis.
    |
    */

    'default_model' => env('OPENAI_DEFAULT_MODEL', 'gpt-3.5-turbo'),
]; 