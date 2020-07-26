<?php

namespace App\Traits;

trait HasSubscriptions
{
    public function hasPiggybackSubscription(): bool
    {
        if ($this->team_id == null) {
            return false;
        }

//        if ($this->id == $this->team->owner->id) {
//            return false;
//        }

//        if (auth()->user()->team->owner->hasSubscription()) {
//            return true;
//        }

        return false;
    }

    public function hasSubscription($subscription = 'main'): bool
    {
        if ($this->hasPiggybackSubscription()) {
            return true;
        }

        return $this->subscribed($subscription);
    }

    public function hasNoSubscription($subscription = 'main'): bool
    {
        return ! $this->hasSubscription($subscription);
    }

    public function hasCancelled()
    {
        return optional($this->subscription('main'))->cancelled();
    }

    public function hasNotCancelled()
    {
        return ! $this->hasCancelled();
    }

    public function isCustomer()
    {
        return $this->hasStripeId();
    }

    public function hasTeamSubscription()
    {
        foreach ($this->plans as $plan) {
            if ($plan->isForTeams()) {
                return true;
            }
        }

        return false;
    }

    public function doesNotHaveTeamSubscription(): bool
    {
        return ! $this->hasTeamSubscription();
    }
}
