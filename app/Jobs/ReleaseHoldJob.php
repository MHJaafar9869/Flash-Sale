<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Hold;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ReleaseHoldJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected string $holdId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (! Hold::find($this->holdId)->exists()) {
            return;
        }

        DB::transaction(function () {
            $hold = Hold::find($this->holdId);

            if ($hold->is_used) {
                return;
            }

            $product = $hold->product;

            $product->increment('stock', $hold->qty);

            $hold->refresh();
            $product->refresh();

            DB::afterCommit(function () use ($product, $hold) {
                Cache::put("products:{$product->id}", $product, 30);
                Cache::put("holds:{$hold->id}", $hold, 30);
            });
        });
    }
}
