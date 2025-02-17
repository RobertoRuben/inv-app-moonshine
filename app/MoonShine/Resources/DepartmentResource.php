<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Department;

use MoonShine\ImportExport\Contracts\HasImportExportContract;
use MoonShine\ImportExport\ExportHandler;
use MoonShine\ImportExport\ImportHandler;
use MoonShine\ImportExport\Traits\ImportExportConcern;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Fields\Text;

/**
 * @extends ModelResource<Department>
 */
class DepartmentResource extends ModelResource implements HasImportExportContract
{
    use ImportExportConcern;

    protected string $model = Department::class;

    protected string $title = 'Departments';

    protected bool $createInModal = true;

    protected bool $editInModal = true;

    protected bool $simplePaginate = true;

    protected int $itemsPerPage = 5;

    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Name', 'name') -> sortable()
        ];
    }

    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function formFields(): iterable
    {
        return [
            Box::make([
                ID::make(),
                Text::make('Name', 'name')
                    -> required()
                    -> placeholder('Enter department name'),
                Text::make('Description', 'description')
                    -> nullable()
                    ->placeholder('Enter department description')
            ])
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function detailFields(): iterable
    {
        return [
            ID::make(),
            Text::make('Name', 'name'),
            Text::make('Description', 'description') -> nullable()
        ];
    }


    protected function search(): array
    {
        return [
            'id',
            'name'
        ];
    }

    protected function rules(mixed $item): array
    {
        return [
            'name' => [
                'required',
                'string',
                'unique:departments,name,' . $item?->id,
                'min:3',
            ],
            'description' => [
                'nullable',
                'string',
            ],
        ];
    }

    protected function importFields(): iterable
    {
        return [
            ID::make(),
            Text::make('Name', 'name'),
            Text::make('Description', 'description'),
        ];
    }

    protected function import(): ?ImportHandler
    {
        return ImportHandler::make(__('moonshine::ui.import'))
            ->notifyUsers(fn() => [auth()->id()])
            ->disk('public')
            ->dir('/imports')
            ->deleteAfter()
            ->modifyButton(fn(ActionButton $btn) => $btn->class('my-class'));
    }

    protected function exportFields(): iterable
    {
        return [
            ID::make(),
            Text::make('Name', 'name'),
        ];
    }

    protected function export(): ?ExportHandler
    {
        return ExportHandler::make(__('moonshine::ui.export'))
            ->notifyUsers(fn() => [auth()->id()])
            ->disk('public')
            ->filename(sprintf('export_%s.xlsx', date('Ymd-His')))
            ->dir('/exports')
            ->withConfirm()
            ->modifyButton(fn(ActionButton $btn) => $btn->class('my-class'));
    }

}
