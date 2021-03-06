<?php
namespace Arrounded\Abstracts\Controllers;

use Arrounded\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Input;
use Request;

/**
 * A smart controller based on a Repository implementation
 */
abstract class AbstractSmartRepositoryController extends AbstractSmartController
{
	/**
	 * The repository in use
	 *
	 * @type RepositoryInterface
	 */
	protected $repository;

	/**
	 * The relationships to eager load automatically
	 *
	 * @type array
	 */
	protected $eagerLoaded = array();

	/**
	 * Build a new AbstractSmartRepositoryController
	 *
	 * @param RepositoryInterface $repository
	 */
	public function __construct(RepositoryInterface $repository)
	{
		parent::__construct();

		$this->repository = $repository->eagerLoad($this->eagerLoaded);
	}

	////////////////////////////////////////////////////////////////////
	///////////////////////////////// CRUD /////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Display a listing of the resource.
	 *
	 * @param  array        $eager
	 * @param  integer|null $paginate
	 *
	 * @return \Illuminate\View\View
	 */
	protected function coreIndex($eager = array(), $paginate = null)
	{
		return $this->getView('index', array(
			'items' => $this->repository->all($paginate),
		));
	}

	/**
	 * Get the core create view
	 *
	 * @param  array $data Additional data
	 *
	 * @return \Illuminate\View\View
	 */
	protected function coreCreate($data = array())
	{
		return $this->getView('edit', $this->getFormData($data));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $user
	 *
	 * @return \Illuminate\View\View
	 */
	protected function coreShow($user)
	{
		return $this->getView('show', $this->getShowData($user));
	}

	/**
	 * Get the core edit view
	 *
	 * @param  integer $item
	 * @param  array   $data Additional data
	 *
	 * @return \Illuminate\View\View
	 */
	protected function coreEdit($item, $data = array())
	{
		$item         = $this->getSingleModel($item);
		$data['item'] = $item;

		return $this->getView('edit', $this->getFormData($data));
	}

	/**
	 * Update an item
	 *
	 * @param  integer|null $item
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	protected function coreUpdate($item = null)
	{
		// Get item
		$item  = $item ? $this->repository->find($item) : $this->repository->getModelInstance();
		$input = Input::all();

		// Execute hooks
		$this->onUpdate($input, $item);

		// Update attributes
		$item = $this->repository->update($item, $input);

		// Update relationships
		foreach ($input as $key => $value) {
			if (method_exists($item, $key) && $item->$key() instanceof BelongsToMany) {
				$item->$key()->sync($value);
			}
		}

		return $this->getRedirect('index')->with('success', true);
	}

	/**
	 * Delete an item
	 *
	 * @param  integer $item
	 * @param  boolean $force
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	protected function coreDestroy($item, $force = false)
	{
		$this->repository->delete($item, $force);

		if (Request::ajax()) {
			return \Response::json(array(), 204);
		}

		return $this->getRedirect('index');
	}

	////////////////////////////////////////////////////////////////////
	////////////////////////////// VIEW DATA ///////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get a single model
	 *
	 * @param integer $item
	 *
	 * @return Model
	 */
	protected function getSingleModel($item)
	{
		$item = $this->repository->find($item);
		$item = $item->load($this->eagerLoaded);

		return $item;
	}

	/**
	 * Get the data to display
	 *
	 * @param integer $item
	 *
	 * @return array<string,Model>
	 */
	protected function getShowData($item)
	{
		return array(
			'item' => $this->getSingleModel($item),
		);
	}

	/**
	 * Get the form data
	 *
	 * @param array <string,Model> $data
	 *
	 * @return array
	 */
	public function getFormData(array $data = array())
	{
		$item  = array_get($data, 'item') ?: $this->repository->getModelInstance();
		$route = $item->id ? 'update' : 'store';

		return array(
			'item'  => $item,
			'route' => $this->getRoute($route),
		);
	}
}
