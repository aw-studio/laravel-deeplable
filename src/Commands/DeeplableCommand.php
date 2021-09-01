<?php

namespace AwStudio\Deeplable\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use AwStudio\Deeplable\Facades\Translator;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class DeeplableCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'deeplable:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate missing translations via DeepL.';

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['locale', InputArgument::OPTIONAL, 'The locale of the language to translate to'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Whether existing records are to be overwritten', null],
        ];
    }

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
        $force = $this->hasOption('force') && $this->option('force');

        foreach ($models as $model) {
            $translator = Translator::for($model);

            foreach ($locales as $locale) {
                $translator->translate($model, $locale, $fallbackLocale, $force);
            }

            $model->save();
        }
    }
}
