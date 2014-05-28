<?php
namespace Arrounded\Abstracts;

use Arrounded\Collection;
use Arrounded\Traits\ReflectionModel;
use Arrounded\Traits\Serializable;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractModel extends Model
{
	use ReflectionModel;
	use Serializable;

	/**
	 * The attributes to cast on serialization
	 *
	 * @var array
	 */
	protected $casts = array(
		'integer' => ['id'],
	);

	/**
	 * Create a new Eloquent Collection instance.
	 *
	 * @param  array  $models
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function newCollection(array $models = array())
	{
		return new Collection($models);
	}

	/**
	 * Get all models belonging to other models
	 *
	 * @param string $relation
	 * @param array  $ids
	 *
	 * @return Query
	 */
	public function scopeWhereBelongsTo($query, $relation, array $ids = array())
	{
		$ids = $ids ?: ['void'];

		return $query->whereIn($relation.'_id', $ids);
	}

	/**
	 * Cast the model to an array
	 *
	 * @return array
	 */
	public function toArray()
	{
		$model = parent::toArray();
		$model = $this->serializeEntity($model);

		return $model;
	}
}