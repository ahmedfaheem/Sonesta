<?php

namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ManagerClientsExport implements FromQuery, WithMapping, WithHeadings
{
    use Exportable;

    public function __construct(private readonly int $managerId)
    {
    }

    public function query()
    {
        return Client::query()
            ->with(['user', 'approvedBy'])
            ->whereHas('user', function ($query) {
                $query->role('client')
                    ->where('created_by', $this->managerId);
            });
    }

    public function map($user): array
    {
        $gender = $user->gender ? ucfirst($user->gender) : 'Unknown';
        $country = $user->country ?: 'Unknown';

        return [
            $user->id,
            $user->user?->name ?? $user->name,
            $user->user?->email ?? $user->email,
            $country,
            $gender,
            $user->is_approved ? 'Approved' : 'Pending',
            $user->approvedBy?->name,
            $user->created_at?->toDateTimeString(),
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
