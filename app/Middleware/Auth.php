namespace App\Middleware;

use App\Services\Auth as AuthService;

class Auth
{
    protected $auth;

    public function __construct(AuthService $auth)
    {
        $this->auth = $auth;
    }

    public function handle($request, \Closure $next)
    {
        if ($this->auth->check()) {
            return $next($request);
        }

        return redirect('login');
    }
}