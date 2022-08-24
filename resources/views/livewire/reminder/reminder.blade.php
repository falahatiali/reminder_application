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
                    <th scope="col">Reminder</th>
                    <th scope="col">Frequency</th>
                    <th scope="col">Day</th>
                    <th scope="col">Date</th>
                    <th scope="col">Time</th>
                    <th scope="col">Run Once?</th>
                    <th scope="col">&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                @foreach($reminders as $reminder)
                    <tr>
                        <th scope="row">{{ $reminder->id }}</th>
                        <td>{{ $reminder->body }}</td>
                        <td>{{ $reminder->frequency }}</td>
                        <td>{{ $reminder->day ?: '-' }}</td>
                        <td>{{ $reminder->date ?: '-' }}</td>
                        <td>{{ $reminder->time }}</td>
                        <td>{{ $reminder->run_once ? 'Yes' : 'No'}}</td>
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
