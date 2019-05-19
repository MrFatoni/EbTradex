<?php

namespace App\Jobs;

use App\Services\User\Trader\WalletService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StockItemWithdrawal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $withdrawalId;
    public $address;
    public $amount;

    /**
     * Create a new job instance.
     *
     * @param $withdrawalId
     */
    public function __construct($withdrawalId)
    {
        $this->queue = 'withdrawal';
        $this->withdrawalId = $withdrawalId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        app(WalletService::class)->send($this->withdrawalId);
    }
}
