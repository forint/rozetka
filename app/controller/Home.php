<?php
namespace App\Controller;

use App\Core\Database;
use App\Core\View;
use App\Core\Controller;

/**
 * Class Home
 * @package App\Controller
 */
class Home extends Controller
{
    /**
     * @var Database $db
     */
    private $db;

    /**
     * @var Database|\MysqliDb|null $connection
     */
    private $connection;

    /**
     * Films constructor.
     * @param $route_params
     */
    public function __construct($route_params)
    {
        parent::__construct($route_params);

        $this->db = new Database();
        $this->connection = $this->db::getInstance();
    }

    /**
     * View homepage
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function indexAction()
    {
        $this->connection->join("booking b", "b.film_id=f.id", "LEFT");
        $this->connection->groupBy("f.id");
        $this->connection->orderBy("TotalValue", 'DESC');
        $this->connection->orderBy("created", 'DESC');
        $popularFilms = $this->connection->get('films f', 5, "f.*, SUM((CHAR_LENGTH(b.seats) - CHAR_LENGTH(REPLACE(b.seats, ',', '')) + 1) ) as TotalValue");

        $this->connection->orderBy("created", 'DESC');
        $films = $this->connection->get('films');

        View::renderTemplate('home/index.twig',[
            'popularFilms' => $popularFilms,
            'films' => $films,
        ]);

    }
}