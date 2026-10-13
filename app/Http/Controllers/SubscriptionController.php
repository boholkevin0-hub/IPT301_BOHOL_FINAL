<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubscriptionController extends Controller
{
    public function index()
    {
        $records = Subscription::withCount('watchHistory')->latest()->paginate(10);

        return view('resources.index', [
            'heading' => 'Subscriptions',
            'description' => 'Track subscriber plans, billing, and membership status.',
            'createRoute' => 'subscriptions.create',
            'resource' => 'subscriptions',
            'columns' => ['Subscriber', 'Plan', 'Monthly price', 'Status', 'Watch sessions'],
            'rows' => $records->through(fn (Subscription $subscription) => [
                'id' => $subscription->id,
                'primary' => $subscription->subscriber_name,
                'cells' => [
                    $subscription->plan,
                    '$'.number_format((float) $subscription->monthly_price, 2),
                    ucfirst($subscription->status),
                    $subscription->watch_history_count,
                ],
            ]),
            'records' => $records,
        ]);
    }

    public function create()
    {
        return view('resources.form', [
            'heading' => 'Add subscription',
            'resource' => 'subscriptions',
            'action' => route('subscriptions.store'),
            'method' => 'POST',
            'fields' => $this->fields(),
        ]);
    }

    public function store(Request $request)
    {
        $subscription = Subscription::create($request->validate($this->rules()));

        return redirect()->route('subscriptions.show', $subscription)->with('success', 'Subscription created successfully.');
    }

    public function show(Subscription $subscription)
    {
        $subscription->load(['watchHistory.movie']);

        return view('resources.show', [
            'heading' => $subscription->subscriber_name,
            'resource' => 'subscriptions',
            'editRoute' => route('subscriptions.edit', $subscription),
            'details' => [
                'Email' => $subscription->email,
                'Plan' => $subscription->plan,
                'Monthly price' => '$'.number_format((float) $subscription->monthly_price, 2),
                'Status' => ucfirst($subscription->status),
                'Started' => $subscription->started_at->format('F j, Y'),
                'Expires' => $subscription->expires_at?->format('F j, Y') ?: 'No expiry date',
            ],
            'relatedTitle' => 'Watch history',
            'related' => $subscription->watchHistory->map(fn ($history) => [
                'title' => $history->movie->title,
                'subtitle' => $history->watched_at->format('M j, Y g:i A').' · '.$history->minutes_watched.' min watched',
                'url' => route('watch-histories.show', $history),
            ]),
            'deleteRoute' => route('subscriptions.destroy', $subscription),
        ]);
    }

    public function edit(Subscription $subscription)
    {
        return view('resources.form', [
            'heading' => 'Edit subscription',
            'resource' => 'subscriptions',
            'action' => route('subscriptions.update', $subscription),
            'method' => 'PUT',
            'fields' => $this->fields(),
            'item' => $subscription,
        ]);
    }

    public function update(Request $request, Subscription $subscription)
    {
        $subscription->update($request->validate($this->rules($subscription)));

        return redirect()->route('subscriptions.show', $subscription)->with('success', 'Subscription updated successfully.');
    }

    public function destroy(Subscription $subscription)
    {
        if ($subscription->watchHistory()->exists()) {
            return back()->with('error', 'This subscription has watch-history records. Remove those records before deleting it.');
        }

        $subscription->delete();

        return redirect()->route('subscriptions.index')->with('success', 'Subscription deleted successfully.');
    }

    private function rules(?Subscription $subscription = null): array
    {
        return [
            'subscriber_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('subscriptions', 'email')->ignore($subscription?->id)],
            'plan' => ['required', Rule::in(['Basic', 'Standard', 'Premium'])],
            'monthly_price' => ['required', 'numeric', 'min:0', 'max:99999.99'],
            'started_at' => ['required', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:started_at'],
            'status' => ['required', Rule::in(['active', 'paused', 'cancelled'])],
        ];
    }

    private function fields(): array
    {
        return [
            ['name' => 'subscriber_name', 'label' => 'Subscriber name', 'required' => true],
            ['name' => 'email', 'label' => 'Email address', 'type' => 'email', 'required' => true],
            ['name' => 'plan', 'label' => 'Plan', 'type' => 'select', 'required' => true, 'choices' => ['Basic' => 'Basic', 'Standard' => 'Standard', 'Premium' => 'Premium']],
            ['name' => 'monthly_price', 'label' => 'Monthly price', 'type' => 'number', 'required' => true, 'min' => 0, 'step' => '0.01'],
            ['name' => 'started_at', 'label' => 'Started on', 'type' => 'date', 'required' => true],
            ['name' => 'expires_at', 'label' => 'Expires on', 'type' => 'date', 'required' => false],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'required' => true, 'choices' => ['active' => 'Active', 'paused' => 'Paused', 'cancelled' => 'Cancelled']],
        ];
    }
}
