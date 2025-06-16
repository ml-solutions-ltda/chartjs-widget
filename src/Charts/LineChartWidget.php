<?php

declare(strict_types = 1);

namespace MlSolutions\ChartJsWidget\Charts;

use MlSolutions\ChartJsWidget\ChartJsWidget;

abstract class LineChartWidget extends ChartJsWidget
{
    public string $type = 'line';
}
