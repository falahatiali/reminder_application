<?php

namespace App\Scheduler;

use Cron\CronExpression;
use Cron\FieldFactory;
use Cron\FieldFactoryInterface;
use InvalidArgumentException;

class MyCronExpression extends CronExpression
{
    public const MAPPINGS = [
        '@yearly' => '0 0 1 1 *',
        '@annually' => '0 0 1 1 *',
        '@monthly' => '0 0 1 * *',
        '@weekly' => '0 0 * * 0',
        '@daily' => '0 0 * * *',
        '@midnight' => '0 0 * * *',
        '@hourly' => '0 * * * *',
        '@everyMinute' => '* * * * *',
        '@everyTwoMinutes' => '*/2 * * * *',
        '@everyThreeMinutes' => '*/3 * * * *',
        '@everyFourMinutes' => '*/4 * * * *',
        '@everyFiveMinutes' => '*/5 * * * *',
        '@everyFifteenMinutes' => '*/15 * * * *',
        '@everyThirtyMinutes' => '*/30 * * * *',
        '@everyTwoHours' => '0 */2 * * *',
        '@everyThreeHours' => '0 */3 * * *',
        '@everyFourHours' => '0 */4 * * *',
        '@everySixHours' => '0 */6 * * *',
    ];

    /**
     * Parse a CRON expression.
     *
     * @param string $expression CRON expression (e.g. '8 * * * *')
     * @param null|FieldFactoryInterface $fieldFactory Factory to create cron fields
     */
    public function __construct(string $expression, FieldFactoryInterface $fieldFactory = null)
    {
        $expression = self::MAPPINGS[$expression] ?? $expression;

        $this->fieldFactory = $fieldFactory ?: new FieldFactory();
        $this->setExpression($expression);
    }

    public static function isValidExpression(string $expression): bool
    {
        try {
            new MyCronExpression($expression);
        } catch (InvalidArgumentException $e) {
            return false;
        }

        return true;
    }

}
