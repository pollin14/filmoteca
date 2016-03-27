<?php

namespace Filmoteca\Exhibitions\Controllers\Frontend;

use Carbon\Carbon;
use Filmoteca\Repository\ExhibitionsRepository;
use Filmoteca\Exhibition\ExhibitionsManager;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Input;
use InvalidArgumentException;

/**
 * Class ExhibitionController
 */
class ExhibitionController extends Controller
{
    /**
     * @var ExhibitionsRepository
     */
    protected $repository;

    /**
     * @var ExhibitionsManager
     */
    protected $manager;

    /**
     * @param ExhibitionsRepository $exhibitionRepository
     * @param ExhibitionsManager $exhibitionsManager
     */
    public function __construct(ExhibitionsRepository $exhibitionRepository, ExhibitionsManager $exhibitionsManager)
    {
        $this->repository   = $exhibitionRepository;
        $this->manager      = $exhibitionsManager;
    }

    /**
     * @param $humanDate
     * @return mixed
     */
    public function index($humanDate = '')
    {
        if ($humanDate !== '' && substr($humanDate, 0, 1) === '0') {
            App::abort(404);
        }

        try {
            $englishDate = $this->manager->convertMonthFromHumanToNumber($humanDate);
            $date = Carbon::createFromFormat(ExhibitionsManager::DATE_FORMAT, $englishDate);
        } catch (InvalidArgumentException $e) {
            $date = Carbon::today();
        }

        $exhibitions = $this->repository->findByDate($date);
        $calendar = $this->manager->getCalendar($date);

        return View::make('frontend.exhibitions.index', compact('exhibitions', 'date', 'calendar'));
    }

    public function history()
    {
        if (empty(Input::all())) {
            return View::make('frontend.exhibitions.history');
        }

        $fields = [];
        $fieldsNames = ['title', 'director'];

        foreach ($fieldsNames as $name) {
            if (Input::has($name)) {
                $fields[$name] = Input::get($name);
            }
        }

        $fields = array_reduce($fieldsNames, function ($fields, $name) {
            if (Input::has($name)) {
                $fields[$name] = Input::get($name);
            }

            return $fields;
        }, []);

        if (empty($fields)) {
            return View::make('frontend.exhibitions.history');
        }

        $results = $this->repository->findByTitleDirector($fields);

        return View::make('frontend.exhibitions.history')->with('results', $results);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $exhibition = $this->repository->search('id', $id);

        return View::make('frontend.exhibitions.show', compact('exhibition'));
    }
}
