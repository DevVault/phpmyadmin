<?php

declare(strict_types=1);

namespace PhpMyAdmin\Controllers\Database\Structure;

use PhpMyAdmin\Controllers\AbstractController;
use PhpMyAdmin\Core;
use PhpMyAdmin\Current;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Http\Response;
use PhpMyAdmin\Http\ServerRequest;
use PhpMyAdmin\ResponseRenderer;
use PhpMyAdmin\Template;

use function __;

final class ShowCreateController extends AbstractController
{
    public function __construct(ResponseRenderer $response, Template $template, private DatabaseInterface $dbi)
    {
        parent::__construct($response, $template);
    }

    public function __invoke(ServerRequest $request): Response|null
    {
        /** @var string[] $selected */
        $selected = $request->getParsedBodyParam('selected_tbl', []);

        if ($selected === []) {
            $this->response->setRequestStatus(false);
            $this->response->addJSON('message', __('No table selected.'));

            return null;
        }

        $tables = $this->getShowCreateTables($selected);

        $showCreate = $this->template->render('database/structure/show_create', ['tables' => $tables]);

        $this->response->addJSON('message', $showCreate);

        return null;
    }

    /**
     * @param string[] $selected Selected tables.
     *
     * @return array<string, array<int, array<string, string>>>
     */
    private function getShowCreateTables(array $selected): array
    {
        $tables = ['tables' => [], 'views' => []];

        foreach ($selected as $table) {
            $object = $this->dbi->getTable(Current::$database, $table);

            $tables[$object->isView() ? 'views' : 'tables'][] = [
                'name' => Core::mimeDefaultFunction($table),
                'show_create' => Core::mimeDefaultFunction($object->showCreate()),
            ];
        }

        return $tables;
    }
}
