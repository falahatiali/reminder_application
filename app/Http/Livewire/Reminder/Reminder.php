<?php

namespace App\Http\Livewire\Reminder;

use App\Helpers\Date;
use App\Http\Requests\Reminder\CreateReminderRequest;
use App\Library\Reminder\ReminderTypes;
use App\Models\ReminderModel;
use App\Scheduler\MyCronExpression;
use Livewire\Component;
use Livewire\WithPagination;

class Reminder extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $selected_id;
    public $body;
    public $frontend;
    public $backend;
    public $additional_text;
    public $reminder_type = ReminderTypes::LEITNER_BOX;
    public $frequency;
    public $time;
    public $expression;
    public $day;
    public $date;
    public $run_once;
    public $active;

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
        $reminders = ReminderModel::query()->latest()->paginate(request()->get('perPage') ?? 10);
        return view('livewire.reminder.reminder', [
            'reminders' => $reminders
        ]);
    }

    public function resetInputs()
    {
        $this->body = '';
        $this->frequency = '';
        $this->frontend = '';
        $this->backend = '';
        $this->additional_text = '';
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
            auth()->user()->reminders()->create([
                'body' => $this->body,
                'frontend' => $this->frontend,
                'backend' => $this->backend,
                'additional_text' => $this->additional_text,
                'frequency' => $this->frequency,
                'expression' => $expression,
                'date' => $this->date,
                'day' => $this->day,
                'time' => $this->time,
            ]);
        }
        $this->resetInputs();
    }

    public function edit($id)
    {
        $reminder = ReminderModel::query()->find($id);

        $this->body = $reminder->body;
        $this->selected_id = $reminder->id;
        $this->frontend = $reminder->frontend;
        $this->backend = $reminder->backend;
        $this->additional_text = $reminder->additional_text;
        $this->reminder_type = $reminder->reminder_type;
        $this->frequency = array_keys(Date::frequencies(), $reminder->frequency)[0];
        $this->time = $reminder->time;
        $this->expression = $reminder->expression;
        $this->day = array_keys(Date::days(), $reminder->day)[0] ?? $reminder->day;
        $this->date = $reminder->date;
        $this->run_once = $reminder->run_once;
        $this->active = $reminder->active;
        $this->changeFrequencyValue($reminder->frequency);
        $this->updateMode = true;
    }

    public function update()
    {
        if ($this->selected_id) {
            $reminder = ReminderModel::query()->find($this->selected_id);
            $expression = $this->buildCronExpression($this->all());
            $reminder->update([
                'body' => $this->body,
                'frontend' => $this->frontend,
                'backend' => $this->backend,
                'additional_text' => $this->additional_text,
                'frequency' => $this->frequency,
                'expression' => $expression,
                'date' => $this->date,
                'day' => $this->day,
                'time' => $this->time,
            ]);

            $this->resetInputs();
            $reminder->refresh();
            $this->updateMode = false;
        }

        $this->render();
    }

    public function delete($id)
    {
        $reminder = ReminderModel::query()->find($id);
        $reminder?->delete();
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

    public function changeStatus($id)
    {
        $reminder = ReminderModel::query()->find($id);
        $reminder->update([
            'active' => !$reminder->active
        ]);

        return true;
    }
}
