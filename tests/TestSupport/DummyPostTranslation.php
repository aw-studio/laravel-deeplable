<?php

namespace Tests\TestSupport;

use Illuminate\Database\Eloquent\Model;

class DummyPostTranslation extends Model
{
    public $table = 'post_translations';
    protected $fillable = ['title'];
    public $timestamps = false;
}
