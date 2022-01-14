<?php

namespace AwStudio\Deeplable\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use GuzzleHttp\Exception\GuzzleException;
use AwStudio\Deeplable\Facades\Translator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

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
