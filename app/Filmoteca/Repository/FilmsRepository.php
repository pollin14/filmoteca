<?php namespace Filmoteca\Repository;

use Filmoteca\Models\Film;

class FilmsRepository extends ResourcesRepository
{
	public function __construct(Film $film)
	{
		$this->resource = $film;
	}

	/**
	 * Realiza una búsqueda de una película.
	 * @param  String $by      
	 * @param  String | Array 	$data   Valor que se desea buscar.
	 * @param  Array $options (Opcional)
	 * @return Collection | Eloquent
	 */
	public function search($by, $data, $options = [])
	{
		switch($by)
		{
			case( 'title' ):

				return $this->resource->where($by, 'like', '%' . $data . '%')->get();

			default:
				throw new Exception("The type of search there is not.");
		}
	}
}

