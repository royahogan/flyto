<?php

use App\Auth\Controllers\ConfirmarUsuarioActionController;
use App\Auth\Controllers\LoginPageController;
use App\Auth\Controllers\LoginUsuarioActionController;
use App\Auth\Controllers\LogoutUsuarioActionController;
use App\Auth\Controllers\RegisterUsuarioActionController;
use App\Auth\Controllers\RegistroPageController;
use App\Auth\Middlewares\AdminMiddleware;
use App\Auth\Middlewares\AuthMiddleware;
use App\Auth\Middlewares\CeoMiddleware;
use App\Auth\Middlewares\GuestMiddleware;
use App\Auth\Repositories\TipoUsuarioRepository;
use App\Auth\Repositories\UsuarioRepository;
use App\Auth\Services\ConfirmacionUsuarioEmailService;
use App\Auth\Services\ConfirmarUsuarioService;
use App\Auth\Services\LoginUsuarioService;
use App\Auth\Services\LogoutUsuarioService;
use App\Auth\Services\RegisterUsuarioService;
use App\Auth\Services\SessionService;
use App\Contacto\Controllers\ContactoPageController;
use App\Contacto\Controllers\EnviarMensajeActionController;
use App\Contacto\Services\ContactoEmailService;
use App\Contacto\Services\EnviarMensajeService;
use App\Ciudades\Repositories\CiudadRepository;
use App\Ciudades\Services\CiudadService;
use App\Container;
use App\Home\Controllers\HomePageController;
use App\Novedades\Controllers\AdminNovedadesPageController;
use App\Novedades\Controllers\BorrarNovedadActionController;
use App\Novedades\Controllers\CrearNovedadActionController;
use App\Novedades\Controllers\EditarNovedadActionController;
use App\Novedades\Controllers\NovedadesPageController;
use App\Novedades\Repositories\NovedadRepository;
use App\Novedades\Services\NovedadService;
use App\Perfil\Controllers\MiPerfilPageController;
use App\Reservas\Controllers\CancelarReservaActionController;
use App\Reservas\Controllers\CrearReservaActionController;
use App\Reservas\Controllers\ConfirmacionPageController;
use App\Reservas\Controllers\GuardarPasajerosActionController;
use App\Reservas\Controllers\PagoPageController;
use App\Reservas\Controllers\PasajerosPageController;
use App\Reservas\Repositories\ReservaRepository;
use App\Reservas\Services\ReservaService;
use App\Reservas\Controllers\MisReservasPageController;
use App\Reservas\Controllers\ReservaDetallePageController;
use App\Router;
use App\Shared\Config\Env;
use App\Shared\Database\Database;
use App\Shared\Http\Flash;
use App\Shared\Http\HttpException;
use App\Shared\Http\RedirectResponse;
use App\Shared\Http\ViewResponse;
use App\Shared\Services\EmailService;
use App\Vuelos\Controllers\BuscarVuelosPageController;
use App\Vuelos\Controllers\VueloController;
use App\Vuelos\Repositories\VueloRepository;
use App\Vuelos\Services\VueloService;

$autoload = __DIR__ . '/../vendor/autoload.php';

if (file_exists($autoload)) {
    require_once $autoload;
}

require_once __DIR__ . '/../app/container.php';
require_once __DIR__ . '/../app/router.php';

require_once __DIR__ . '/../app/shared/config/env.php';
require_once __DIR__ . '/../app/shared/database/database.php';
require_once __DIR__ . '/../app/shared/http/flash.php';
require_once __DIR__ . '/../app/shared/http/http-exception.php';
require_once __DIR__ . '/../app/shared/http/redirect-response.php';
require_once __DIR__ . '/../app/shared/http/view-response.php';
require_once __DIR__ . '/../app/shared/services/email.service.php';

