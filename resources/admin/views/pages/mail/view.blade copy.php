<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CFOCN</title>
</head>

<body>
    @foreach ($data as $f)
        <img src="{{$f}}" />
        <p>8888</p>
        <a href="{{ $f }}">Click PDF</a>
    @endforeach

</body>

</html>
