<?php

namespace Vgplay\Recharge\Observers;

use Vgplay\Recharge\Models\PaymentMethod;
use Vgplay\Recharge\Services\PaymentService;
use Illuminate\Support\Facades\DB;

class PaymentMethodObserver
{
    public bool $afterCommit = true;

    public function saved(PaymentMethod $pm): void
    {
        $this->flushGames($pm);
    }

    public function deleted(PaymentMethod $pm): void
    {
        $this->flushGames($pm);
    }

    public function restored(PaymentMethod $pm): void
    {
        $this->flushGames($pm);
    }

    protected function flushGames(PaymentMethod $pm): void
    {
        $svc = app(PaymentService::class);
        $gameIds = DB::table('game_payment_method')
            ->where('payment_method_id', $pm->id)
            ->pluck('game_id');

        foreach ($gameIds as $gid) {
            $svc->forgetByGame((int)$gid);
        }
    }
}
