<div class="message-wrapper">
    <ul class="messages">
        @foreach($messages as $row)
        <li class="message clearfix">
            <div class="{{ $row->from==Auth::id() ? 'sent':'receive' }}">
                <p>{{ $row->message }}</p>
                <p class="date">{{ date('d M y: h:i a',strtotime($row->created_at)) }}</p>
            </div>
        </li>
        @endforeach
    </ul>
</div>
<div class="input-text">
    <input type="text" name="message" class="submit">
</div>