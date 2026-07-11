<?php


/**
 * HomeController
 * Trang chủ
 */
class HomeController extends Controller
{
    public function index()
    {
        $topicModel = $this->model('Topic');
        $topics = $topicModel->getAllWithStats();

        $this->view('home/index', [
            'title' => 'Trang chủ - ' . APP_NAME,
            'topics' => $topics,
            'user' => Middleware::user(),
        ]);
    }

    /**
     * AJAX search endpoint — trả về JSON
     */
    public function search()
    {
        $q = trim($_GET['q'] ?? '');
        if (mb_strlen($q) < 2) {
            $this->json(['results' => []]);
            return;
        }

        $topicModel = $this->model('Topic');
        $results = $topicModel->search($q);

        $this->json(['results' => $results]);
    }
}
