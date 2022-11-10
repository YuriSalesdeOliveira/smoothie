<?php

namespace Source\Http\Controllers\Web;

use Source\Http\Controllers\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Source\Library\PageBuilder\Page;
use Source\Library\PageBuilder\PageBuilder;

class Index extends Controller
{
    public function handle(Request $request, Response $response): Response
    {
        $baseHTML = new Page(
            paths('resources') . '/template/baseHTML.php'
        );

        $pageBuilder = new PageBuilder(
            $baseHTML,
            styleOutputPath: paths('public') . '/assets/style.css',
            scriptOutputPath: paths('public') . '/assets/script.js'
        );

        $componentList = ['slide'];
        foreach ($componentList as $componentName) {

            $component = new Page(
                paths('resources') . "/components/{$componentName}.php"
            );

            $pageBuilder->addComponent($componentName, $component);
        }

        $page = $pageBuilder->build();

        $response->getBody()->write($page);
        return $response;
    }
}