require_once __DIR__ . '/../app/auth/middlewares/auth.middleware.php';
require_once __DIR__ . '/../app/auth/middlewares/admin.middleware.php';
require_once __DIR__ . '/../app/auth/middlewares/ceo.middleware.php';
require_once __DIR__ . '/../app/auth/middlewares/guest.middleware.php';
require_once __DIR__ . '/../app/auth/repositories/usuario.repository.php';
require_once __DIR__ . '/../app/auth/repositories/tipo-usuario.repository.php';
require_once __DIR__ . '/../app/auth/services/session.service.php';
require_once __DIR__ . '/../app/auth/services/confirmacion-usuario-email.service.php';
require_once __DIR__ . '/../app/auth/services/register-usuario.service.php';
require_once __DIR__ . '/../app/auth/services/confirmar-usuario.service.php';
require_once __DIR__ . '/../app/auth/services/login-usuario.service.php';
require_once __DIR__ . '/../app/auth/services/logout-usuario.service.php';
require_once __DIR__ . '/../app/auth/controllers/login-page.controller.php';
require_once __DIR__ . '/../app/auth/controllers/registro-page.controller.php';
require_once __DIR__ . '/../app/auth/controllers/confirmar-usuario-action.controller.php';
require_once __DIR__ . '/../app/auth/controllers/login-usuario-action.controller.php';
require_once __DIR__ . '/../app/auth/controllers/register-usuario-action.controller.php';
require_once __DIR__ . '/../app/auth/controllers/logout-usuario-action.controller.php';

require_once __DIR__ . '/../app/contacto/services/contacto-email.service.php';
require_once __DIR__ . '/../app/contacto/services/enviar-mensaje.service.php';
require_once __DIR__ . '/../app/contacto/controllers/contacto-page.controller.php';
require_once __DIR__ . '/../app/contacto/controllers/enviar-mensaje-action.controller.php';

require_once __DIR__ . '/../app/ciudades/repositories/ciudad.repository.php';
require_once __DIR__ . '/../app/ciudades/services/ciudad.service.php';

require_once __DIR__ . '/../app/home/controllers/home-page.controller.php';

require_once __DIR__ . '/../app/novedades/repositories/novedad.repository.php';
require_once __DIR__ . '/../app/novedades/services/novedad.service.php';
require_once __DIR__ . '/../app/novedades/controllers/novedades-page.controller.php';
require_once __DIR__ . '/../app/novedades/controllers/admin-novedades-page.controller.php';
require_once __DIR__ . '/../app/novedades/controllers/crear-novedad-action.controller.php';
require_once __DIR__ . '/../app/novedades/controllers/editar-novedad-action.controller.php';
require_once __DIR__ . '/../app/novedades/controllers/borrar-novedad-action.controller.php';

require_once __DIR__ . '/../app/perfil/controllers/mi-perfil-page.controller.php';

require_once __DIR__ . '/../app/reservas/repositories/reserva.repository.php';
require_once __DIR__ . '/../app/reservas/services/reserva.service.php';
require_once __DIR__ . '/../app/reservas/controllers/cancelar-reserva-action.controller.php';
require_once __DIR__ . '/../app/reservas/controllers/crear-reserva-action.controller.php';
require_once __DIR__ . '/../app/reservas/controllers/confirmacion-page.controller.php';
require_once __DIR__ . '/../app/reservas/controllers/guardar-pasajeros-action.controller.php';
require_once __DIR__ . '/../app/reservas/controllers/pago-page.controller.php';
require_once __DIR__ . '/../app/reservas/controllers/pasajeros-page.controller.php';
require_once __DIR__ . '/../app/reservas/controllers/mis-reservas-page.controller.php';
require_once __DIR__ . '/../app/reservas/controllers/reserva-detalle-page.controller.php';

require_once __DIR__ . '/../app/vuelos/repositories/vuelo.repository.php';
require_once __DIR__ . '/../app/vuelos/services/vuelo.service.php';
require_once __DIR__ . '/../app/vuelos/controllers/vuelo.controller.php';
require_once __DIR__ . '/../app/vuelos/controllers/buscar-vuelos-page.controller.php';

Env::load(__DIR__ . '/../.env.example');

$container = new Container();

$container->singleton(Database::class, function () {
    return Database::getConnection();
});

$container->singleton(SessionService::class, function () {
    return new SessionService();
});

$container->scoped(ViewResponse::class, function ($c) {
    return new ViewResponse($c->get(SessionService::class));
});

$container->scoped(AuthMiddleware::class, function ($c) {
    return new AuthMiddleware($c->get(SessionService::class));
});

$container->scoped(AdminMiddleware::class, function ($c) {
    return new AdminMiddleware($c->get(SessionService::class));
});

$container->scoped(CeoMiddleware::class, function ($c) {
    return new CeoMiddleware($c->get(SessionService::class));
});

$container->scoped(GuestMiddleware::class, function ($c) {
    return new GuestMiddleware($c->get(SessionService::class));
});

$container->scoped(UsuarioRepository::class, function ($c) {
    return new UsuarioRepository($c->get(Database::class));
});

