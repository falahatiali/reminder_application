<div class="mt-4">
    <div class="p-4 pl-5">
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                <strong>Sorry!</strong> There are some errors.<br><br>
                <ul style="list-style-type:none;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        @if($updateMode)
            @include('livewire.reminder.update')
        @else
            @include('livewire.reminder.create')
        @endif


        @if(count($reminders))
            <table class="table center table-responsive table-bordered table-striped">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Front of Card</th>
                    <th scope="col">Back of Card</th>
                    <th scope="col">Reminder Body</th>
                    <th scope="col">Frequency</th>
                    <th scope="col">Day</th>
                    <th scope="col">Date</th>
                    <th scope="col">Time</th>
                    <th scope="col">Run Once?</th>
                    <th scope="col">Active Or not</th>
                    <th scope="col">&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                @foreach($reminders as $reminder)
                    <tr>
                        <th scope="row">{{ $reminder->id }}</th>
                        <td>{{ $reminder->frontend }}</td>
                        <td>{{ $reminder->backend }}</td>
                        <td>{{ $reminder->body }}</td>
                        <td>{{ $reminder->frequency }}</td>
                        <td>{{ $reminder->day ?: '-' }}</td>
                        <td>{{ $reminder->date ?: '-' }}</td>
                        <td>{{ $reminder->time }}</td>
                        <td>{{ $reminder->run_once ? 'Yes' : 'No'}}</td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input" wire:click="ChangeStatus({{ $reminder->id }})"
                                       value="{{ $reminder->id }}" type="checkbox"
                                       role="switch" {{ $reminder->active == true ? 'checked' :'' }}>
                                <label class="form-check-label" for="flexSwitchCheckDefault"></label>
                            </div>
                        </td>
                        <td>
                            <button wire:click="edit({{ $reminder->id }})" class="btn btn-primary btn-sm">Edit</button>
                            <button wire:click="delete({{ $reminder->id }})" class="btn btn-danger btn-sm">Delete
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <p>You have no reminders.</p>
        @endif

    </div>
</div>
