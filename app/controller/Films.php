<?php
namespace App\Controller;

use App\Core\Database;
use App\Core\View;
use App\Core\Controller;

/**
 * Class Films
 * @package App\Controller
 */
class Films extends Controller
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
     * Show film details page
     *
     * @param $filmSlug
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function viewAction($filmSlug)
    {
        $fullSeats = [];
        $this->connection->where("status", '1');
        $this->connection->where("slug", $filmSlug);
        $film = $this->connection->getOne('films');

        $this->connection->where("film_id", $film['id']);
        $seats = $this->connection->get('booking', null, ['seats']);
        if (sizeof($seats) > 0) {
            foreach ($seats as $_seats){
                $fullSeats = array_merge($fullSeats, explode('|', $_seats['seats']));
            }
        }else{
            header('Location: /page/notFound');
        }

        View::renderTemplate('films/index.twig',[
            'film' => $film,
            'seats' => $fullSeats
        ]);
    }

    /**
     * Insert booking options
     *
     * @throws \Exception
     */
    public function bookingAction()
    {
        $messages = [];

        $film_id = $_POST['film_id'] ?? null;
        $username = $_POST['username'] ?? null;
        $email = $_POST['email'] ?? null;
        $phone = $_POST['phone'] ?? null;
        $session = $_POST['session'] ?? null;
        $seats = $_POST['seats'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_numeric($film_id) && $username && $email && $phone && $session && $seats && sizeof($seats) > 0){

            $fullSeats = [];
            // $this->connection->setTrace(true);
            $this->connection->where("film_id", $film_id);
            $this->connection->where("session", $session);
            $_seats = $this->connection->get('booking', null, ['seats']);
            if (sizeof($_seats) > 0) {
                foreach ($_seats as $_seat){
                    $fullSeats = array_merge($fullSeats, explode('|', $_seat['seats']));
                }
            }

            if (!array_intersect($seats,$fullSeats)){
                $bookingData = [
                    'film_id' => $film_id,
                    'username' => $username,
                    'email' => $email,
                    'phone' => $phone,
                    'session' => $session,
                    'seats' => implode('|',$seats)
                ];

                $result = $this->connection->insert('booking', $bookingData);
                // print_r ($this->connection->trace); die;
                if ($result){
                    $messages[] = 'Вы успешно забронировали выбраные места. Мы с нетерпением ожидаем вас в нашем кинотеатре.';
                }else{
                    $messages[] = 'Произошла непредвиденная ошибка. Пожалуйста попробуйте ещё.';
                }
            }else{
                $messages[] = 'Хакер, то что ты умеешь пользоваться консолью не дает тебе право занимать чужие места.';
            }
        }else{
            $messages[] = 'Бронирование невозможно. Пожалуйста позвоните нам: +380637257018';
        }

        echo json_encode($messages);
        die;
    }

    public function checkReservedSeatsAction()
    {
        $fullSeats = [];
        $film_id = $_POST['film_id'] ?? null;
        $session = $_POST['session'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_numeric($film_id) && $session){

            $this->connection->where("film_id", $film_id);
            $this->connection->where("session", $session);
            $_seats = $this->connection->get('booking', null, ['seats']);


            if (sizeof($_seats) > 0) {
                foreach ($_seats as $_seat){
                    $fullSeats = array_merge($fullSeats, explode('|',$_seat['seats']));
                }
            }
        }

        echo json_encode($fullSeats);
        die;
    }
}