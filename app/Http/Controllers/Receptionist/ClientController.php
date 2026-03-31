<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Http\Requests\Receptionist\ApproveClientRequest;
use App\Models\User;
use App\Notifications\ClientApprovedNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    public function pending(Request $request): Response
    {
        $this->authorize('viewPendingClients', User::class);

        return Inertia::render('Receptionist/Clients/Pending', [
            'clients' => $this->pendingClientsQuery()
                ->paginate(10)
                ->withQueryString()
                ->through(fn (User $client) => $this->serializeClient($client)),
        ]);
    }

    public function approve(ApproveClientRequest $request, User $user): RedirectResponse
    {
        $client = $this->loadClientOrFail($user);

        $this->authorize('approveClient', $client);

        $client->forceFill([
            'is_approved' => true,
            'approved_by' => $request->user()->id,
        ])->save();

        $client->clientProfile()->update([
            'is_approved' => true,
            'approved_by' => $request->user()->id,
        ]);

        $client->notify(new ClientApprovedNotification());

        return back()->with('success', "{$client->name} approved successfully.");
    }

    public function approved(Request $request): Response
    {
        $this->authorize('viewApprovedClients', User::class);

        return Inertia::render('Receptionist/Clients/MyApproved', [
            'clients' => $this->approvedClientsQuery($request->user()->id)
                ->paginate(10)
                ->withQueryString()
                ->through(fn (User $client) => $this->serializeClient($client)),
        ]);
    }

    protected function pendingClientsQuery(): Builder
    {
        return User::query()
            ->role('client')
            ->with(['clientProfile.approvedBy:id,name', 'approvedBy:id,name'])
            ->whereHas('clientProfile', fn (Builder $query) => $query->where('is_approved', false))
            ->latest();
    }

    protected function approvedClientsQuery(int $receptionistId): Builder
    {
        return User::query()
            ->role('client')
            ->with(['clientProfile.approvedBy:id,name', 'approvedBy:id,name'])
            ->whereHas('clientProfile', function (Builder $query) use ($receptionistId): void {
                $query
                    ->where('is_approved', true)
                    ->where('approved_by', $receptionistId);
            })
            ->latest('updated_at');
    }

    protected function loadClientOrFail(User $user): User
    {
        abort_unless($user->hasRole('client') && $user->clientProfile()->exists(), 404);

        return $user->load(['clientProfile.approvedBy:id,name', 'approvedBy:id,name']);
    }

    protected function serializeClient(User $client): array
    {
        $profile = $client->clientProfile;

        return [
            'id' => $client->id,
            'name' => $client->name,
            'email' => $client->email,
            'avatar_url' => $client->avatar ? asset('storage/'.$client->avatar) : null,
            'national_id' => $client->national_id,
            'country' => $profile?->country,
            'gender' => $profile?->gender,
            'is_approved' => (bool) $profile?->is_approved,
            'approved_by' => $client->approved_by,
            'approved_by_name' => $profile?->approvedBy?->name ?? $client->approvedBy?->name,
            'created_at' => $client->created_at?->toISOString(),
            'approved_at' => $profile?->updated_at?->toISOString(),
        ];
    }
}
