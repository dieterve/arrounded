<?php
namespace Arrounded\Seeders;

use Closure;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * An enhanced core seeder class
 */
abstract class AbstractSeeder extends Seeder
{
	/**
	 * The Faker instance
	 *
	 * @var Faker
	 */
	protected $faker;

	/**
	 * Build a new Seed
	 */
	public function __construct()
	{
		$this->faker = Faker::create();
	}

	/**
	 * Run a seeder
	 *
	 * @param  string $table
	 *
	 * @return void
	 */
	public function seed($table)
	{
		$timer = microtime(true);
		$this->command->info('Seeding '.$table);
		$this->call($table.'TableSeeder');

		// Log results
		$results = Str::singular($table);
		$timer   = round(microtime(true) - $timer, 2);
		$this->command->comment(sprintf('-- %s entries created (%sms)', $results::count(), $timer));
	}

	////////////////////////////////////////////////////////////////////
	/////////////////////////////// HELPERS ////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Generate X entries
	 *
	 * @param  Closure $closure
	 * @param integer  $min Minimum entries
	 * @param integer  $max Maximum entries
	 *
	 * @return void
	 */
	protected function generateEntries(Closure $closure, $min = 5, $max = null)
	{
		if (!$max) {
			$max = $min + 5;
		}

		// Execute the Closure n times
		$number = $this->faker->randomNumber($min, $max);
		for ($i = 0; $i <= $number; $i++) {
			$closure($i);
		}
	}

	/**
	 * Get a random model from the database
	 *
	 * @param  string $model
	 *
	 * @return Eloquent
	 */
	protected function randomModel($model, $notIn = array())
	{
		$models = $model::query();
		if ($notIn) {
			$models = $models->whereNotIn('id', $notIn);
		}

		return $this->faker->randomElement($models->lists('id'));
	}
}