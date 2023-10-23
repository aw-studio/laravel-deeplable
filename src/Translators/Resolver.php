<?php

namespace AwStudio\Deeplable\Translators;

use AwStudio\Deeplable\Contracts\Translator;
use Closure;
use Illuminate\Database\Eloquent\Model;

class Resolver
{
    /**
     * Translator resolver.
     *
     * @var array[Closure]
     */
    protected $resolvers = [];

    /**
     * Resolved translators.
     *
     * @var array[BaseTranslator]
     */
    protected $resolved = [];

    /**
     * Register a translator resolver.
     *
     * @param  string  $alias
     * @param  Closure  $resolver
     * @return void
     */
    public function register($alias, Closure $resolver)
    {
        $this->resolvers[$alias] = $resolver;
    }

    /**
     * Get a translator.
     *
     * @param  string  $alias
     * @return Translator|null
     */
    public function get($alias)
    {
        if (array_key_exists($alias, $this->resolved)) {
            return $this->resolved[$alias];
        }

        if (! array_key_exists($alias, $this->resolvers)) {
            return;
        }

        return $this->resolved[$alias] = call_user_func($this->resolvers[$alias]);
    }

    /**
     * Get translator for the given model.
     *
     * @param  Model  $model
     * @return Translator|null
     */
    public function for(Model $model)
    {
        if($model->translator){
            $translator = new $model->translator;
            if($translator instanceof Translator){
                return $translator;
            }
        }
        
        return $this->get(
            call_user_func($this->strategy, $model)
        );
    }

    /**
     * Set translation strategy.
     *
     * @param  Closure  $closure
     * @return void
     */
    public function strategy(Closure $closure)
    {
        $this->strategy = $closure;
    }
}
