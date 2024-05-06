<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessDocument implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private string $filename)
    {
    }

    public function handle(): void
    {
        $path = storage_path('app/' . $this->filename);
        $command = "lowriter --convert-to pdf $path --outdir " . storage_path('app');
        exec($command, $output, $result);
        info($command, [$output, $result]);
    }
}
