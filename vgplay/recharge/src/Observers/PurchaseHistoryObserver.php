<?php

namespace Vgplay\Recharge\Observers;

use Vgplay\Recharge\Models\PurchaseHistory;
use Vgplay\Recharge\Services\PurchaseService;

class PurchaseHistoryObserver
{
    public bool $afterCommit = true;

    public function created(PurchaseHistory $p): void
    {
        // Nếu tạo với trạng thái đã paid thì flush
        if ($p->status === 'paid') {
            app(PurchaseService::class)->forgetUserGame((int)$p->vgp_id, (int)$p->game_id);
        }
    }

    public function updated(PurchaseHistory $p): void
    {
        // Khi chuyển trạng thái sang paid
        if ($p->wasChanged('status') && $p->status === 'paid') {
            app(PurchaseService::class)->forgetUserGame((int)$p->vgp_id, (int)$p->game_id);
        }
    }

    public function deleted(PurchaseHistory $p): void
    {
        // Xoá đơn không ảnh hưởng lịch sử paid (tuỳ yêu cầu),
        // nếu muốn làm sạch thì vẫn có thể flush:
        app(PurchaseService::class)->forgetUserGame((int)$p->vgp_id, (int)$p->game_id);
    }
}
