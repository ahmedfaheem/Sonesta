<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function revenuePerMonth(): JsonResponse
    {
        $driver = DB::connection()->getDriverName();
        $groupBy = $driver === 'sqlite'
            ? "strftime('%Y-%m', reservation_date)"
            : "DATE_FORMAT(reservation_date, '%Y-%m')";

        $revenue = Reservation::query()
            ->selectRaw("{$groupBy} as month, SUM(paid_price) as revenue")
            ->whereNotNull('reservation_date')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($row) {
                return [
                    'month' => $row->month,
                    'revenue' => number_format(($row->revenue ?? 0) / 100, 2, '.', ''),
                ];
            });

        return response()->json(['data' => $revenue]);
    }

    public function reservationsByCountry(): JsonResponse
    {
        $countryField = DB::connection()->getDriverName() === 'sqlite'
            ? "COALESCE(NULLIF(TRIM(clients.country), ''), 'Unknown')"
            : "COALESCE(NULLIF(TRIM(clients.country), ''), 'Unknown')";

        $countries = Reservation::query()
            ->selectRaw("{$countryField} as country, COUNT(*) as count")
            ->join('clients', 'clients.id', '=', 'reservations.client_id')
            ->groupBy('country')
            ->orderByDesc('count')
            ->get()
            ->map(function ($row) {
                return [
                    'country' => $row->country,
                    'count' => (int) $row->count,
                ];
            });

        return response()->json(['data' => $countries]);
    }

    public function genderRatio(): JsonResponse
    {
        $genderField = DB::connection()->getDriverName() === 'sqlite'
            ? "LOWER(COALESCE(NULLIF(TRIM(clients.gender), ''), 'unknown'))"
            : "LOWER(COALESCE(NULLIF(TRIM(clients.gender), ''), 'unknown'))";

        $genders = Client::query()
            ->selectRaw("{$genderField} as gender, COUNT(*) as count")
            ->groupBy('gender')
            ->orderBy('gender')
            ->get()
            ->map(function ($row) {
                return [
                    'gender' => $row->gender,
                    'count' => (int) $row->count,
                ];
            });

        return response()->json(['data' => $genders]);
    }

    public function topClients(): JsonResponse
    {
        $clients = Client::query()
            ->with('user')
            ->withCount('reservations')
            ->orderByDesc('reservations_count')
            ->limit(5)
            ->get()
            ->map(function (Client $client) {
                return [
                    'id' => $client->id,
                    'name' => $client->user?->name ?? $client->name,
                    'reservations_count' => $client->reservations_count,
                ];
            });

        return response()->json(['data' => $clients]);
    }
}
