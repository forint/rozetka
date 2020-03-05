<?php
namespace App\Controller\Admin;

use App\Core\View;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Settings;

/**
 * Class Films
 * @package App\Controller\Admin
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
     * List of films
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function indexAction()
    {
        if ($_SESSION && $_SESSION['is_admin'] == '1'){

            $films = $this->connection->get ('films');

            View::renderTemplate('admin/films/list.twig',[
                'films' => $films
            ]);
        }else{
            header('Location: /');
        }
    }

    /**
     * Add new film
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function addAction()
    {
        if ($_SESSION && $_SESSION['is_admin'] == '1'){
            View::renderTemplate('admin/films/add.twig');
        }else{
            header('Location: /');
        }
    }

    /**
     * Edit film
     *
     * @param int $id
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function editAction(int $id = null)
    {
        if ($_SESSION && $_SESSION['is_admin'] == '1'){

            $film = null;
            $connection = $this->db::getInstance();

            $filmId = $_POST['id'] ?? null;
            $title = $_POST['title'] ?? null;
            $description = $_POST['description'] ?? null;

            $director = $_POST['director'] ?? null;
            $country = $_POST['country'] ?? null;

            try {
                if ($filmId && is_numeric($filmId)) {
                    if ($title && $description) {

                        $this->uploadImage($filmId);

                        $this->connection->setTrace(true);
                        $this->connection->where("id", $filmId);
                        $result = $connection->update('films', $_POST);
/*print_r("<pre>");
print_r($this->connection->trace);
print_r("</pre>");
die;*/
                        if ($result){
                            header('Location: /admin/films/index?message=Film updated successfully');
                        }else{
                            header('Location: /admin/films/add?message=Can\'t insert film: ' . $connection->getLastError());
                        }
                    }
                }else{
                    if ($title && $description) {
                        $_POST['slug'] = \Transliterator::createFromRules(
                            ':: Any-Latin;'
                            . ':: NFD;'
                            . ':: [:Nonspacing Mark:] Remove;'
                            . ':: NFC;'
                            . ':: [:Punctuation:] Remove;'
                            . ':: Lower();'
                            . '[:Separator:] > \'-\''
                        )->transliterate( $title );

                        $id = $connection->insert('films', $_POST);
                        if ($id){
                            $this->uploadImage($id);

                            $this->connection->where("id", $id);
                            $result = $connection->update('films', $_POST);

                            header('Location: /admin/films/index?message=Film inserted successfully');
                        }else{
                            header('Location: /admin/films/add?message=Can\'t insert film: ' . $connection->getLastError());
                        }
                    }
                }
            }catch(\Exception $e){
                print_r("<pre>");
                print_r($e->getMessage());
                print_r("</pre>");
                die;
            }
            if ($id && is_numeric($id)){

                $this->connection->where ("id", $id);
                $film = $this->connection->getOne('films');
            }

            View::renderTemplate('/admin/films/edit.twig',[
                'film' => $film
            ]);

        }else{
            header('Location: /');
        }
    }

    public function deleteAction(int $id = null)
    {
        if ($_SESSION && $_SESSION['is_admin'] == '1'){
            if ($id && is_numeric($id)){
                $this->connection->where ("id", $id);
                $film = $this->connection->getOne('films');

                if ($film['img']){
                    $img = $_SERVER['DOCUMENT_ROOT'].'uploads/'.$film['img'];
                    if (file_exists($img)) {
                        unlink($img);
                    }
                }

                $this->connection->where("id", $id);
                $result = $this->connection->delete('films');

                if ($result) {
                    header('Location: /admin/films/index?message=Film deleted successfully');
                }

            }
        }else{
            header('Location: /');
        }
    }

    /**
     * @param $filmId
     * @return string
     */
    private function uploadImage($filmId)
    {
        if (sizeof($_FILES) > 0){
            $ext = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
            $newFileName = $filmId.'.'.$ext;
            $targetPath = $_SERVER['DOCUMENT_ROOT'] . 'uploads/'.$newFileName;
            if (move_uploaded_file($_FILES['img']['tmp_name'], $targetPath)) {
                $_POST['img'] = $newFileName;
            }
        }

        return $newFileName;
    }
}