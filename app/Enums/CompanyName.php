<?php

namespace App\Enums;

enum CompanyName: int
{
    case HCL = 1;
    case ABCO = 51;
    case Glogo = 101;
    case HIL = 151;

    public function label(): string
    {
        return match ($this) {
            self::HCL => 'HCL',
            self::ABCO => 'ABCO',
            self::Glogo => 'Glogo',
            self::HIL => 'HIL',
        };
    }

    /**
     * Get the enum instance from a label.
     *
     * @param  string  $label
     * @return Status|null
     */
    public static function fromLabel(string $label): ?self
    {
        return match ($label) {
            'HCL' => self::HCL,
            'ABCO' => self::ABCO,
            'Glogo' => self::Glogo,
            'HIL' => self::HIL,
            default => null,
        };
    }
}
