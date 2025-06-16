<?php

declare(strict_types = 1);

namespace MlSolutions\ChartJsWidget\Charts;

use MlSolutions\ChartJsWidget\ChartJsWidget;

abstract class PieChartWidget extends ChartJsWidget
{
    public string $type = 'pie';
}
