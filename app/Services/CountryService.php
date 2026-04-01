<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Rinvex\Country\Country;

class CountryService
{
    public function all(): array
    {
        return Cache::remember('countries.v2', 86400, function () {
            return collect(countries())
                ->map(fn (array|Country $country) => [
                    'name' => is_array($country)
                        ? data_get($country, 'name.common', data_get($country, 'name'))
                        : $country->getName(),
                ])
                ->filter(fn (array $country) => filled($country['name']))
                ->sortBy('name')
                ->values()
                ->all();
        });
    }
}
