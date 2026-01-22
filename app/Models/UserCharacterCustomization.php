<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCharacterCustomization extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'head_part_id',
        'body_part_id',
        'legs_part_id',
        'arms_part_id',
    ];

    /**
     * Get the user that owns this customization
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the head appearance part
     */
    public function headPart()
    {
        return $this->belongsTo(TribeAppearancePart::class, 'head_part_id');
    }

    /**
     * Get the body appearance part
     */
    public function bodyPart()
    {
        return $this->belongsTo(TribeAppearancePart::class, 'body_part_id');
    }

    /**
     * Get the legs appearance part
     */
    public function legsPart()
    {
        return $this->belongsTo(TribeAppearancePart::class, 'legs_part_id');
    }

    /**
     * Get the arms appearance part
     */
    public function armsPart()
    {
        return $this->belongsTo(TribeAppearancePart::class, 'arms_part_id');
    }

    /**
     * Get all parts as array
     */
    public function getAllParts()
    {
        return [
            'head' => $this->headPart,
            'body' => $this->bodyPart,
            'legs' => $this->legsPart,
            'arms' => $this->armsPart,
        ];
    }

    /**
     * Get part by type with fallback to default
     */
    public function getPartByType($type, $tribeId)
    {
        $partIdField = $type . '_part_id';
        $part = $this->{$type . 'Part'};

        // If no part set, get default for tribe
        if (!$part) {
            $part = TribeAppearancePart::forTribe($tribeId)
                ->ofType($type)
                ->default()
                ->active()
                ->first();
        }

        return $part;
    }
}
