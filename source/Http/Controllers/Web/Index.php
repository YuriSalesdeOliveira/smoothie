<?php

namespace Source\Http\Controllers\Web;

use Slim\Routing\RouteContext;
use Source\Library\PageBuilder\Page;
use Source\Http\Controllers\Controller;
use Source\Library\PageBuilder\PageBuilder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Source\Http\Controllers\Components\Contracts\Component;

class Index extends Controller
{
    public function handle(Request $request, Response $response): Response
    {
        $routeContext = RouteContext::fromRequest($request);
        $routeParser = $routeContext->getRouteParser();

        $baseHTML = new Page(
            paths('resources') . '/template/baseHTML.php',
            args: [
                'title' => 'smoothie',
                'route' => $routeParser
            ]
        );

        $pageBuilder = new PageBuilder(
            $baseHTML,
            styleOutputPath: paths('public') . '/assets/css/style.css',
            scriptOutputPath: paths('public') . '/assets/js/script.js'
        );

        $componentList = ['slide', 'gallery'];
        foreach ($componentList as $componentName) {

            $componentController = $this->getController(
                namespace: 'Source\Http\Controllers\Components',
                controllerName: $componentName
            );

            if ($componentController) {

                $component = $componentController->handle(
                    $request->getParsedBody(),
                    $request->getQueryParams(),
                    [
                        'route' => $routeParser
                    ]
                );
                
            } else {

                $component = new Page(
                    paths('resources') . "/components/{$componentName}/index.php",
                    args: [
                        'route' => $routeParser
                    ]
                );
            }

            $pageBuilder->addComponent($component);
        }

        $page = $pageBuilder->build();

        $response->getBody()->write($page);
        return $response;
    }

    protected function getController(string $namespace, string $controllerName, array $constructArgs = []): Component|false
    {
        $controller =  "{$namespace}\\" . ucfirst($controllerName);

        if (class_exists($controller)) {

            return new $controller(...$constructArgs);
        }

        return false;
    }
}
