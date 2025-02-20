<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Asset;

use App\Models\AssetCategory;
use App\Models\Brand;
use App\Models\Department;
use Illuminate\Database\Eloquent\Builder;
use MoonShine\ImportExport\Contracts\HasImportExportContract;
use MoonShine\ImportExport\ExportHandler;
use MoonShine\ImportExport\ImportHandler;
use MoonShine\ImportExport\Traits\ImportExportConcern;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\QueryTags\QueryTag;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\ID;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;

/**
 * @extends ModelResource<Asset>
 */
class AssetResource extends ModelResource implements HasImportExportContract
{
    use ImportExportConcern;

    protected string $model = Asset::class;

    protected string $title = 'Assets';

    protected bool $createInModal = true;

    protected bool $editInModal = true;

    protected bool $simplePaginate = true;

    protected int $itemsPerPage = 5;

    protected bool $columnSelection = true;

    protected bool $saveQueryState = true;

    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Bar Code', 'bar_code'),
            Text::make('Code', 'code'),
            Text::make('Label', 'label'),
            Text::make('Name', 'name'),
            Image::make('Imagen', 'image')
                ->disk('public')
                ->dir('assets')
                ->nullable(),
            Text::make('Category', 'category.name'),
            Date::make('Acquisition Date', 'acquisition_date')
                ->format('Y-m-d'),
            Switcher::make('Status', 'status')
                ->onValue(1)
                ->offValue(0)
                ->withUpdateRow($this->getListComponentName()),

