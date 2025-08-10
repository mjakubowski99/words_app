<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property        int                                                  $id
 * @property        null|string                                          $user_id
 * @property        null|string                                          $email
 * @property        string                                               $type
 * @property        string                                               $description
 * @property        null|string                                          $reportable_id
 * @property        null|string                                          $reportable_type
 * @property        null|Carbon                                          $created_at
 * @property        null|Carbon                                          $updated_at
 * @method   static \Database\Factories\ReportFactory                    factory($count = null, $state = [])
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|Report newModelQuery()
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|Report newQuery()
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|Report query()
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|Report whereCreatedAt($value)
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|Report whereDescription($value)
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|Report whereEmail($value)
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|Report whereId($value)
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|Report whereReportableId($value)
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|Report whereReportableType($value)
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|Report whereType($value)
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|Report whereUpdatedAt($value)
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|Report whereUserId($value)
 * @mixin \Eloquent
 */
class Report extends Model
{
    use HasFactory;
}
