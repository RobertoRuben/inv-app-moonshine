<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Employee;

use MoonShine\ImportExport\Contracts\HasImportExportContract;
use MoonShine\ImportExport\ExportHandler;
use MoonShine\ImportExport\ImportHandler;
use MoonShine\ImportExport\Traits\ImportExportConcern;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\FieldsGroup;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Fields\ID;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Components\Tabs\Tab;

/**
 * @extends ModelResource<Employee>
 */
class EmployeeResource extends ModelResource implements HasImportExportContract
{
    use ImportExportConcern;

    protected string $model = Employee::class;

    protected string $title = 'Employees';

    protected bool $createInModal = true;

    protected bool $editInModal = true;

    protected bool $simplePaginate = true;

    protected int $itemsPerPage = 5;

    protected bool $columnSelection = true;

    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Dni', 'dni')->sortable(),
            Text::make('Paternal Surname', 'paternal_surname')->sortable(),
            Text::make('Maternal Surname', 'maternal_surname')->sortable(),
            Text::make('Names', 'names')->sortable(),
            Text::make('Email', 'email'),
            Text::make('Phone', 'phone'),
            Text::make('Department', 'department.name')->sortable(),
            Text::make('Position', 'position.title')->sortable(),
        ];
    }

    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function formFields(): iterable
    {
        return [
            Box::make([
                Tabs::make([
                    Tab::make('Personal Information', [
                        FieldsGroup::make([
                            ID::make(),
                            Text::make('Dni', 'dni')
                                ->required()
                                ->placeholder('Input employee dni'),
                            Text::make('Names', 'names')
                                ->required()
                                ->placeholder('Input employee names'),
                            Text::make('Paternal Surname', 'paternal_surname')
                                ->required()
                                ->placeholder('Input employee paternal surname'),
                            Text::make('Maternal Surname', 'maternal_surname')
                                ->required()
                                ->placeholder('Input employee maternal surname'),
                            Text::make('Email', 'email')
                                ->required()
                                ->placeholder('Input employee email'),
                            Text::make('Phone', 'phone')
                                ->required()
                                ->placeholder('Input employee phone'),
                        ]),
                    ])->icon('user'),

                    Tab::make('Organizational Information', [
                        FieldsGroup::make([
                            BelongsTo::make(
                                'Department',
                                'department',
                                formatted: 'name',
                                resource: DepartmentResource::class
                            )
                                ->required()
                                ->searchable()
                                ->placeholder('Select department'),
                            BelongsTo::make(
                                'Position',
                                'position',
                                formatted: 'title',
                                resource: PositionResource::class
                            )
                                ->required()
                                ->searchable()
                                ->placeholder('Select position'),
                        ]),
                    ])->icon('briefcase'),
                ]),
            ]),
        ];
    }
    /**
     * @return list<FieldContract>
     */
    protected function detailFields(): iterable
    {
        return [
            ID::make(),
            Text::make('Dni', 'dni'),
            Text::make('Paternal Surname', 'paternal_surname'),
            Text::make('Maternal Surname', 'maternal_surname'),
            Text::make('Names', 'names'),
            Text::make('Email', 'email'),
            Text::make('Phone', 'phone'),
            Text::make('Department', 'department.name'),
            Text::make('Position', 'position.title'),
        ];
    }

    /**
     * @param Employee $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [
            'dni' => ['required', 'string', 'regex:/^\d{8}$/', 'unique:employees,dni,' . $item?->id],
            'names' => ['required', 'string', 'max:255'],
            'paternal_surname' => ['required', 'string', 'max:255'],
            'maternal_surname' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'regex:/^[^@]+@(gmail\.com|hotmail\.com|outlook\.com)$/i',
                'unique:employees,email,' . $item?->id
            ],
            'phone' => ['required', 'string', 'regex:/^\d{9}$/'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'position_id' => [
                'required',
                'integer',
                'exists:positions,id',
                'unique:employees,position_id,' . $item?->id
            ],
        ];
    }

    protected function search():array
    {
        return [
            'id',
            'dni',
            'names',
        ];
    }

    protected function filters(): iterable
    {
        return [
            BelongsTo::make(
                'Department',
                'department',
                formatted: 'name',
                resource: DepartmentResource::class
            ),
            BelongsTo::make(
                'Position',
                'position',
                formatted: 'title',
                resource: PositionResource::class
            ),
        ];
    }

    protected function importFields(): iterable
    {
        return [
            ID::make(),
            Text::make('Dni', 'dni'),
            Text::make('Names', 'names'),
            Text::make('Paternal Surname', 'paternal_surname'),
            Text::make('Maternal Surname', 'maternal_surname'),
            Text::make('Email', 'email'),
            Text::make('Phone', 'phone'),
            BelongsTo::make(
                'Department',
                'department',
                formatted: 'name',
                resource: DepartmentResource::class
            ),
            BelongsTo::make(
                'Position',
                'position',
                formatted: 'title',
                resource: PositionResource::class
            ),
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
            Text::make('Dni', 'dni'),
            Text::make('Names', 'names'),
            Text::make('Paternal Surname', 'paternal_surname'),
            Text::make('Maternal Surname', 'maternal_surname'),
            Text::make('Email', 'email'),
            Text::make('Phone', 'phone'),
            Text::make('Department', 'department.name'),
            Text::make('Position', 'position.title'),
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
