<?php

namespace Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Shared\Enum\ReportableType;

class Report extends Model
{
    public function getReportableId(): ?string
    {
        return $this->reportable_id;
    }

    public function getReportableType(): ?ReportableType
    {
        if (!$this->reportable_type) {
            return null;
        }

        try {
            return ReportableType::from($this->reportable_type);
        } catch (\Throwable $exception) {
            return ReportableType::UNKNOWN;
        }
    }
}