            Text::make('Quantity', 'quantity'),
            Text::make('Updated At', 'updated_at')->sortable(),
        ];
    }

    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function formFields(): iterable
    {
        return [
            Box::make('Asset Overview', [
                ID::make(),

                Text::make('Bar Code', 'bar_code')
                    ->placeholder('Enter bar code')
                    ->required(),

                Text::make('Code', 'code')
                    ->placeholder('Enter the code')
                    ->required(),

                Text::make('Label', 'label')
                    ->placeholder('Enter the label')
                    ->required(),

                Text::make('Name', 'name')
                    ->placeholder('Enter the name')
                    ->required(),
            ]) ->icon('information-circle'),

            Box::make('Asset Details', [
                Date::make('Acquisition Date', 'acquisition_date')
                    ->placeholder('YYYY-MM-DD')
                    ->required()
                    ->format('YYYY-MM-DD'),

                Select::make('Status', 'status')
                    ->options([
                        false => 'Inactive',
                        true  => 'Active',
                    ])
                    ->placeholder('Select status')
                    ->default(false),

                Number::make('Quantity', 'quantity')
                    ->placeholder('Enter quantity')
                    ->min(1)
                    ->required(),

                Image::make('Imagen', 'image')
                    ->disk('public')
                    ->dir('assets')
                    ->nullable(),
            ]) ->icon('adjustments-horizontal'),

            Box::make('Associations', [
                BelongsTo::make(
                    'Category',
                    'category',
                    formatted: 'name',
                    resource: AssetCategoryResource::class
                )
                    ->nullable()
                    ->required()
                    ->placeholder('Select category')
                    ->searchable(),

                BelongsTo::make(
                    'Location',
                    'department',
                    formatted: 'name',
                    resource: DepartmentResource::class
                )
                    ->nullable()
                    ->required()
                    ->placeholder('Select Location')
                    ->searchable(),

                BelongsTo::make(
                    'Brand',
                    'brand',
                    formatted: 'name',
                    resource: BrandResource::class
                )
                    ->nullable()
                    ->required()
                    ->placeholder('Select brand')
                    ->searchable(),
            ]) ->icon('link'),
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function detailFields(): iterable
    {
        return [
            ID::make(),
            Text::make('Bar Code', 'bar_code'),
            Text::make('Code', 'code'),
            Text::make('Label', 'label'),
            Text::make('Name', 'name'),
            Date::make('Acquisition Date', 'acquisition_date')
                ->format('Y-m-d'),
            Select::make('Status', 'status')
                ->options([
                    false => 'Inactive',
                    true  => 'Active',
                ]),
            Number::make('Quantity', 'quantity'),
            Image::make('Imagen', 'image')
                ->disk('public')
                ->dir('assets'),
            Text::make('Brand', 'brand.name'),
            Text::make('Category', 'category.name'),
            Text::make('Location', 'department.name'),


        ];
    }

    protected function rules(mixed $item): array
    {
        return [
            'bar_code' => ['required', 'string', 'max:13', 'min:1', 'unique:assets,bar_code,' . $item?->id],
            'code' => ['required', 'numeric', 'max:999999999999999', 'min:1', 'unique:assets,code,' . $item?->id],
            'label'    => ['required', 'string', 'max:255'],
            'name'     => ['required', 'string', 'max:255'],
            'acquisition_date' => ['required', 'date'],
            'status'   => ['boolean'],
            'quantity' => ['required', 'integer', 'min:1'],
            'image' => ['nullable', 'image', 'max:2048'],
        ];
    }

    protected function search():array
    {
        return [
            'id',
            'bar_code',
            'code',
            'label',
        ];

    }
    public function queryTags(): array
    {
        return [
            QueryTag::make(
                'Active',
                fn(Builder $query) => $query->where('status', true)
            )->icon('check-circle'),

            QueryTag::make(
                'Inactive',
                fn(Builder $query) => $query->where('status', false)
            )->icon('x-circle'),

        ];
    }

    protected function filters(): iterable
    {
        return [
            Select::make('Status', 'status')
                ->options([
                    ''  => 'All statuses',
                    '1' => 'Active',
                    '0' => 'Inactive',
                ])
                ->nullable()
                ->onApply(function (Builder $query, $value) {
                    if ($value === '' || is_null($value)) {
                        return $query;
                    }

                    return $query->where('status', (bool) $value);
                }),

            BelongsTo::make(
                'Category',
                'category',
                formatted: 'name',
                resource: AssetCategoryResource::class
            )
                ->nullable()
                ->placeholder('Select the category to filter')
                ->searchable()
                ->onApply(function (Builder $query, $value) {
                    if (!$value) {
                        return $query;
                    }
                    return $query->where('asset_category_id', $value);
                }),

            BelongsTo::make(
                'Location',
                'department',
                formatted: 'name',
                resource: DepartmentResource::class
            )
                ->nullable(true)
                ->placeholder('Select the location to filter')
                ->searchable()
                ->onApply(function (Builder $query, $value) {
                    if (!$value) {
                        return $query;
                    }
                    return $query->where('department_id', $value);
                }),
        ];
    }

    protected function importFields(): iterable
    {
        return [
            ID::make(),
            Text::make('Bar Code', 'bar_code'),
            Text::make('Code', 'code'),
            Text::make('Label', 'label'),
            Text::make('Name', 'name'),
            Text::make('Acquisition Date', 'acquisition_date'),
            Select::make('Status', 'status')
                ->options([
                    0 => 'Inactive',
                    1 => 'Active',
                ])
                ->fromRaw(fn(string $value) => $value === 'Active' ? 1 : ($value === 'Inactive' ? 0 : $value)),

            Number::make('Quantity', 'quantity'),

            BelongsTo::make(
                'Category',
                'asset_category',
                formatted: 'name',
                resource: AssetCategoryResource::class
            )
                ->fromRaw(fn(string $name) => AssetCategory::query()->where('name', $name)->value('id')),

            BelongsTo::make(
                'Location',
                'department',
                formatted: 'name',
                resource: DepartmentResource::class
            )
                ->fromRaw(fn(string $name) => Department::query()->where('name', $name)->value('id')),

            BelongsTo::make(
                'Brand',
                'brand',
                formatted: 'name',
                resource: BrandResource::class
            )
                ->fromRaw(fn(string $name) => Brand::query()->where('name', $name)->value('id')),
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
            Text::make('Bar Code', 'bar_code'),
            Text::make('Code', 'code'),
            Text::make('Label', 'label'),
            Text::make('Name', 'name'),
            Text::make('Acquisition Date', 'acquisition_date'),
            Select::make('Status', 'status')
                ->options([
                    false => 'Inactive',
                    true  => 'Active',
                ]),
            Number::make('Quantity', 'quantity'),
            Text::make('Brand', 'brand.name'),
            Text::make('Category', 'category.name'),
            Text::make('Location', 'department.name'),
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
