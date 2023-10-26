<?php

namespace Tests\TestSupport;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class DummyPost extends Model implements TranslatableContract
{
    use Translatable;

    public $table = 'posts';
    protected $translationModel = DummyPostTranslation::class;
    protected $translatedAttributes = ['title'];
    protected $translationForeignKey = 'post_id';
    public $timestamps = false;
}
