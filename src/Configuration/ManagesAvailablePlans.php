<?php

namespace Laravel\Spark\Configuration;

use Illuminate\Support\Collection;
use Laravel\Spark\Plan;
use Laravel\Spark\TeamPlan;

trait ManagesAvailablePlans
{
    /**
     * Indicates that the application will bill customers.
     *
     * @var bool
     */
    public static bool $billsCustomers = false;

    /**
     * Indicates that the application will bill teams.
     *
     * @var bool
     */
    public static bool $billsTeams = false;

    /**
     * The coupon code for the current application wide promotion.
     *
     * @var string
     */
    public static string $promotion;

    /**
     * The number of days to grant to generic trials.
     *
     * @var int
     */
    public static int $trialDays;

    /**
     * The number of days to grant to generic team trials.
     *
     * @var int
     */
    public static int $teamTrialDays;

    /**
     * All the plans defined for the application.
     *
     * @var array
     */
    public static array $plans = [];

    /**
     * All the team plans defined for the application.
     *
     * @var array
     */
    public static array $teamPlans = [];

    /**
     * Indicates that the application will bill customers.
     *
     * @return void
     */
    public static function billsCustomers(): void
    {
        static::$billsCustomers = true;
    }

    /**
     * Determine if the application bills customers.
     *
     * @return bool
     */
    public static function canBillCustomers(): bool
    {
        return static::hasPaidPlans() || static::$billsCustomers;
    }

    /**
     * Indicates that the application will bill teams.
     *
     * @return void
     */
    public static function billsTeams(): void
    {
        static::$billsTeams = true;
    }

    /**
     * Determine if the application bills teams.
     *
     * @return bool
     */
    public static function canBillTeams(): bool
    {
        return static::hasPaidTeamPlans() || static::$billsTeams;
    }

    /**
     * Define or retrieve an application wide promotion for new registrations.
     *
     * @param string|null $coupon
     * @return static|string
     */
    public static function promotion(string $coupon = null): string|static
    {
        if (is_null($coupon)) {
            return static::$promotion;
        }

        static::$promotion = $coupon;

        return new static;
    }

    /**
     * Get or set the number of days for the generic trial.
     *
     * @param int|null $trialDays
     * @return static|int
     */
    public static function trialDays(int $trialDays = null): int|static
    {
        if (is_null($trialDays)) {
            return static::$trialDays;
        }

        static::$trialDays = $trialDays;

        return new static;
    }

    /**
     * Get or set the number of days for the generic team trial.
     *
     * @param int|null $teamTrialDays
     * @return static|int
     */
    public static function teamTrialDays(int $teamTrialDays = null): int|static
    {
        if (is_null($teamTrialDays)) {
            return static::$teamTrialDays;
        }

        static::$teamTrialDays = $teamTrialDays;

        return new static;
    }

    /**
     * Create a new free plan instance.
     *
     * @param string $name
     * @return Plan
     */
    public static function freePlan(string $name = 'Free'): Plan
    {
        return static::plan($name, 'free');
    }

    /**
     * Create a new free team plan instance.
     *
     * @param string $name
     * @return TeamPlan
     */
    public static function freeTeamPlan(string $name = 'Free'): TeamPlan
    {
        return static::teamPlan($name, 'free');
    }

    /**
     * Create a new plan instance.
     *
     * @param string $name
     * @param string $id
     * @return Plan
     */
    public static function plan(string $name, string $id): Plan
    {
        static::$plans[] = $plan = new Plan($name, $id);

        return $plan;
    }

    /**
     * Create a new team plan instance.
     *
     * @param string $name
     * @param string $id
     * @return TeamPlan
     */
    public static function teamPlan(string $name, string $id): TeamPlan
    {
        static::$teamPlans[] = $plan = new TeamPlan($name, $id);

        return $plan;
    }

    /**
     * Determine if paid plans are defined for the application.
     *
     * @return bool
     */
    public static function hasPaidPlans(): bool
    {
        return count(static::plans()->filter(function ($plan) {
            return $plan->price > 0;
        })) > 0;
    }

    /**
     * Determine if active yearly plans are defined.
     *
     * @return bool
     */
    public static function hasYearlyPlans(): bool
    {
        return static::plans()->filter(function ($plan) {
            return $plan->interval === 'yearly';
        })->count() > 0;
    }

    /**
     * Get the active plans defined for the application.
     *
     * @return Collection
     */
    public static function activePlans(): Collection
    {
        return static::plans()->filter(function ($plan) {
            return $plan->active;
        });
    }

    /**
     * Get the plans defined for the application.
     *
     * @return Collection
     */
    public static function plans(): Collection
    {
        return collect(static::$plans)->map(function ($plan) {
            $plan->type = 'user';

            return $plan;
        });
    }

    /**
     * Get an array of all the active plan IDs.
     *
     * @return array
     */
    public static function activePlanIds(): array
    {
        return static::activePlans()->pluck('id')->all();
    }

    /**
     * Get a comma-delimited list of active Spark plan IDs.
     *
     * @return string
     */
    public static function activePlanIdList(): string
    {
        return implode(',', static::activePlanIds());
    }

    /**
     * Determine if paid team plans are defined for the application.
     *
     * @return bool
     */
    public static function hasPaidTeamPlans(): bool
    {
        return count(static::teamPlans()->filter(function ($plan) {
            return $plan->price > 0;
        })) > 0;
    }

    /**
     * Determine if active team yearly plans are defined.
     *
     * @return bool
     */
    public static function hasYearlyTeamPlans(): bool
    {
        return static::teamPlans()->filter(function ($plan) {
            return $plan->interval === 'yearly';
        })->count() > 0;
    }

    /**
     * Get the active team plans defined for the application.
     *
     * @return Collection
     */
    public static function activeTeamPlans(): Collection
    {
        return static::teamPlans()->filter(function ($plan) {
            return $plan->active;
        });
    }

    /**
     * Get the team plans defined for the application.
     *
     * @return Collection
     */
    public static function teamPlans(): Collection
    {
        return collect(static::$teamPlans)->map(function ($plan) {
            $plan->type = 'team';

            return $plan;
        });
    }

    /**
     * Get an array of all the active team plan IDs.
     *
     * @return array
     */
    public static function activeTeamPlanIds(): array
    {
        return static::activeTeamPlans()->pluck('id')->all();
    }

    /**
     * Get a comma-delimited list of active Spark team plan IDs.
     *
     * @return string
     */
    public static function activeTeamPlanIdList(): string
    {
        return implode(',', static::activeTeamPlanIds());
    }

    /**
     * Determine if the application has team plans only.
     *
     * @return bool
     */
    public static function onlyTeamPlans(): bool
    {
        return static::plans()->isEmpty() && ! static::teamPlans()->isEmpty();
    }

    /**
     * Get all the plans, both individual and teams.
     *
     * @return Collection
     */
    public static function allPlans(): Collection
    {
        return static::plans()->merge(static::teamPlans());
    }

    /**
     * Get all the monthly plans, both individual and teams.
     *
     * @return Collection
     */
    public static function allMonthlyPlans(): Collection
    {
        return collect(array_merge(
            static::plans()->where('interval', 'monthly')->all(),
            static::teamPlans()->where('interval', 'monthly')->all()
        ));
    }

    /**
     * Get all the yearly plans, both individual and teams.
     *
     * @return Collection
     */
    public static function allYearlyPlans(): Collection
    {
        return collect(array_merge(
            static::plans()->where('interval', 'yearly')->all(),
            static::teamPlans()->where('interval', 'yearly')->all()
        ));
    }
}
