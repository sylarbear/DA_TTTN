<?php
/**
 * HomeController
 * Trang chủ
 */
class HomeController extends Controller {

    public function index() {
        $topicModel = $this->model('Topic');
        $topics = $topicModel->getAllWithStats();

        $this->view('home/index', [
            'title'  => 'Trang chủ - ' . APP_NAME,
            'topics' => $topics,
            'user'   => Middleware::user()
        ]);
    }
}
