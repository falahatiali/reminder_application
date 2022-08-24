<?php

namespace App\Http\Livewire\Reminder;

use App\Helpers\Date;
use App\Http\Requests\Reminder\CreateReminderRequest;
use App\Models\ReminderModel;
use App\Scheduler\MyCronExpression;
use Cron\CronExpression;
use Illuminate\Support\Arr;
use Livewire\Component;
use Livewire\WithPagination;

class Reminder extends Component
{
    use WithPagination;

    public $body;
    public $frequency;
    public $time;
    public $expression;
    public $day;
    public $date;
    public $run_once;

    public $showTime = true;
    public $showDay = true;
    public $showDate = true;

    public bool $updateMode = false;

    /**
     * @return array
     */
    public function rules(): array
    {
        return (new CreateReminderRequest)->rules();
    }

    public function render()
    {
        $reminders = ReminderModel::query()->latest()->paginate(10);
        return view('livewire.reminder.reminder', [
            'reminders' => $reminders
        ]);
    }

    public function resetInputs()
    {
        $this->body = '';
        $this->frequency = '';
        $this->date = '';
        $this->day = '';
        $this->time = '';
        $this->expression = '';
        $this->run_once = false;
    }

    public function store()
    {
        $this->validate();

        $expression = $this->buildCronExpression($this->all());

        if (MyCronExpression::isValidExpression($expression)) {
            ReminderModel::query()->create([
                'body' => $this->body,
                'frequency' => $this->frequency,
                'expression' => $expression,
                'date' => $this->date,
                'day' => $this->day,
                'time' => $this->time,
            ]);
        }
        $this->resetInputs();

        $this->render();
    }

    public function edit($id)
    {

    }

    public function update()
    {

    }

    public function delete($id)
    {

    }

    public function buildCronExpression(array $params)
    {
        if (isset($params['time']) && $params['time'] != "") {
            list($hour, $minute) = explode(':', $params['time']);
        }

        $value = Date::allMappings()[$params['frequency']];
        $expression = new MyCronExpression($value);

        if (isset($hour) && isset($minute)) {
            $expression = explode(' ', $expression);
            $expression[0] = $minute;
            $expression[1] = $hour;
            $expression = implode(' ', $expression);
        }
        if (isset($params['day'])) {
            $expression = explode(' ', $expression);
            $expression[4] = $params['day'];
            $expression = implode(' ', $expression);
        }
        if (isset($params['date'])) {
            $expression = explode(' ', $expression);
            $expression[2] = $params['date'];
            $expression = implode(' ', $expression);
        }

        if (!is_array($expression) && !is_string($expression)) {
            $expression = $expression->getParts();
            $expression = implode(' ', $expression);
        }

        return $expression;
    }

    public function changeFrequencyValue($value)
    {
        $paramRequiredList = [
            'yearly',
            'annually',
            'monthly',
            'weekly',
            'daily',
        ];

        if (in_array($value, $paramRequiredList)) {
            $this->showDay = true;
            $this->showDate = true;
            $this->showTime = true;
        } else {
            $this->showDay = false;
            $this->showDate = false;
            $this->showTime = false;
        }
    }
}
