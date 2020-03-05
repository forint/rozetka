<?php

namespace App\Controller\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\View;

/**
 * User admin controller
 */
class Dashboard extends Controller
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
     * Before filter
     *
     * @return void
     */
    protected function before()
    {
        // Make sure an admin user is logged in for example
        // return false;
    }

    /**
     * Cancel add-edit mode
     */
    public function cancelAction()
    {
        header('Location: /admin/films/index');
    }

    /**
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function indexAction()
    {
        if ($_SESSION && $_SESSION['is_admin'] == '1') {

            $this->connection->join("booking b", "b.film_id=f.id", "LEFT");
            $this->connection->groupBy ("f.id");
            $_films = $this->connection->get('films f', null, "f.*, b.session, b.seats");


            $this->connection->setTrace(true);
            $this->connection->join("booking b", "b.film_id=f.id", "LEFT");
            //$this->connection->groupBy ("f.id");
            $films = $this->connection->get('films f', null, "f.*, b.session, b.seats");//GROUP_CONCAT(b.session SEPARATOR '|') AS sessions, GROUP_CONCAT(b.seats SEPARATOR '|') AS seats
            //print_r ($this->connection->trace); die;

            /**
             * Grouping all data into multidimensional array:
             * First level is film_id
             * Second level is session
             * Third are seats
             */
            $progress = [];
            $counterSeats = [];
            $sessionsSeats = [];
            foreach ($films as $film) {

                if ($film['session'] && $film['seats']){
                    $seats = explode('|', $film['seats']);

                    if ($seats){
                        for ($i = 0; $i < sizeof($seats); $i++){
                            if (!array_key_exists($film['id'], $counterSeats)){ // && !is_array($counterSeats[$film['id']])
                                $counterSeats[$film['id']] = [];
                            }

                            if (!array_key_exists($film['id'], $sessionsSeats) || !array_key_exists($film['session'], $sessionsSeats[$film['id']])){ //&& !array_key_exists($sessions[$i], $sessionsSeats[$film['id']]) && !is_array($sessionsSeats[$film['id']][$sessions[$i]])
                                $sessionsSeats[$film['id']][$film['session']] = [];
                            }

                            $counterSeats[$film['id']] = array_merge($counterSeats[$film['id']], explode('|',$seats[$i]));
                            //if (array_key_exists($i, $sessions) && array_key_exists($sessions[$i], $sessionsSeats[$film['id']])){
                            $sessionsSeats[$film['id']][$film['session']] = array_merge($sessionsSeats[$film['id']][$film['session']], explode('|',$seats[$i]));
                           // }
                        }
                    }
                }

            }

            View::renderTemplate('admin/dashboard/index.twig',[
                'films' => $_films,
                'sessionsSeats' => $sessionsSeats,
                'counterSeats' => $counterSeats
            ]);

        }else{
            header('Location: /');
        }
    }

}