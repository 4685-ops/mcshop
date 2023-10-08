<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Class BaseModel
 *
 * @package App\Models
 * @method static Builder|BaseModel newModelQuery()
 * @method static Builder|BaseModel newQuery()
 * @method static Builder|BaseModel query()
 * @mixin Eloquent
 */
class BaseModel extends Model
{
    use HasFactory;
    public const CREATED_AT = 'add_time';

    public const UPDATED_AT = 'update_time';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * 屏蔽表最后增加s
     * @return string
     */
    public function getTable()
    {
        return $this->table ?? Str::snake(class_basename($this));
    }

}
