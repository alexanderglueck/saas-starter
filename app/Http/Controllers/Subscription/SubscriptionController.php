<?php

namespace App\Http\Controllers\Subscription;

use App\Events\SubscriptionCreated;
use App\Plan;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subscription\SubscriptionStoreRequest;
use App\User;
use Illuminate\Http\Request;
use Laravel\Cashier\Exceptions\PaymentActionRequired;
use Laravel\Cashier\SubscriptionBuilder;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $plans = Plan::active()->get();

        return view('subscription.index', [
            'plans' => $plans,
            'intent' => $request->user()->createSetupIntent()
        ]);
    }

    public function store(SubscriptionStoreRequest $request)
    {
        /** @var SubscriptionBuilder $subscription */
        $subscription = $request->user()->newSubscription('main', $request->plan);

        if ($request->has('coupon')) {
            $subscription->withCoupon($request->coupon);
        }

        try {
            $subscription->create($request->token);
        } catch (PaymentActionRequired $exception) {
            return redirect()->route('cashier.payment',
                [$exception->payment->id, 'redirect' => route('home')]
            );
        }

        event(new SubscriptionCreated($request->user()));

        flashSuccess('Thanks for becoming a subscriber!');

        return redirect()->route('home');
    }
}
