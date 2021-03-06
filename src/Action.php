<?php namespace GeneaLabs\LaravelGovernor;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    public $incrementing = false;

    protected $primaryKey = 'name';
    protected $rules = [
        'name' => 'required|min:3',
    ];
    protected $fillable = [
        'name',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function permissions()
    {
        return $this->hasMany(Permission::class, 'action_key');
    }
}
