<?php

namespace Vgplay\Recharge\Observers;

use Vgplay\Recharge\Models\GamePaymentMethod;
use Vgplay\Recharge\Services\PaymentService;

class GamePaymentMethodObserver
{
    public bool $afterCommit = true;

    public function saved(GamePaymentMethod $cfg): void
    {
        app(PaymentService::class)->forgetByGame((int)$cfg->game_id);
    }

    public function deleted(GamePaymentMethod $cfg): void
    {
        app(PaymentService::class)->forgetByGame((int)$cfg->game_id);
    }

    public function restored(GamePaymentMethod $cfg): void
    {
        app(PaymentService::class)->forgetByGame((int)$cfg->game_id);
    }
}
