<?php

namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class ClientsExport implements FromQuery, WithMapping, WithHeadings
{
    use Exportable;

    public function query()
    {
        return Client::query()->with(['user', 'approvedBy']);
    }

    public function map($client): array
    {
        return [
            $client->id,
            $client->user?->name ?? $client->name,
            $client->email,
            $client->country,
            ucfirst($client->gender),
            $client->is_approved ? 'Approved' : 'Pending',
            $client->approvedBy?->name,
            $client->created_at?->toDateTimeString(),
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Country',
            'Gender',
            'Approval Status',
            'Approved By',
            'Created At',
        ];
    }
}
