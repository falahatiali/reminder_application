<div>
    <form wire:submit.prevent="update" method="PATCH">
        <input type="hidden" wide:model="selected_id">
        <div class="row">
            <div class="form-group mb-2 col">
                <label for="frontend" class="sr-only">Front of the card</label>
                <input type="text" class="form-control mt-1" id="frontend" placeholder="Front"
                       wire:model.defer="frontend">
                @error('frontend') <span class="text-danger">{{ $message }}</span>@enderror
            </div>
            <div class="form-group mb-2 col">
                <label for="backend" class="sr-only">Backend of the card</label>
                <input type="text" class="form-control mt-1" id="backend" placeholder="Backend"
                       wire:model.defer="backend">
                @error('backend') <span class="text-danger">{{ $message }}</span>@enderror
            </div>

            <div class="form-group mb-2 col">
                <label for="frequency">Reminder Frequency ...</label>
                <select name="frequency" id="frequency" class="form-control mt-1" wire:model.defer="frequency"
                        wire:change="changeFrequencyValue($event.target.value)">
                    <option value="0">Select Frequency</option>
                    @foreach(\App\Helpers\Date::frequencies() as $key => $fr)
                        <option value="{{ $key }}" @if($key === $frequency) selected @endif>
                            {{ $fr }}
                        </option>
                    @endforeach
                </select>
                @error('frequency') <span class="text-danger">{{ $message }}</span>@enderror
            </div>

            <div class="form-group mb-2 col">
                <label for="day">Day</label>
                <select name="day" id="day" class="form-control mt-1" wire:model.defer="day"
                    {{ $showDay === false ? 'disabled' : '' }}>
                    <option value="">Select the day ...</option>
                    @foreach(\App\Helpers\Date::days() as $key => $dayVal)
                        <option value="{{ $key }}" @if($key === $day) selected @endif>{{ $dayVal }}</option>
                    @endforeach
                </select>
                @error('day') <span class="text-danger">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="row mt-3">
            <div class="form-group mb-2 col">
                <label for="body" class="sr-only">Reminder Body</label>
                <input type="text" class="form-control mt-1" id="body" placeholder="Body"
                       wire:model.defer="body">
                @error('body') <span class="text-danger">{{ $message }}</span>@enderror
            </div>
            <div class="form-group mb-2 col">
                <label for="additional_text" class="sr-only">Additional Text</label>
                <input type="text" class="form-control mt-1" id="additional_text" placeholder="Body"
                       wire:model.defer="additional_text">
                @error('additional_text') <span class="text-danger">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="row mt-3">
            <div class="form-group mb-2 col">
                <label for="date" class="sr-only">Date</label>
                <select name="date" id="date" class="form-control mt-1" wire:model.defer="date"
                    {{ $showDate === false ? 'disabled' : '' }}>
                    <option value="">{{ $date }}</option>
                    @foreach(range(1,31) as $val)
                        <option value="{{ $val }}" @if(\App\Helpers\Date::ordinal($val) == $date) selected @endif >
                            {{ \App\Helpers\Date::ordinal($val) }}
                        </option>
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
            <div class="form-group mb-2 col">
                <label for="active" class="sr-only">Status (Activate | Deactivate)</label>
                <select name="active" id="active" class="form-control mt-1" wire:model.defer="active">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
                @error('active') <span class="text-danger">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="align-content-center">
            <button class="btn btn-lg btn-outline-info btn-toolbar mb-2">Update Reminder</button>
        </div>
    </form>
</div>
