<?php

namespace AwStudio\Deeplable\Jobs;

use AwStudio\Deeplable\Facades\Translator;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TranslateModelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        protected $model,
        protected $locale,
        protected $fallbackLocale,
        protected $force
    ) {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $translator = Translator::for($this->model);
        try {
            $translator->translate($this->model, $this->locale, $this->fallbackLocale, $this->force);
        } catch (GuzzleException $e) {
            $this->error('Failed to translate '.get_class($this->model));
        }
    }
}
