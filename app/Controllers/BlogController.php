<?php
namespace App\Controllers;

use App\Models\Blog;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;

class BlogController extends BaseController {
    private $blog;
    private const POSTS_PER_PAGE = 10;

    public function __construct(PhpRenderer $renderer) {
        parent::__construct($renderer);
        $this->blog = new Blog();
    }

    public function index(Request $request, Response $response): Response {
        try {
            $params = $request->getQueryParams();
            $page = isset($params['page']) ? max(1, intval($params['page'])) : 1;
            $posts = $this->blog->getAllPosts($page, self::POSTS_PER_PAGE);
            
            if (empty($posts) && $page > 1) {
                return $this->error($response, 'Page not found', 404);
            }

            return $this->render($response, 'blog/index.php', [
                'title' => 'Blog - Gideon\'s Technology',
                'page' => 'blog',
                'posts' => $posts,
                'currentPage' => $page,
                'postsPerPage' => self::POSTS_PER_PAGE
            ]);
        } catch (\Exception $e) {
            return $this->error($response, 'Failed to load blog posts: ' . $e->getMessage());
        }
    }

    public function show(Request $request, Response $response, array $args): Response {
        try {
            $slug = $args['slug'] ?? null;
            if (!$slug || !is_string($slug)) {
                throw new \Exception('Invalid post slug');
            }

            $post = $this->blog->getPostBySlug($slug);
            if (!$post) {
                return $this->error($response, 'Post not found', 404);
            }

            return $this->render($response, 'blog/post.php', [
                'title' => $post['title'] . ' - Gideon\'s Technology',
                'page' => 'blog',
                'post' => $post
            ]);
        } catch (\Exception $e) {
            return $this->error($response, 'Failed to load post: ' . $e->getMessage());
        }
    }

    public function category(Request $request, Response $response, array $args): Response {
        try {
            $category = $args['category'] ?? null;
            if (!$category || !is_string($category)) {
                throw new \Exception('Invalid category');
            }

            $params = $request->getQueryParams();
            $page = isset($params['page']) ? max(1, intval($params['page'])) : 1;
            $posts = $this->blog->getPostsByCategory(
                $category, 
                $page, 
                self::POSTS_PER_PAGE
            );

            if (empty($posts) && $page > 1) {
                return $this->error($response, 'Page not found', 404);
            }

            return $this->render($response, 'blog/category.php', [
                'title' => ucfirst($category) . ' - Blog - Gideon\'s Technology',
                'page' => 'blog',
                'posts' => $posts,
                'category' => $category,
                'currentPage' => $page,
                'postsPerPage' => self::POSTS_PER_PAGE
            ]);
        } catch (\Exception $e) {
            return $this->error($response, 'Failed to load category posts: ' . $e->getMessage());
        }
    }

    private function error(Response $response, $message, $code = 500): Response {
        return $this->render($response->withStatus($code), 'error/index.php', [
            'title' => 'Error - Gideon\'s Technology',
            'message' => $message,
            'code' => $code
        ]);
    }
}
