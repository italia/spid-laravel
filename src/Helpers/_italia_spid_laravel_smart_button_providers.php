<?php

/**
 * @param bool $in_random_order
 * @return array
 */
function smart_button_providers($in_random_order = true) : array 
{
    $providers = collect(Config::get('spid-idps'))->except('empty');

    // Hide real providers
    if (Config::get('spid-auth.hide_real_idps')) {
        $providers = $providers->only(['test', 'validator']);
    }

    // hide
    if (!Config::get('spid-auth.test_idp')) {
        $providers = $providers->except('test');
    }

    // Show validator_idp
    if (!Config::get('spid-auth.validator_idp')) {
        $providers = $providers->except('validator');
    }

    // Randomize providers order
    if ($in_random_order) {
        $providers = $providers->shuffle();
    }

    return $providers->map(function ($provider) {
        return (object)[
            'provider' => $provider['provider'],
            'title'    => $provider['title'],
            'logo'     => $provider['logo'],
            'logoPng'  => $provider['logoPng'],
        ];
    })->all();
}
