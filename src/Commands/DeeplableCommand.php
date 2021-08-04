<?php

namespace AwStudio\Deeplable\Commands;

use AwStudio\Deeplable\Facades\Deepl;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class DeeplableCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deeplable:run {locale?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate missing translations via DeepL.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $models = config('deeplable.translated_models');

        foreach ($models as $model) {
            $instance = new $model;

            $all = $instance->get();

            $this->translateCollection($all);
        }
    }

    /**
     * Translate a collection of models.
     *
     * @param  Collection $models
     * @return void
     */
    public function translateCollection(Collection $models): void
    {
        $locales = collect($this->argument('locale') ?: config('translatable.locales'));

        $models->each(function ($model) use ($locales) {
            // skip if there is no default translation
            if (! $model->hasTranslation()) {
                return;
            }

            $locales->each(function ($locale) use ($model) {
                if (! $model->hasTranslation($locale)) {
                    Deepl::translateModel($model, $locale);
                }
            });
        });
    }
}
