<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use App\Models\Asset;
use App\Models\AssetCategory;
use MoonShine\Apexcharts\Components\DonutChartMetric;
use MoonShine\Apexcharts\Components\LineChartMetric;
use MoonShine\Laravel\Pages\Page;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Components\Card;
use MoonShine\UI\Components\Heading;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Layout\LineBreak;
use MoonShine\UI\Components\Metrics\Wrapped\ValueMetric;

class Dashboard extends Page
{
    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        return [
            '#' => $this->getTitle()
        ];
    }

    public function getTitle(): string
    {
        return $this->title ?: 'Dashboard';
    }

    /**
     * @return list<ComponentContract>
     */
    protected function components(): iterable
	{
        return [
            Heading::make('Welcome to Inventory App', 1),
            Heading::make('Dashboard', 2),
            LineBreak::make(),

            Grid::make([
                Column::make([
                    ValueMetric::make('Total Assets')
                        ->value(Asset::count()),
                ])->columnSpan(4),

                Column::make([
                    ValueMetric::make('Active Assets')
                        ->value(Asset::where('status', true)->count()),
                ])->columnSpan(4),

                Column::make([
                    ValueMetric::make('Inactive Assets')
                        ->value(Asset::where('status', false)->count()),
                ])->columnSpan(4),
            ]) ,

            LineBreak::make(),

            Grid::make([
                Column::make([
                    DonutChartMetric::make('Assets by Category')
                        ->values(function() {
                            $data = AssetCategory::withCount('assets')->get();
                            return $data->pluck('assets_count', 'name')->toArray();
                        }),
                ])->columnSpan(6),

                Column::make([
                    LineChartMetric::make('Assets Over Time')
                        ->line(function() {
                            $data = Asset::query()
                                ->selectRaw("DATE(acquisition_date) as date, COUNT(*) as count")
                                ->groupBy('date')
                                ->orderBy('date')
                                ->pluck('count', 'date')
                                ->toArray();
                            return ['Assets' => $data];
                        }),
                ])->columnSpan(6),
            ]),
        ];
	}
}
