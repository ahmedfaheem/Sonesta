<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Reservation;
use Illuminate\Database\Query\Expression;
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
        $countries = Reservation::query()
            ->select('clients.country as country', DB::raw('COUNT(*) as count'))
            ->join('clients', 'clients.id', '=', 'reservations.client_id')
            ->groupBy('clients.country')
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
        $genders = Client::query()
            ->select('gender', DB::raw('COUNT(*) as count'))
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
