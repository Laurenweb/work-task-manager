<?php

// src/Twig/AppExtension.php
namespace App\Twig;

use App\Entity\GanttTask;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('is_date_checked', [$this, 'isDateChecked']),
        ];
    }

    public function getFilters()
    {
        return [
            new TwigFilter('to_hours_or_days', [$this, 'toHoursOrDays']),
        ];
    }

    public function isDateChecked(GanttTask $ganttTask, string $date)
    {
        return in_array($date, $ganttTask->getSlotArray());
    }

    public function toHoursOrDays(int $minutes) {
        if ($minutes < 60) {
            return $minutes . ' min';
        }
        if ($minutes < 1440) {
            $hours = intval($minutes / 60);
            $minutes = $minutes - $hours * 60;
            return $hours . ' h ' . $minutes . ' min';
        }

        $days = intval($minutes / 420);
        $minutes = $minutes - $days * 420;
        $hours = intval($minutes / 60);
        $minutes = $minutes - $hours * 60;
        return $days . ' j ' . $hours . ' h ' . $minutes . ' min';
    }
}
