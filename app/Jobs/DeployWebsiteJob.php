<?php

namespace App\Jobs;

use App\Http\Api\DelcomApi;
use App\Http\Api\DelcomWebServerApi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeployWebsiteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $authToken;
    protected string $domain;

    /**
     * Create a new job instance.
     */
    public function __construct(string $authToken, string $domain)
    {
        $this->authToken = $authToken;
        $this->domain = $domain;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DelcomWebServerApi::postWebsiteDeploy(
            $this->authToken,
            $this->domain
        );
    }
}
