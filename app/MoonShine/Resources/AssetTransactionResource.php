<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\AssetTransaction;

use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\ID;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Text;

/**
 * @extends ModelResource<AssetTransaction>
 */
class AssetTransactionResource extends ModelResource
{
    protected string $model = AssetTransaction::class;

    protected string $title = 'Asset Transactions';

    protected bool $createInModal = true;

    protected bool $editInModal = true;

    protected bool $simplePaginate = true;

    protected int $itemsPerPage = 5;

    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Asset ID', 'asset_id'),
            Text::make('Asset Name', 'asset.name'),
            Text::make('Transaction Type', 'transactionType.name'),
            Text::make('Details', 'details'),
            Text::make('Created At', 'created_at'),
        ];
    }

    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function formFields(): iterable
    {
        return [
            Box::make('Select Asset', [
                ID::make(),
                BelongsTo::make(
                    'Asset ID',
                    'asset',
                    formatted: 'id',
                    resource: AssetResource::class
                )
                    ->required()
                    ->searchable()
            ]) ->icon('cube'),

            Box::make('Asset Transaction Details', [
                BelongsTo::make(
                    'Transaction Type',
                    'transactionType',
                    formatted: 'name',
                    resource: TransactionTypeResource::class
                )
                    ->required()
                    ->searchable(),
                Date::make('Transaction Date', 'transaction_date')
                    ->required()
                    ->format('Y-m-d'),
                Text::make('Details', 'details') ->required()
            ]) ->icon('document-text'),
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function detailFields(): iterable
    {
        return [
            ID::make(),
            Text::make('ID del Activo', 'asset_id'),
            Text::make('Código del Activo', 'asset.code'),
            Text::make('Código de Barras del Activo', 'asset.bar_code'),
            Text::make('Nombre del Activo', 'asset.name'),
            Image::make('Imagen', 'asset.image')
                ->disk('public')
                ->dir('assets'),
            Text::make('Estado del Activo', 'asset.status', formatted: function ($value) {
                return $value ? 'Activo' : 'Inactivo';
            }),
            Text::make('Tipo de Transacción', 'transactionType.name'),
            Text::make('Detalles', 'details'),
            Date::make('Fecha de la Transacción', 'created_at'),
        ];
    }

    /**
     * @param AssetTransaction $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [];
    }
}
