<?php

namespace App\Jobs;

use App\Http\Controllers\Api\SynchronizerController;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Illuminate\Support\Facades\Log;


class ProductsAccordanceJob implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    //public $filePath;

    /**
     * Количество попыток выполнения задания.
     *
     * @var int
     */
    public $tries = 1;

    public function __construct()
    {
        //$this->filePath = $filePath;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Log::channel("jobs")->info("[ProcessSyncLogJob] START " . $this->id);

        SynchronizerController::products_accordance();

        //Log::channel("jobs")->info("[ProcessSyncLogJob] FINISH " . $this->id);
    }

}
