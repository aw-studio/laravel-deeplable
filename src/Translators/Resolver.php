<?php

namespace AwStudio\Deeplable\Translators;

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
     * @param string $abstract
     * @param Closure $resolver
     * @return void
     */
    public function register($abstract, Closure $resolver)
    {
        $this->resolvers[$abstract] = $resolver;
    }

    /**
     * Get a translator.
     *
     * @param string $alias
     * @return BaseTranslator|null
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
     * @param Model $model
     * @return void
     */
    public function for(Model $model)
    {
        return $this->get(
            call_user_func($this->strategy, $model)
        );
    }

    /**
     * Set strategy.
     *
     * @param Closure $closure
     * @return void
     */
    public function strategy(Closure $closure)
    {
        $this->strategy = $closure;
    }
}
