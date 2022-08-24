<div>
    <form wire:submit.prevent="store" method="POST">
        <div class="row">
            <div class="form-group mb-2 col">
                <label for="body" class="sr-only">Reminder Body</label>
                <input type="text" class="form-control mt-1" id="body" placeholder="Body"
                       wire:model.defer="body">
                @error('body') <span class="text-danger">{{ $message }}</span>@enderror
            </div>
            <div class="form-group mb-2 col">
                <label for="frequency">Reminder Frequency ...</label>
                <select name="frequency" id="frequency" class="form-control mt-1" wire:model.defer="frequency"
                        wire:change="changeFrequencyValue($event.target.value)">
                    <option value="0">Select Frequency</option>
                    @foreach(\App\Helpers\Date::frequencies() as $key => $fr)
                        <option value="{{ $key }}">{{ $fr }}</option>
                    @endforeach
                </select>
                @error('frequency') <span class="text-danger">{{ $message }}</span>@enderror
            </div>

            <div class="form-group mb-2 col">
                <label for="day">Day</label>
                <select name="day" id="day" class="form-control mt-1" wire:model.defer="day"
                    {{ $showDay === false ? 'disabled' : '' }}>
                    <option value="">Select the day ...</option>
                    @foreach(\App\Helpers\Date::days() as $key => $day)
                        <option value="{{ $key }}">{{ $day }}</option>
                    @endforeach
                </select>
                @error('day') <span class="text-danger">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="row mt-3">
            <div class="form-group mb-2 col">
                <label for="date" class="sr-only">Date</label>
                <select name="date" id="date" class="form-control mt-1" wire:model.defer="date"
                    {{ $showDate === false ? 'disabled' : '' }}>
                    <option value="">Select Date ...</option>
                    @foreach(range(1,31) as $val)
                        <option value="{{ $val }}">{{ \App\Helpers\Date::ordinal($val) }}</option>
                    @endforeach
                </select>
                @error('date') <span class="text-danger">{{ $message }}</span>@enderror
            </div>

            <div class="form-group mb-2 col">
                <label for="time" class="sr-only">Time</label>
                <select name="time" id="time" class="form-control mt-1" wire:model.defer="time"
                    {{ $showTime === false ? 'disabled' : '' }}>
                    <option value="">Select Time ...</option>
                    @foreach(\App\Helpers\Date::range('00:00' ,'24:00') as $datetime)
                        <option value="{{ $datetime->format('H:i') }}">{{ $datetime->format('H:i') }}</option>
                    @endforeach
                </select>
                @error('time') <span class="text-danger">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="align-content-center">
            <button class="btn btn-lg btn-outline-success btn-toolbar mb-2">Create new Reminder</button>
        </div>
    </form>
</div>
