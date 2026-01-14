<?php
/**
 * Eloquent model for SPID transaction logs.
 *
 * @license BSD-3-clause
 */

namespace Italia\SPIDAuth\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SPIDTransaction extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'spid_transactions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'idp',
        'authn_request_id',
        'authn_request_issue_instant',
        'authn_request_xml',
        'response_id',
        'response_issue_instant',
        'response_issuer',
        'response_xml',
        'assertion_id',
        'assertion_subject',
        'assertion_subject_name_qualifier',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'authn_request_issue_instant' => 'datetime',
        'response_issue_instant' => 'datetime',
    ];

    /**
     * Scope a query to only include transactions older than a given date.
     *
     * @param Builder $query
     * @param Carbon $date
     *
     * @return Builder
     */
    public function scopeOlderThan(Builder $query, Carbon $date): Builder
    {
        return $query->where('created_at', '<', $date);
    }
}