$container->scoped(TipoUsuarioRepository::class, function ($c) {
    return new TipoUsuarioRepository($c->get(Database::class));
});

$container->scoped(EmailService::class, function () {
    return new EmailService();
});

$container->scoped(ConfirmacionUsuarioEmailService::class, function ($c) {
    return new ConfirmacionUsuarioEmailService($c->get(EmailService::class));
});

$container->scoped(RegisterUsuarioService::class, function ($c) {
    return new RegisterUsuarioService(
        $c->get(UsuarioRepository::class),
        $c->get(TipoUsuarioRepository::class),
        $c->get(ConfirmacionUsuarioEmailService::class)
    );
});

$container->scoped(ConfirmarUsuarioService::class, function ($c) {
    return new ConfirmarUsuarioService($c->get(UsuarioRepository::class));
});

$container->scoped(LoginUsuarioService::class, function ($c) {
    return new LoginUsuarioService(
        $c->get(UsuarioRepository::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(LogoutUsuarioService::class, function ($c) {
    return new LogoutUsuarioService($c->get(SessionService::class));
});

$container->scoped(LoginPageController::class, function ($c) {
    return new LoginPageController(
        $c->get(SessionService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(RegistroPageController::class, function ($c) {
    return new RegistroPageController(
        $c->get(SessionService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(RegisterUsuarioActionController::class, function ($c) {
    return new RegisterUsuarioActionController(
        $c->get(RegisterUsuarioService::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(ConfirmarUsuarioActionController::class, function ($c) {
    return new ConfirmarUsuarioActionController(
        $c->get(ConfirmarUsuarioService::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(LoginUsuarioActionController::class, function ($c) {
    return new LoginUsuarioActionController(
        $c->get(LoginUsuarioService::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(LogoutUsuarioActionController::class, function ($c) {
    return new LogoutUsuarioActionController(
        $c->get(LogoutUsuarioService::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(ContactoEmailService::class, function ($c) {
    return new ContactoEmailService($c->get(EmailService::class));
});

$container->scoped(EnviarMensajeService::class, function ($c) {
    return new EnviarMensajeService($c->get(ContactoEmailService::class));
});

$container->scoped(ContactoPageController::class, function ($c) {
    return new ContactoPageController($c->get(ViewResponse::class));
});

$container->scoped(EnviarMensajeActionController::class, function ($c) {
    return new EnviarMensajeActionController($c->get(EnviarMensajeService::class));
});

$container->scoped(CiudadRepository::class, function ($c) {
    return new CiudadRepository($c->get(Database::class));
});

$container->scoped(CiudadService::class, function ($c) {
    return new CiudadService($c->get(CiudadRepository::class));
});

$container->scoped(HomePageController::class, function ($c) {
    return new HomePageController(
        $c->get(CiudadService::class),
        $c->get(NovedadService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(NovedadRepository::class, function ($c) {
    return new NovedadRepository($c->get(Database::class));
});

$container->scoped(NovedadService::class, function ($c) {
    return new NovedadService($c->get(NovedadRepository::class));
});

$container->scoped(NovedadesPageController::class, function ($c) {
    return new NovedadesPageController(
        $c->get(NovedadService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(AdminNovedadesPageController::class, function ($c) {
    return new AdminNovedadesPageController(
        $c->get(NovedadService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(CrearNovedadActionController::class, function ($c) {
    return new CrearNovedadActionController(
        $c->get(NovedadService::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(EditarNovedadActionController::class, function ($c) {
    return new EditarNovedadActionController(
        $c->get(NovedadService::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(BorrarNovedadActionController::class, function ($c) {
    return new BorrarNovedadActionController(
        $c->get(NovedadService::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(MiPerfilPageController::class, function ($c) {
    return new MiPerfilPageController($c->get(ViewResponse::class));
});

$container->scoped(ReservaRepository::class, function ($c) {
    return new ReservaRepository($c->get(Database::class));
});

$container->scoped(ReservaService::class, function ($c) {
    return new ReservaService($c->get(ReservaRepository::class));
});

$container->scoped(CancelarReservaActionController::class, function ($c) {
    return new CancelarReservaActionController(
        $c->get(ReservaService::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(MisReservasPageController::class, function ($c) {
    return new MisReservasPageController(
        $c->get(ReservaService::class),
        $c->get(SessionService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(ReservaDetallePageController::class, function ($c) {
    return new ReservaDetallePageController(
        $c->get(ReservaService::class),
        $c->get(SessionService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(CrearReservaActionController::class, function ($c) {
    return new CrearReservaActionController(
        $c->get(ReservaService::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(ConfirmacionPageController::class, function ($c) {
    return new ConfirmacionPageController(
        $c->get(ReservaService::class),
        $c->get(SessionService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(PasajerosPageController::class, function ($c) {
    return new PasajerosPageController(
        $c->get(ReservaService::class),
        $c->get(SessionService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(GuardarPasajerosActionController::class, function ($c) {
    return new GuardarPasajerosActionController(
        $c->get(ReservaService::class),
        $c->get(SessionService::class)
    );
});

$container->scoped(PagoPageController::class, function ($c) {
    return new PagoPageController(
        $c->get(ReservaService::class),
        $c->get(SessionService::class),
        $c->get(ViewResponse::class)
    );
});

$container->scoped(VueloRepository::class, function ($c) {
    return new VueloRepository($c->get(Database::class));
});

$container->scoped(VueloService::class, function ($c) {
    return new VueloService($c->get(VueloRepository::class));
});

$container->scoped(VueloController::class, function ($c) {
    return new VueloController($c->get(VueloService::class));
});

$container->scoped(BuscarVuelosPageController::class, function ($c) {
    return new BuscarVuelosPageController(
        $c->get(CiudadService::class),
        $c->get(VueloService::class),
        $c->get(ViewResponse::class)
    );
});

function resolvePage(
    array $route,
    Container $container,
    array $query,
    string $layoutPath
): void {
    if (isset($route['controller'], $route['action'])) {
        $controller = $container->get($route['controller']);
        $controller->{$route['action']}([], $query, $layoutPath);
        return;
    }

    // Cuando todas las paginas que usan datos tengan el PageController
    // esto se puede sacar
    $viewData = isset($route['data']) ? $route['data']() : [];

    $container->get(ViewResponse::class)->render(
        $route['view'],
        $route['title'],
        $viewData,
        200,
        $layoutPath
    );
}

function routePublicPage(
    array $routes,
    string $requestPath,
    array $query,
    Container $container
): bool {
    if (!isset($routes[$requestPath])) {
        return false;
    }

    resolvePage(
        $routes[$requestPath],
        $container,
        $query,
        __DIR__ . '/../app/shared/views/layouts/public.layout.php'
    );

    return true;
}

function routeProtectedPage(
    array $routes,
    string $requestPath,
    array $query,
    Container $container,
    string $middlewareClass,
    string $layoutPath,
    string $forbiddenMessage
): bool {
    if (!isset($routes[$requestPath])) {
        return false;
    }

    try {
        $container->get($middlewareClass)->handle();
    } catch (HttpException $exception) {
        Flash::error($forbiddenMessage);
        RedirectResponse::to($exception->getStatusCode() === 401 ? '/auth/login' : '/', [], 303);
        return true;
    }

    resolvePage($routes[$requestPath], $container, $query, $layoutPath);

    return true;
}

function routeAdminPage(
    array $routes,
    string $requestPath,
    array $query,
    Container $container
): bool {
    return routeProtectedPage(
        $routes,
        $requestPath,
        $query,
        $container,
        AdminMiddleware::class,
        __DIR__ . '/../app/shared/views/layouts/admin.layout.php',
        'Necesitas permisos de administrador para acceder a esta pagina.'
    );
}

function routeProtectedPublicPage(
    array $routes,
    string $requestPath,
    array $query,
    Container $container
): bool {
    return routeProtectedPage(
        $routes,
        $requestPath,
        $query,
        $container,
        AuthMiddleware::class,
        __DIR__ . '/../app/shared/views/layouts/public.layout.php',
        'Necesitas iniciar sesion para acceder a esta pagina.'
    );
}

function routeCeoPage(
    array $routes,
    string $requestPath,
    array $query,
    Container $container
): bool {
    return routeProtectedPage(
        $routes,
        $requestPath,
        $query,
        $container,
        CeoMiddleware::class,
        __DIR__ . '/../app/shared/views/layouts/ceo.layout.php',
        'Necesitas permisos de CEO para acceder a esta pagina.'
    );
}

$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$basePath = $scriptDir === '/' ? '' : rtrim($scriptDir, '/');

if ($basePath !== '' && str_starts_with($requestPath, $basePath)) {
    $requestPath = substr($requestPath, strlen($basePath)) ?: '/';
}

$requestPath = $requestPath !== '/' ? rtrim($requestPath, '/') : '/';
$requestQuery = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_QUERY);
parse_str(is_string($requestQuery) ? $requestQuery : '', $queryParams);

$publicRoutes = [
    '/' => [
        'controller' => HomePageController::class,
        'action' => 'show',
    ],
    '/auth/login' => [
        'controller' => LoginPageController::class,
        'action' => 'show',
    ],
    '/auth/registro' => [
        'controller' => RegistroPageController::class,
        'action' => 'show',
    ],
    '/auth/registro/confirmacion-enviada' => [
        'view' => __DIR__ . '/../app/auth/views/pages/registro-confirmacion-enviada.page.php',
        'title' => 'Confirmacion enviada - Flyto',
    ],
    '/auth/cuenta-confirmada' => [
        'view' => __DIR__ . '/../app/auth/views/pages/cuenta-confirmada.page.php',
        'title' => 'Cuenta confirmada - Flyto',
    ],
    '/auth/recuperar-contrasena' => [
        'view' => __DIR__ . '/../app/auth/views/pages/recuperar-contrasena.page.php',
        'title' => 'Recuperar contrasena - Flyto',
    ],
    '/auth/recuperar-contrasena/codigo' => [
        'view' => __DIR__ . '/../app/auth/views/pages/recuperar-contrasena-token.page.php',
        'title' => 'Codigo de recuperacion - Flyto',
    ],
    '/auth/recuperar-contrasena/cambiar' => [
        'view' => __DIR__ . '/../app/auth/views/pages/recuperar-contrasena-cambiar.page.php',
        'title' => 'Cambiar contrasena - Flyto',
    ],
    '/novedades' => [
        'controller' => NovedadesPageController::class,
        'action' => 'show',
    ],
    '/faq' => [
        'view' => __DIR__ . '/../app/faq/views/pages/faq.page.php',
        'title' => 'Preguntas frecuentes - Flyto',
    ],
    '/contacto' => [
        'controller' => ContactoPageController::class,
        'action' => 'show',
    ],
    '/vuelos/buscar' => [
        'controller' => BuscarVuelosPageController::class,
        'action' => 'show',
    ],
];

$protectedPublicRoutes = [
    '/reservas/pasajeros' => [
        'controller' => PasajerosPageController::class,
        'action' => 'show',
    ],
    '/reservas/pago' => [
        'controller' => PagoPageController::class,
        'action' => 'show',
    ],
    '/reservas/confirmacion' => [
        'controller' => ConfirmacionPageController::class,
        'action' => 'show',
    ],
    '/mi-perfil/mis-reservas' => [
        'controller' => MisReservasPageController::class,
        'action' => 'show',
    ],
    '/mi-perfil/datos' => [
        'controller' => MiPerfilPageController::class,
        'action' => 'show',
    ],
    '/reservas/detalle' => [
        'controller' => ReservaDetallePageController::class,
        'action' => 'show',
    ],
];

$adminRoutes = [
    '/admin' => [
        'view' => __DIR__ . '/../app/admin/views/pages/admin.page.php',
        'title' => 'Admin - Flyto',
    ],
    '/admin/novedades' => [
        'controller' => AdminNovedadesPageController::class,
        'action' => 'show',
    ],
];

$ceoRoutes = [
    '/ceo' => [
        'view' => __DIR__ . '/../app/ceo/views/pages/ceo.page.php',
        'title' => 'CEO - Flyto',
    ],
];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (routeAdminPage($adminRoutes, $requestPath, $queryParams, $container)) {
        return;
    }

    if (routeCeoPage($ceoRoutes, $requestPath, $queryParams, $container)) {
        return;
    }

    if (routeProtectedPublicPage($protectedPublicRoutes, $requestPath, $queryParams, $container)) {
        return;
    }

    if (routePublicPage($publicRoutes, $requestPath, $queryParams, $container)) {
        return;
    }
}

$router = new Router();

$router->registerModule(require __DIR__ . '/../app/auth/routes.php');
$router->registerModule(require __DIR__ . '/../app/contacto/routes.php');
$router->registerModule(require __DIR__ . '/../app/novedades/routes.php');
$router->registerModule(require __DIR__ . '/../app/reservas/routes.php');
$router->registerModule(require __DIR__ . '/../app/vuelos/routes.php');

$normalizedUri = $requestPath;

if (is_string($requestQuery) && $requestQuery !== '') {
    $normalizedUri .= '?' . $requestQuery;
}

$router->resolve(
    $_SERVER['REQUEST_METHOD'],
    $normalizedUri,
    $container
);
