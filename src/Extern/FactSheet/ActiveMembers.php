<?php

declare(strict_types=1);

namespace Francken\Extern\FactSheet;

use Illuminate\Support\Collection;

final class ActiveMembers
{
    private Collection $members;

    public function __construct(Collection $members)
    {
        $this->members = $members;
    }

    public function total() : int
    {
        return $this->members->count();
    }

    public function internationals() : int
    {
        return $this->members->where('is_nederlands', 0)
            ->where('nederlands', 0)
            ->count();
    }

    public function studies() : Collection
    {
        $studies = $this->members->groupBy('studierichting')
            ->map(function ($students, $study) : StudyStatistic {
                return new class($study, $students->count()) implements StudyStatistic {
                    private string $study;
                    private int $amount;
                    public function __construct(string $study, int $amount)
                    {
                        $this->study = $study;
                        $this->amount = $amount;
                    }

                    public function study() : string
                    {
                        return $this->study;
                    }

                    public function amount() : int
                    {
                        return $this->amount;
                    }

                    /*
                     * Used to create "Total" and "Other" statistics
                     */
                    public static function fromMultipleStatistics(string $name, StudyStatistic ...$others) : StudyStatistic
                    {
                        return new self(
                            $name,
                            collect($others)->reduce(
                                fn ($amount, StudyStatistic $study) => $amount + $study->amount(),
                                0
                            )
                        );
                    }
                };
            });
        return (new StudiesStatistic(...$studies->values()))->studies();
    }

    public function genders() : Collection
    {
        return $this->members->groupBy('geslacht');
    }
}
