<?php namespace Illuminate\Database\Eloquent\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

abstract class HasOneOrMany extends Relation {

	/**
	 * The foreign key of the parent model.
	 *
	 * @var string
	 */
	protected $foreignKey;

	/**
	 * Create a new has many relationship instance.
	 *
	 * @param  Illuminate\Database\Eloquent\Builder  $query
	 * @param  Illuminate\Database\Eloquent\Model  $parent
	 * @param  string  $foreignKey
	 * @return void
	 */
	public function __construct(Builder $query, Model $parent, $foreignKey)
	{
		$this->foreignKey = $foreignKey;

		parent::__construct($query, $parent);
	}

	/**
	 * Create a new instance of the related model.
	 *
	 * @param  array  $attributes
	 * @return mixed
	 */
	public function create(array $attributes)
	{
		$foreign = array($this->foreignKey => $this->parent->getKey());

		$instance = $this->related->newInstance(array_merge($attributes, $foreign));

		return $instance->save();
	}

	/**
	 * Set the base constraints on the relation query.
	 *
	 * @return void
	 */
	public function addConstraints()
	{
		$key = $this->parent->getKey();

		$this->query->where($this->foreignKey, '=', $key);
	}

	/**
	 * Set the constraints for an eager load of the relation.
	 *
	 * @param  array  $models
	 * @return void
	 */
	public function addEagerConstraints(array $models)
	{
		$this->query->whereIn($this->foreignKey, $this->getKeys($models));
	}

	/**
	 * Build model dictionary keyed by the relation's foreign key.
	 *
	 * @param  Illuminate\Database\Eloquent\Collection  $reuslts
	 * @return array
	 */
	protected function buildDictionary(Collection $results)
	{
		$dictionary = array();

		// First we will create a dictionary of models keyed by the foreign key of the
		// relationship as this will allow us to quickly access all of the related
		// models without having to do nested looping which will be quite slow.
		foreach ($results as $result)
		{
			$dictionary[$result->{$this->foreignKey}][] = $result;
		}

		return $dictionary;
	}

}