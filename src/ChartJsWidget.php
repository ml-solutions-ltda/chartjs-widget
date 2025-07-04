<?php

declare(strict_types = 1);

namespace MlSolutions\ChartJsWidget;

use MlSolutions\NovaDashboard\Card\View;
use MlSolutions\NovaDashboard\Card\Widget;
use Exception;
use Illuminate\Support\Collection;
use Laravel\Nova\Http\Requests\NovaRequest;

abstract class ChartJsWidget extends Widget
{
    public string $type;

    public $component = 'chartjs-widget';

    public function title(string $title): self
    {
        return $this->withMeta([ 'title' => $title ]);
    }

    public function buttonTitle(string $title): self
    {
        return $this->withMeta([ 'buttonTitle' => $title ]);
    }

    public function backgroundColor(string $dark, ?string $light = null): self
    {
        return $this
            ->withMeta([ 'backgroundColorDark' => $dark ])
            ->withMeta([ 'backgroundColorLight' => $light ?: $dark ]);
    }

    public function disableAnimation(): self
    {
        return $this->withMeta([ 'animation' => false ]);
    }

    public function disableLegend(): self
    {
        return $this->withMeta([ 'legend' => [ 'display' => false ] ]);
    }

    public function disableTooltip(): self
    {
        return $this->withMeta([ 'tooltip' => [ 'enabled' => false ] ]);
    }

    /**
     * @see https://www.chartjs.org/docs/latest/configuration/legend.html#legend
     */
    public function legend(array $options): self
    {
        $legend = $this->getMeta('legend', []);

        return $this->withMeta([ 'legend' => array_merge_recursive($legend, $options) ]);
    }

    /**
     * @see https://www.chartjs.org/docs/latest/axes/#axes
     */
    public function scales(array $options): self
    {
        $tooltip = $this->getMeta('scales', []);

        return $this->withMeta([ 'scales' => array_merge_recursive($tooltip, $options) ]);
    }

    /**
     * @see https://www.chartjs.org/docs/latest/configuration/tooltip.html#tooltip
     */
    public function tooltip(array $options): self
    {
        $tooltip = $this->getMeta('tooltip', []);

        return $this->withMeta([ 'tooltip' => array_merge_recursive($tooltip, $options) ]);
    }

    /**
     * @see https://www.chartjs.org/docs/latest/configuration/elements.html#elements
     */
    public function elements(
        ?array $point = null,
        ?array $line = null,
        ?array $bar = null,
        ?array $arc = null,
    ): self
    {
        $elements = $this->getMeta('elements', []);

        return $this->withMeta([
            'elements' => array_merge_recursive($elements, array_filter([
                'point' => $point,
                'line' => $line,
                'bar' => $bar,
                'arc' => $arc,
            ])),
        ]);
    }

    public function icon(?string $leadingIcon = null, ?string $trailingIcon = null): self
    {
        return $this->withMeta([ 'leadingIcon' => $leadingIcon, 'trailingIcon' => $trailingIcon ]);
    }

    public function padding(?int $top = null, ?int $left = null, ?int $bottom = null, ?int $right = null): self
    {
        return $this->withMeta([
            'padding' => [
                'top' => $top,
                'left' => $left,
                'bottom' => $bottom,
                'right' => $right,
            ],
        ]);
    }

    public function addTab(string $widget): self
    {
        if (!is_subclass_of($widget, ChartJsWidget::class)) {
            throw new Exception('Please provide an class string of another ChartJs widget');
        }

        $tabs = $this->getMeta('tabs', []);

        $widget = new $widget($this->caller);

        return $this->withMeta([
            'tabs' => array_merge($tabs, [ $widget ]),
        ]);
    }

    public function resolveValue(NovaRequest $request, ?View $view = null): Collection
    {
        $value = parent::resolveValue($request, $view);

        $tabs = collect($this->getMeta('tabs'));
        $tabs = $tabs->flatMap(function (ChartJsWidget $widget) use ($request, $view) {

            $widget->configure($request);

            return $widget->resolveValue($request, $view);

        });

        $title = $this->getMeta('title') ?? $this->getMeta('buttonTitle');

        $tabs->prepend([
            'key' => $this->key(),
            'type' => $this->type,
            'title' => $title,
            'leadingIcon' => $this->getMeta('leadingIcon'),
            'trailingIcon' => $this->getMeta('trailingIcon'),
            'buttonTitle' => $this->getMeta('buttonTitle', $title),
            'padding' => $this->getMeta('padding', 10),
            'animation' => $this->getMeta('animation', true),
            'legend' => $this->getMeta('legend'),
            'tooltip' => $this->getMeta('tooltip'),
            'elements' => $this->getMeta('elements'),
            'scales' => $this->getMeta('scales'),
            'backgroundColorDark' => $this->getMeta('backgroundColorDark'),
            'backgroundColorLight' => $this->getMeta('backgroundColorLight'),
            'value' => $value,
        ]);

        $unset = [
            'tabs', 'leadingIcon', 'trailingIcon', 'title', 'buttonTitle',
            'padding', 'animation', 'legend', 'tooltip', 'elements', 'scales',
            'backgroundColorDark', 'backgroundColorLight',
        ];

        foreach ($unset as $attribute) {
            unset($this->meta[ $attribute ]);
        }

        return $tabs;
    }
}
