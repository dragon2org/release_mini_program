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
<a href="{{$uri}}">授权</a>
<button onclick="window.location.href='{{$uri}}'">授权跳转测试,js直接跳转有问题。</button>
</body>
</html>
<script>
    setTimeout(function(){
        window.location.href='{{$uri}}';
    }, 5000)
</script>