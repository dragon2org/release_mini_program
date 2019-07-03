<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>跳转中...</title>
</head>
<body>
<a href="{{$uri}}" id="btn-auth" style="display:none">授权</a>
</body>
</html>
<script>
    setTimeout(function(){
        document.getElementById('btn-auth').click();
        {{--window.location.href='{{$uri}}';--}}
    }, 500)
</script>