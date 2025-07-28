<?php

namespace DevNoKage;

use DevNoKage\Enums\KeyRoute;

class Router
{
    private static array $routes = [];
    
    public static function setRoute(array $routes): void
    {
        self::$routes = $routes;
    }
    
    public static function resolve(): void
    {
        try {
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $method = $_SERVER['REQUEST_METHOD'];
            
            error_log("🔍 URI demandée: " . $uri);
            error_log("📝 Méthode HTTP: " . $method);

            // Vérifier si la route existe exactement
            if (isset(self::$routes[$uri])) {
                error_log("✅ Route exacte trouvée");
                self::executeRoute(self::$routes[$uri]);
                return;
            }

            // Chercher une route avec paramètres
            foreach (self::$routes as $route => $config) {
                if (self::matchRoute($route, $uri)) {
                    error_log("✅ Route avec paramètres trouvée: " . $route);
                    self::executeRoute($config, self::extractParams($route, $uri));
                    return;
                }
            }

            // Route 404
            error_log("❌ Aucune route trouvée, retour 404");
            header('HTTP/1.1 404 Not Found');
            header('Content-Type: application/json');
            echo json_encode([
                'data' => null,
                'statut' => 'error',
                'code' => 404,
                'message' => 'Route non trouvée'
            ]);
        } catch (\Exception $e) {
            error_log("❌ Erreur dans le routeur: " . $e->getMessage());
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode([
                'data' => null,
                'statut' => 'error',
                'code' => 500,
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ]);
        }
    }
    
    private static function matchRoute(string $route, string $uri): bool
    {
        $routePattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route);
        $routePattern = '#^' . $routePattern . '$#';
        return preg_match($routePattern, $uri);
    }
    
    private static function extractParams(string $route, string $uri): array
    {
        $routePattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route);
        $routePattern = '#^' . $routePattern . '$#';
        
        preg_match($routePattern, $uri, $matches);
        array_shift($matches);
        
        return $matches;
    }
    
   private static function executeRoute(array $config, array $params = []): void
{
    $controllerClass = $config[KeyRoute::CONTROLLER->value];
    $method = $config[KeyRoute::METHOD->value];
    $httpMethod = $config[KeyRoute::HTTP_METHOD->value] ?? 'GET';

    if (!class_exists($controllerClass)) {
        http_response_code(500);
        echo json_encode(['error' => 'Controller not found']);
        return;
    }

    $controller = new $controllerClass();

    if (!method_exists($controller, $method)) {
        http_response_code(500);
        echo json_encode(['error' => 'Method not found']);
        return;
    }

    // Cas spécial POST JSON : injecter les données du body
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($params)) {
        $input = json_decode(file_get_contents('php://input'), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['error' => 'Format JSON invalide']);
            return;
        }

        if (!isset($input['numero_compteur'], $input['montant'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Paramètres requis: numero_compteur et montant']);
            return;
        }

        $params = [
            trim($input['numero_compteur']),
            floatval($input['montant']),
        ];
    }

    try {
        call_user_func_array([$controller, $method], $params);
    } catch (\Throwable $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

}