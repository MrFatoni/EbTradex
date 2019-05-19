<form action="{{route('testpost')}}" method="post">
    {{csrf_field()}}
    <input type="hidden" name="base_key" value="{{base_key()}}">
    <input type="text" name="{{fake_field('users.username')}}" value="{{old(old_fake_field('users.username'))}}">
    <br>
    {{old_fake_field('users.username')}}
    <br>
    <input type="submit" value="test">
</form>