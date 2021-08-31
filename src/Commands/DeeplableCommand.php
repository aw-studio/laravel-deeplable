<?php

namespace AwStudio\Deeplable\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use AwStudio\Deeplable\Facades\Translator;

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
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $fallbackLocale = config('translatable.fallback_locale');
        $locales = $this->getLocales();
        $models = config('deeplable.translated_models');

        foreach ($models as $model) {
            $this->translateCollection((new $model)->get(), $locales, $fallbackLocale);
        }
    }

    /**
     * Get locales that should be translated to.
     *
     * @return array
     */
    public function getLocales()
    {
        return collect($this->argument('locale') ?: config('translatable.locales'))
            ->filter(fn ($locale) => $locale != config('translatable.fallback_locale'))
            ->toArray();
    }

    /**
     * Translate a collection of models.
     *
     * @param  Collection $models
     * @param array $locales
     * @param string $fallbackLocale
     * @return void
     */
    public function translateCollection(Collection $models, $locales, $fallbackLocale): void
    {
        foreach ($models as $model) {
            $translator = Translator::for($model);

            foreach ($locales as $locale) {
                $translator->translate($model, $locale, $fallbackLocale);
            }

            $model->save();
        }
    }
}
